
<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once 'conexao.php';

function responder($sucesso, $mensagem, $status = 200) {
    http_response_code($status);
    echo json_encode([
        'sucesso' => $sucesso,
        'mensagem' => $mensagem
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método não permitido.', 405);
}

$nome = trim($_POST['name'] ?? '');
$matricula = trim($_POST['registration'] ?? '');
$telefone = trim($_POST['phone'] ?? '');
$senha = $_POST['password'] ?? '';
$confirmarSenha = $_POST['confirm_password'] ?? '';
$nomeTurma = trim($_POST['school_class'] ?? '');

if (
    $nome === '' ||
    $matricula === '' ||
    $telefone === '' ||
    $senha === '' ||
    $confirmarSenha === '' ||
    $nomeTurma === ''
) {
    responder(false, 'Preencha todos os campos.');
}

if ($senha !== $confirmarSenha) {
    responder(false, 'As senhas não coincidem.');
}

if (strlen($senha) < 8) {
    responder(false, 'A senha deve ter pelo menos 8 caracteres.');
}

/* Verifica se a matrícula já está cadastrada */
$sql = "SELECT id_usuario
        FROM USUARIO
        WHERE matricula = ?
        LIMIT 1";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    error_log('Erro ao preparar consulta de matrícula: ' . $conexao->error);
    responder(false, 'Erro interno ao verificar a matrícula.', 500);
}

$stmt->bind_param('s', $matricula);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $stmt->close();
    responder(false, 'Esta matrícula já está cadastrada.');
}

$stmt->close();

/* Busca a turma selecionada */
$sqlTurma = "SELECT id_turma
             FROM TURMA
             WHERE nome_turma = ?
             LIMIT 1";

$stmtTurma = $conexao->prepare($sqlTurma);

if (!$stmtTurma) {
    error_log('Erro ao preparar consulta de turma: ' . $conexao->error);
    responder(false, 'Erro interno ao consultar a turma.', 500);
}

$stmtTurma->bind_param('s', $nomeTurma);
$stmtTurma->execute();
$resultadoTurma = $stmtTurma->get_result();

if ($resultadoTurma->num_rows === 0) {
    $stmtTurma->close();

    error_log('Turma não encontrada no cadastro: ' . $nomeTurma);

    responder(
        false,
        'Turma não encontrada. Confira se o nome da turma no banco é igual ao selecionado.'
    );
}

$turma = $resultadoTurma->fetch_assoc();
$idTurma = (int) $turma['id_turma'];
$stmtTurma->close();

/* Prepara os dados do usuário */
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

/* Gera automaticamente o e-mail institucional */
$email = $matricula . '@estudante.rn.gov.br';

$status = 'Aluno';

/* Insere o usuário */
$sqlInsert = "INSERT INTO USUARIO
              (status, nome, matricula, email, telefone, id_turma, senha)
              VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmtInsert = $conexao->prepare($sqlInsert);

if (!$stmtInsert) {
    error_log('Erro ao preparar cadastro: ' . $conexao->error);
    responder(false, 'Erro interno ao preparar o cadastro.', 500);
}

$stmtInsert->bind_param(
    'sssssis',
    $status,
    $nome,
    $matricula,
    $email,
    $telefone,
    $idTurma,
    $senhaHash
);

if (!$stmtInsert->execute()) {
    error_log('Erro ao cadastrar usuário: ' . $stmtInsert->error);
    $stmtInsert->close();

    responder(false, 'Não foi possível concluir o cadastro.', 500);
}

$stmtInsert->close();

responder(true, 'Cadastro realizado com sucesso!');
?>
