<?php
// API de inventário e empréstimos de Chromebooks.
// A identidade do usuário vem exclusivamente da sessão autenticada do PHP.

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexao.php';

function responder($sucesso, $mensagem = '', $dados = null, $statusHttp = 200) {
    http_response_code($statusHttp);
    $resposta = ['sucesso' => $sucesso, 'mensagem' => $mensagem];
    if ($dados !== null) {
        $resposta = array_merge($resposta, $dados);
    }
    echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
    exit;
}

function exigirLogin() {
    $id = (int)($_SESSION['id_usuario'] ?? 0);
    $tipo = (string)($_SESSION['status'] ?? '');
    if ($id <= 0 || !in_array($tipo, ['Aluno', 'Professor', 'Administrador'], true)) {
        responder(false, 'Sua sessão expirou. Entre novamente no portal.', null, 401);
    }
    return ['id' => $id, 'tipo' => $tipo, 'nome' => (string)($_SESSION['nome'] ?? '')];
}

function exigirAdministrador($usuario) {
    if ($usuario['tipo'] !== 'Administrador') {
        responder(false, 'Apenas administradores podem executar esta ação.', null, 403);
    }
}

$usuarioSessao = exigirLogin();
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) $input = [];
$acao = (string)($input['acao'] ?? $_POST['acao'] ?? '');

if ($metodo === 'GET') {
    $sql = "SELECT c.id_chromebook AS id,
                   c.patrimonio,
                   c.modelo,
                   c.status,
                   e.id_usuario,
                   e.status AS status_emprestimo,
                   u.nome AS aluno_atual
            FROM CHROMEBOOK c
            LEFT JOIN EMPRESTIMO e
              ON e.id_chromebook = c.id_chromebook
             AND e.status IN ('Ativo', 'Devolução solicitada')
             AND e.data_devolucao IS NULL
            LEFT JOIN USUARIO u ON u.id_usuario = e.id_usuario
            ORDER BY c.id_chromebook ASC";

    $resultado = $conexao->query($sql);
    if (!$resultado) {
        error_log('Erro ao consultar inventário: ' . $conexao->error);
        responder(false, 'Não foi possível carregar o inventário.', null, 500);
    }

    $chromebooks = [];
    while ($row = $resultado->fetch_assoc()) {
        $id = (int)$row['id'];
        $patrimonio = $row['patrimonio'] ?? '';
        $chromebooks[] = [
            'id' => $id,
            'patrimonio' => $patrimonio !== '' ? $patrimonio : ('CEEP-' . $id),
            'modelo' => $row['modelo'] ?? 'Padrão',
            'status' => $row['status'] ?? 'available',
            'id_usuario' => $row['id_usuario'] !== null ? (int)$row['id_usuario'] : null,
            'status_emprestimo' => $row['status_emprestimo'] ?? null,
            'aluno_atual' => $row['aluno_atual'],
            'name' => $patrimonio !== '' ? $patrimonio : ('Chromebook ' . $id)
        ];
    }

    responder(true, '', [
        'dados' => $chromebooks,
        'usuario' => [
            'id_usuario' => $usuarioSessao['id'],
            'nome' => $usuarioSessao['nome'],
            'status' => $usuarioSessao['tipo']
        ]
    ]);
}

if ($metodo !== 'POST') {
    responder(false, 'Método não permitido.', null, 405);
}

$idChromebook = (int)($input['id_chromebook'] ?? $input['id'] ?? $_POST['id_chromebook'] ?? $_POST['id'] ?? 0);

// Solicitação de empréstimo feita por aluno autenticado.
if ($acao === 'solicitar_emprestimo' || ($acao === 'atualizar_status' && ($input['status'] ?? '') === 'in-use')) {
    if ($usuarioSessao['tipo'] !== 'Aluno') {
        responder(false, 'Somente alunos podem solicitar empréstimos por esta tela.', null, 403);
    }
    if ($idChromebook <= 0) responder(false, 'Chromebook inválido.', null, 400);

    $periodo = trim((string)($input['periodo'] ?? ''));
    $periodosValidos = ['1 aula', '2 aulas', 'Período da manhã', 'Período da tarde'];
    if (!in_array($periodo, $periodosValidos, true)) {
        responder(false, 'Selecione um período de uso válido.', null, 400);
    }

    $conexao->begin_transaction();
    try {
        $stmt = $conexao->prepare("SELECT status FROM CHROMEBOOK WHERE id_chromebook = ? FOR UPDATE");
        if (!$stmt) throw new Exception('Não foi possível verificar o equipamento.');
        $stmt->bind_param('i', $idChromebook);
        $stmt->execute();
        $equipamento = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$equipamento) throw new Exception('Chromebook não encontrado.');
        if ($equipamento['status'] !== 'available') throw new Exception('Este Chromebook não está mais disponível. Atualize a página e tente outro.');

        $stmt = $conexao->prepare("SELECT id_emprestimo FROM EMPRESTIMO WHERE id_chromebook = ? AND status = 'Ativo' AND data_devolucao IS NULL LIMIT 1 FOR UPDATE");
        if (!$stmt) throw new Exception('Não foi possível verificar os empréstimos ativos.');
        $stmt->bind_param('i', $idChromebook);
        $stmt->execute();
        $emprestimoExistente = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($emprestimoExistente) throw new Exception('Este Chromebook já possui um empréstimo ativo.');

        $stmt = $conexao->prepare("INSERT INTO EMPRESTIMO (id_usuario, id_chromebook, data_emprestimo, data_prevista_devolucao, data_devolucao, status) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 DAY), NULL, 'Ativo')");
        if (!$stmt) throw new Exception('Não foi possível registrar o empréstimo.');
        $stmt->bind_param('ii', $usuarioSessao['id'], $idChromebook);
        if (!$stmt->execute()) throw new Exception('Erro ao salvar o empréstimo.');
        $idEmprestimo = $stmt->insert_id;
        $stmt->close();

        $stmt = $conexao->prepare("UPDATE CHROMEBOOK SET status = 'in-use' WHERE id_chromebook = ? AND status = 'available'");
        if (!$stmt) throw new Exception('Não foi possível atualizar a disponibilidade.');
        $stmt->bind_param('i', $idChromebook);
        if (!$stmt->execute() || $stmt->affected_rows !== 1) throw new Exception('O equipamento mudou de disponibilidade. Atualize a página e tente novamente.');
        $stmt->close();

        $conexao->commit();
        responder(true, 'Empréstimo registrado com sucesso!', ['id_emprestimo' => $idEmprestimo]);
    } catch (Throwable $e) {
        $conexao->rollback();
        responder(false, $e->getMessage(), null, 400);
    }
}

// Cancelamento de reserva / liberação: permitido para Professor e Administrador.
$novoStatusPost = (string)($input['status'] ?? $_POST['status'] ?? '');
if (in_array($acao, ['cancelar_reserva', 'cancelar'], true) || ($novoStatusPost === 'available' && in_array($usuarioSessao['tipo'], ['Professor', 'Administrador'], true))) {
    if (!in_array($usuarioSessao['tipo'], ['Professor', 'Administrador'], true)) {
        responder(false, 'Apenas professores ou administradores podem cancelar reservas.', null, 403);
    }
    if ($idChromebook <= 0) responder(false, 'Chromebook inválido.', null, 400);

    $conexao->begin_transaction();
    try {
        $stmt = $conexao->prepare("UPDATE EMPRESTIMO SET status = 'Cancelado', data_devolucao = CURDATE() WHERE id_chromebook = ? AND status = 'Ativo' AND data_devolucao IS NULL");
        if (!$stmt) throw new Exception('Não foi possível cancelar o empréstimo ativo.');
        $stmt->bind_param('i', $idChromebook);
        if (!$stmt->execute()) throw new Exception('Não foi possível atualizar o registro de empréstimo.');
        $stmt->close();

        $stmt = $conexao->prepare("UPDATE CHROMEBOOK SET status = 'available' WHERE id_chromebook = ?");
        if (!$stmt) throw new Exception('Não foi possível liberar o Chromebook.');
        $stmt->bind_param('i', $idChromebook);
        if (!$stmt->execute()) throw new Exception('Não foi possível atualizar o status do Chromebook.');
        $stmt->close();

        $conexao->commit();
        responder(true, 'Reserva cancelada e Chromebook liberado!');
    } catch (Throwable $e) {
        $conexao->rollback();
        responder(false, $e->getMessage(), null, 400);
    }
}

// Devolução: o aluno só pode devolver o próprio empréstimo; administrador pode devolver qualquer um.
if ($acao === 'devolver') {
    if ($usuarioSessao['tipo'] !== 'Administrador') {
        responder(false, 'A devolução precisa ser solicitada e confirmada por um administrador.', null, 403);
    }
    if ($idChromebook <= 0) responder(false, 'Chromebook inválido.', null, 400);

    $conexao->begin_transaction();
    try {
        if ($usuarioSessao['tipo'] === 'Administrador') {
            $sqlBusca = "SELECT id_emprestimo FROM EMPRESTIMO WHERE id_chromebook = ? AND status = 'Ativo' AND data_devolucao IS NULL LIMIT 1 FOR UPDATE";
            $stmt = $conexao->prepare($sqlBusca);
            $stmt->bind_param('i', $idChromebook);
        } else {
            if ($usuarioSessao['tipo'] !== 'Aluno') throw new Exception('Seu perfil não pode devolver este equipamento.');
            $sqlBusca = "SELECT id_emprestimo FROM EMPRESTIMO WHERE id_chromebook = ? AND id_usuario = ? AND status = 'Ativo' AND data_devolucao IS NULL LIMIT 1 FOR UPDATE";
            $stmt = $conexao->prepare($sqlBusca);
            $stmt->bind_param('ii', $idChromebook, $usuarioSessao['id']);
        }
        if (!$stmt) throw new Exception('Não foi possível localizar o empréstimo.');
        $stmt->execute();
        $emprestimo = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$emprestimo) throw new Exception('Não foi encontrado empréstimo ativo deste Chromebook para sua conta.');

        $idEmprestimo = (int)$emprestimo['id_emprestimo'];
        $stmt = $conexao->prepare("UPDATE EMPRESTIMO SET status = 'Devolvido', data_devolucao = CURDATE() WHERE id_emprestimo = ? AND status = 'Ativo' AND data_devolucao IS NULL");
        if (!$stmt) throw new Exception('Não foi possível registrar a devolução.');
        $stmt->bind_param('i', $idEmprestimo);
        if (!$stmt->execute() || $stmt->affected_rows !== 1) throw new Exception('Não foi possível concluir a devolução.');
        $stmt->close();

        $stmt = $conexao->prepare("UPDATE CHROMEBOOK SET status = 'available' WHERE id_chromebook = ?");
        if (!$stmt) throw new Exception('Não foi possível liberar o Chromebook.');
        $stmt->bind_param('i', $idChromebook);
        if (!$stmt->execute()) throw new Exception('Não foi possível liberar o Chromebook.');
        $stmt->close();

        $conexao->commit();
        responder(true, 'Equipamento devolvido com sucesso!');
    } catch (Throwable $e) {
        $conexao->rollback();
        responder(false, $e->getMessage(), null, 400);
    }
}

// As demais operações são administrativas.
exigirAdministrador($usuarioSessao);

if ($acao === 'remover') {
    if ($idChromebook <= 0) responder(false, 'Chromebook inválido.', null, 400);
    $stmt = $conexao->prepare("SELECT id_emprestimo FROM EMPRESTIMO WHERE id_chromebook = ? AND status = 'Ativo' AND data_devolucao IS NULL LIMIT 1");
    $stmt->bind_param('i', $idChromebook);
    $stmt->execute();
    $ativo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($ativo) responder(false, 'Não é possível remover um Chromebook com empréstimo ativo.', null, 409);

    $stmt = $conexao->prepare('DELETE FROM CHROMEBOOK WHERE id_chromebook = ?');
    $stmt->bind_param('i', $idChromebook);
    if ($stmt->execute() && $stmt->affected_rows === 1) responder(true, 'Chromebook removido com sucesso!');
    responder(false, 'Não foi possível remover o Chromebook. Ele pode possuir registros vinculados.', null, 409);
}

if ($acao === 'cadastrar') {
    $patrimonio = trim((string)($input['patrimonio'] ?? $_POST['patrimonio'] ?? ''));
    $modelo = trim((string)($input['modelo'] ?? $_POST['modelo'] ?? 'Padrão'));
    $status = trim((string)($input['status'] ?? $_POST['status'] ?? 'available'));
    if ($patrimonio === '') responder(false, 'O patrimônio é obrigatório.', null, 400);
    if (!in_array($status, ['available', 'unavailable'], true)) {
        responder(false, 'Novos equipamentos devem ser cadastrados como disponíveis ou indisponíveis.', null, 400);
    }
    $stmt = $conexao->prepare('INSERT INTO CHROMEBOOK (patrimonio, modelo, status) VALUES (?, ?, ?)');
    if (!$stmt) responder(false, 'Não foi possível preparar o cadastro.', null, 500);
    $stmt->bind_param('sss', $patrimonio, $modelo, $status);
    if ($stmt->execute()) responder(true, 'Chromebook cadastrado com sucesso!', ['id' => $stmt->insert_id]);
    error_log('Erro ao cadastrar Chromebook: ' . $stmt->error);
    responder(false, 'Não foi possível cadastrar o Chromebook.', null, 500);
}

// A tela administrativa pode liberar um equipamento (registrando a devolução) ou marcá-lo indisponível.
$novoStatus = (string)($input['status'] ?? $_POST['status'] ?? '');
if ($idChromebook > 0 && in_array($novoStatus, ['available', 'unavailable'], true)) {
    $conexao->begin_transaction();
    try {
        if ($novoStatus === 'unavailable') {
            $stmt = $conexao->prepare("SELECT id_emprestimo FROM EMPRESTIMO WHERE id_chromebook = ? AND status = 'Ativo' AND data_devolucao IS NULL LIMIT 1 FOR UPDATE");
            $stmt->bind_param('i', $idChromebook);
            $stmt->execute();
            $ativo = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($ativo) throw new Exception('Este Chromebook possui empréstimo ativo. Registre a devolução antes de marcá-lo como indisponível.');
        } else {
            $stmt = $conexao->prepare("UPDATE EMPRESTIMO SET status = 'Devolvido', data_devolucao = CURDATE() WHERE id_chromebook = ? AND status = 'Ativo' AND data_devolucao IS NULL");
            $stmt->bind_param('i', $idChromebook);
            if (!$stmt->execute()) throw new Exception('Não foi possível encerrar o empréstimo ativo.');
            $stmt->close();
        }
        $stmt = $conexao->prepare('UPDATE CHROMEBOOK SET status = ? WHERE id_chromebook = ?');
        $stmt->bind_param('si', $novoStatus, $idChromebook);
        if (!$stmt->execute()) throw new Exception('Não foi possível atualizar o status.');
        $stmt->close();
        $conexao->commit();
        responder(true, 'Status atualizado com sucesso!');
    } catch (Throwable $e) {
        $conexao->rollback();
        responder(false, $e->getMessage(), null, 400);
    }
}

responder(false, 'Ação ou parâmetros inválidos.', null, 400);
?>