<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

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

// Recebe os dados do formulário
$nome = trim($_POST['name'] ?? '');
$matricula = trim($_POST['registration'] ?? '');
$telefone = trim($_POST['phone'] ?? '');
$senha = $_POST['password'] ?? '';
$confirmarSenha = $_POST['confirm_password'] ?? '';
$senhaAutorizacao = $_POST['admin_auth_password'] ?? '';

// Validações básicas
if ($nome === '' || $matricula === '' || $telefone === '' || $senha === '' || $senhaAutorizacao === '') {
    responder(false, 'Preencha todos os campos obrigatórios, incluindo a senha de autorização do administrador.', 400);
}

if (strlen($senha) < 8) {
    responder(false, 'A senha deve ter no mínimo 8 caracteres.', 400);
}

if ($senha !== $confirmarSenha) {
    responder(false, 'As senhas informadas não coincidem.', 400);
}

// ETAPA DE SEGURANÇA: Verificar se existe pelo menos um Administrador com a senha informada
$sqlBuscaAdm = "SELECT senha FROM USUARIO WHERE status = 'Administrador'";
$resultadoAdm = $conexao->query($sqlBuscaAdm);

$autorizado = false;

if ($resultadoAdm && $resultadoAdm->num_rows > 0) {
    while ($adm = $resultadoAdm->fetch_assoc()) {
        if (password_verify($senhaAutorizacao, $adm['senha'])) {
            $autorizado = true;
            break;
        }
    }
}

if (!$autorizado) {
    responder(false, 'Senha de autorização inválida. Apenas administradores cadastrados podem autorizar novos registros.', 403);
}

// Verifica se a matrícula já está cadastrada
$sqlCheck = "SELECT id_usuario FROM USUARIO WHERE matricula = ? LIMIT 1";
$stmtCheck = $conexao->prepare($sqlCheck);

if (!$stmtCheck) {
    responder(false, 'Erro interno no servidor.', 500);
}

$stmtCheck->bind_param('s', $matricula);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    $stmtCheck->close();
    responder(false, 'Esta matrícula já está cadastrada no sistema.', 400);
}
$stmtCheck->close();

// Busca o ID da turma 'Administrador' no banco de dados para garantir integridade relacional
$sqlTurma = "SELECT id_turma FROM TURMA WHERE nome_turma = 'Administrador' LIMIT 1";
$resultadoTurma = $conexao->query($sqlTurma);

$idTurmaAdm = null;
if ($resultadoTurma && $resultadoTurma->num_rows > 0) {
    $turma = $resultadoTurma->fetch_assoc();
    $idTurmaAdm = $turma['id_turma'];
} else {
    // Caso a turma não exista por extenso na tabela TURMA, insere de forma preventiva ou usa ID fixo se aplicável
    $sqlInsereTurma = "INSERT INTO TURMA (nome_turma) VALUES ('Administrador')";
    if ($conexao->query($sqlInsereTurma)) {
        $idTurmaAdm = $conexao->insert_id;
    } else {
        responder(false, 'Erro ao configurar a turma administrativa no banco de dados.', 500);
    }
}

// Hash seguro da senha do novo administrador
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
$statusAdm = 'Administrador';

// Insere o novo administrador na tabela USUARIO centralizada
$sqlInsert = "INSERT INTO USUARIO (nome, matricula, telefone, senha, status, id_turma) VALUES (?, ?, ?, ?, ?, ?)";
$stmtInsert = $conexao->prepare($sqlInsert);

if (!$stmtInsert) {
    responder(false, 'Erro ao preparar o cadastro.', 500);
}

$stmtInsert->bind_param('sssssi', $nome, $matricula, $telefone, $senhaHash, $statusAdm, $idTurmaAdm);

if ($stmtInsert->execute()) {
    $stmtInsert->close();
    responder(true, 'Administrador cadastrado com sucesso.');
} else {
    $stmtInsert->close();
    responder(false, 'Erro ao registrar o administrador no banco de dados.', 500);
}
?>