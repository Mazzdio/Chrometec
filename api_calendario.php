<?php
require_once __DIR__ . '/auth.php';
iniciar_sessao_segura();
header('Content-Type: application/json; charset=utf-8');

if (!usuario_autenticado()) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão encerrada.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$perfil = $_SESSION['status'] ?? '';
if (!in_array($perfil, ['Professor', 'Administrador'], true)) {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso restrito.'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/conexao.php';
$conexao->set_charset('utf8mb4');

$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$idUsuario = (int)($_SESSION['id_usuario'] ?? 0);

if ($metodo === 'GET') {
    $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('n');
    $ano = isset($_GET['ano']) ? (int)$_GET['ano'] : (int)date('Y');

    if ($mes < 1 || $mes > 12) $mes = (int)date('n');
    if ($ano < 2000 || $ano > 2100) $ano = (int)date('Y');

    $inicio = sprintf('%04d-%02d-01', $ano, $mes);
    $fim = date('Y-m-t', strtotime($inicio));

    $stmt = $conexao->prepare(
        "SELECT e.id_evento, e.id_usuario, u.nome AS autor_nome,
                e.titulo, e.descricao, e.data_evento, e.hora_evento,
                e.local_evento, e.turma_alvo, e.categoria
         FROM EVENTO_CALENDARIO e
         LEFT JOIN USUARIO u ON u.id_usuario = e.id_usuario
         WHERE e.data_evento BETWEEN ? AND ?
         ORDER BY e.data_evento ASC, e.hora_evento ASC, e.id_evento ASC"
    );
    $stmt->bind_param('ss', $inicio, $fim);
    $stmt->execute();
    $res = $stmt->get_result();

    $eventos = [];
    while ($row = $res->fetch_assoc()) {
        $autor = trim((string)($row['autor_nome'] ?? ''));
        $eventos[] = [
            'id_evento' => (int)$row['id_evento'],
            'id_usuario' => $row['id_usuario'] !== null ? (int)$row['id_usuario'] : null,
            'autor_nome' => $autor !== '' ? $autor : 'Coordenação / Sistema',
            'titulo' => $row['titulo'],
            'descricao' => $row['descricao'] ?? '',
            'data_evento' => $row['data_evento'],
            'hora_evento' => $row['hora_evento'] ? substr($row['hora_evento'], 0, 5) : '',
            'local_evento' => $row['local_evento'] ?? '',
            'turma_alvo' => $row['turma_alvo'] ?? '',
            'categoria' => $row['categoria']
        ];
    }

    echo json_encode(['sucesso' => true, 'eventos' => $eventos], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($metodo === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $acao = $input['acao'] ?? 'criar';

    if ($acao === 'excluir') {
        $idEvento = (int)($input['id_evento'] ?? 0);
        if ($idEvento <= 0) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Evento inválido.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $stmt = $conexao->prepare("DELETE FROM EVENTO_CALENDARIO WHERE id_evento = ?");
        $stmt->bind_param('i', $idEvento);
        $ok = $stmt->execute();

        echo json_encode([
            'sucesso' => (bool)$ok,
            'mensagem' => $ok ? 'Evento removido.' : 'Erro ao remover evento.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $titulo = trim((string)($input['titulo'] ?? ''));
    $dataEvento = trim((string)($input['data_evento'] ?? ''));
    $horaEvento = trim((string)($input['hora_evento'] ?? ''));
    $localEvento = trim((string)($input['local_evento'] ?? ''));
    $turmaAlvo = trim((string)($input['turma_alvo'] ?? ''));
    $descricao = trim((string)($input['descricao'] ?? ''));
    $categoria = trim((string)($input['categoria'] ?? 'Escolar'));

    $categoriasValidas = ['Escolar', 'Aula', 'Avaliacao', 'Reserva'];
    if (!in_array($categoria, $categoriasValidas, true)) {
        $categoria = 'Escolar';
    }

    if ($titulo === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataEvento)) {
        http_response_code(400);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Informe título e uma data válida.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $horaSql = preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $horaEvento)
        ? (strlen($horaEvento) === 5 ? $horaEvento . ':00' : $horaEvento)
        : null;
    $localSql = $localEvento !== '' ? $localEvento : null;
    $turmaSql = $turmaAlvo !== '' ? $turmaAlvo : null;
    $descSql = $descricao !== '' ? $descricao : null;
    $usuarioSql = $idUsuario > 0 ? $idUsuario : null;

    $stmt = $conexao->prepare(
        "INSERT INTO EVENTO_CALENDARIO (id_usuario, titulo, descricao, data_evento, hora_evento, local_evento, turma_alvo, categoria)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        'isssssss',
        $usuarioSql,
        $titulo,
        $descSql,
        $dataEvento,
        $horaSql,
        $localSql,
        $turmaSql,
        $categoria
    );

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar evento no banco.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        'sucesso' => true,
        'id_evento' => (int)$conexao->insert_id,
        'mensagem' => 'Evento adicionado ao calendário.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(405);
echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);