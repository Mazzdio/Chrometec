<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'conexao.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'Administrador') {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Busca usuários com status 'Administrador' ou ligados à turma 'Administrador'
$sql = "SELECT u.id_usuario, u.nome, u.matricula, u.email, u.telefone, t.nome_turma, u.status 
        FROM USUARIO u 
        LEFT JOIN TURMA t ON u.id_turma = t.id_turma 
        WHERE u.status = 'Administrador' OR t.nome_turma = 'Administrador'
        ORDER BY u.nome ASC";

$resultado = $conexao->query($sql);

if (!$resultado) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao consultar o banco de dados.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$responsaveis = [];
while ($row = $resultado->fetch_assoc()) {
    $responsaveis[] = [
        'id' => $row['id_usuario'],
        'name' => $row['nome'],
        'email' => $row['email'] ?: 'E-mail não cadastrado',
        'registration' => $row['matricula'],
        'phone' => $row['telefone'] ?: 'Telefone não informado',
        'role' => $row['status'] ?: 'Administrador',
        'status' => 'Ativo'
    ];
}

echo json_encode(['sucesso' => true, 'responsaveis' => $responsaveis], JSON_UNESCAPED_UNICODE);
?>