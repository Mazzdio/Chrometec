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

if (!$id) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID do professor não informado.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conexao->prepare("DELETE FROM USUARIO WHERE id_usuario = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Professor excluído com sucesso!'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir do banco: ' . $stmt->error], JSON_UNESCAPED_UNICODE);
}
$stmt->close();
?>