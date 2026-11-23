<?php
require_once __DIR__ . '/auth.php';
iniciar_sessao_segura();
exigir_administrador();
header('Content-Type: application/json; charset=utf-8');

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (empty($input)) {
    $input = $_POST;
}

$id_usuario   = intval($input['id'] ?? 0);
$nome         = trim($input['name'] ?? '');
$matricula    = trim($input['registration'] ?? '');
$telefone     = trim($input['phone'] ?? '');
$email        = trim($input['email'] ?? '');
$senha        = trim($input['password'] ?? '');

if ($id_usuario <= 0 || empty($nome) || empty($email)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID, nome e e-mail são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Verifica se uma nova senha foi preenchida
if (!empty($senha)) {
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $sql = "UPDATE USUARIO SET nome = ?, matricula = ?, telefone = ?, email = ?, senha = ? WHERE id_usuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $matricula, $telefone, $email, $senhaHash, $id_usuario);
} else {
    $sql = "UPDATE USUARIO SET nome = ?, matricula = ?, telefone = ?, email = ? WHERE id_usuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssssi", $nome, $matricula, $telefone, $email, $id_usuario);
}

if ($stmt && $stmt->execute()) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Administrador atualizado com sucesso!'], JSON_UNESCAPED_UNICODE);
} else {
    $erro = $stmt ? $stmt->error : $conexao->error;
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar: ' . $erro], JSON_UNESCAPED_UNICODE);
}

if ($stmt) $stmt->close();
exit;
?>