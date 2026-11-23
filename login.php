<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

require_once 'conexao.php';

function responder($sucesso, $mensagem, $status = 200, $redirecionar = null, $usuario = null) {
    http_response_code($status);

    echo json_encode([
        'sucesso' => $sucesso,
        'mensagem' => $mensagem,
        'redirecionar' => $redirecionar,
        'usuario' => $usuario
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método não permitido.', 405);
}

$matricula = trim($_POST['matricula'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($matricula === '' || $senha === '') {
    responder(false, 'Informe a matrícula e a senha.', 400);
}

$sql = "SELECT id_usuario, nome, matricula, senha, status
        FROM USUARIO
        WHERE matricula = ?
        LIMIT 1";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    error_log('Erro ao preparar login: ' . $conexao->error);
    responder(false, 'Erro interno no servidor.', 500);
}

$stmt->bind_param('s', $matricula);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

$stmt->close();

if (!$usuario || !password_verify($senha, $usuario['senha'])) {
    responder(false, 'Matrícula ou senha incorreta.', 401);
}

$destinos = [
    'Aluno' => 'acessoaluno.php',
    'Professor' => 'acessoprofessor.php',
    'Administrador' => 'emprestimo_adm.php'
];

if (!isset($destinos[$usuario['status']])) {
    responder(false, 'Tipo de usuário inválido.', 403);
}

session_regenerate_id(true);

$_SESSION['id_usuario'] = $usuario['id_usuario'];
$_SESSION['nome'] = $usuario['nome'];
$_SESSION['matricula'] = $usuario['matricula'];
$_SESSION['status'] = $usuario['status'];

responder(
    true,
    'Login realizado com sucesso.',
    200,
    $destinos[$usuario['status']],
    [
        'nome' => $usuario['nome'],
        'matricula' => $usuario['matricula'],
        'status' => $usuario['status']
    ]
);
?>