<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'conexao.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'Administrador') {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$id = $_POST['id'] ?? '';
$nome = trim($_POST['name'] ?? '');
$matricula = trim($_POST['registration'] ?? '');
$telefone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$nome_turma = trim($_POST['className'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($nome === '' || $matricula === '') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha os campos obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Busca o id_turma correspondente ao nome da turma selecionada
$stmtTurma = $conexao->prepare("SELECT id_turma FROM TURMA WHERE nome_turma = ? LIMIT 1");
$stmtTurma->bind_param("s", $nome_turma);
$stmtTurma->execute();
$resTurma = $stmtTurma->get_result();
$turma = $resTurma->fetch_assoc();
$id_turma = $turma ? $turma['id_turma'] : 11; // Padrão se não achar
$stmtTurma->close();

if ($id) {
    // Atualizar aluno existente
    $sql = "UPDATE USUARIO SET nome = ?, matricula = ?, telefone = ?, email = ?, id_turma = ? WHERE id_usuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssssii", $nome, $matricula, $telefone, $email, $id_turma, $id);
} else {
    // Inserir novo aluno
    $senhaHash = password_hash($senha ?: '123456', PASSWORD_DEFAULT);
    $status = 'Aluno';
    $sql = "INSERT INTO USUARIO (status, nome, matricula, email, telefone, id_turma, senha) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssssis", $status, $nome, $matricula, $email, $telefone, $id_turma, $senhaHash);
}

if ($stmt->execute()) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Aluno salvo com sucesso!'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar no banco: ' . $stmt->error], JSON_UNESCAPED_UNICODE);
}
$stmt->close();
?>