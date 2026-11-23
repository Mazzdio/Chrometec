
<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['status']) ||
    $_SESSION['status'] !== 'Aluno'
) {
    http_response_code(401);

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Sessão inválida. Faça login novamente.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

echo json_encode([
    'sucesso' => true,
    'nome' => $_SESSION['nome'],
    'matricula' => $_SESSION['matricula']
], JSON_UNESCAPED_UNICODE);
