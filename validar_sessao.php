<?php
require_once __DIR__ . '/auth.php';
iniciar_sessao_segura();
header('Content-Type: application/json; charset=utf-8');
if (!usuario_autenticado()) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'autenticado' => false], JSON_UNESCAPED_UNICODE);
    exit;
}
echo json_encode([
    'sucesso' => true,
    'autenticado' => true,
    'id_usuario' => (int) $_SESSION['id_usuario'],
    'nome' => $_SESSION['nome'] ?? '',
    'perfil' => $_SESSION['status']
], JSON_UNESCAPED_UNICODE);
?>