<?php
require_once __DIR__ . '/auth.php';
iniciar_sessao_segura();
$perfil = $_SESSION['status'] ?? '';
$login = [
    'Aluno' => 'login_aluno.html',
    'Professor' => 'login_professor.html',
    'Administrador' => 'login_adm.html'
][$perfil] ?? 'login_aluno.html';

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['sucesso' => true, 'redirecionar' => $login], JSON_UNESCAPED_UNICODE);
    exit;
}
header('Location: ' . $login, true, 303);
exit;
?>