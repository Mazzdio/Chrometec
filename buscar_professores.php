<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'conexao.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'Administrador') {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "SELECT u.id_usuario, u.nome, u.matricula, u.email, u.telefone, t.nome_turma, u.status 
        FROM USUARIO u 
        LEFT JOIN TURMA t ON u.id_turma = t.id_turma 
        WHERE u.status = 'Professor' OR t.nome_turma IN ('Redes de Computadores', 'Informática', 'Base Nacional Comum Curricular')
        ORDER BY u.nome ASC";

$resultado = $conexao->query($sql);

if (!$resultado) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao consultar banco.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$professores = [];
while ($row = $resultado->fetch_assoc()) {
    $professores[] = [
        'id' => $row['id_usuario'],
        'name' => $row['nome'],
        'email' => $row['email'] ?: 'E-mail não cadastrado',
        'registration' => $row['matricula'],
        'phone' => $row['telefone'] ?: 'Telefone não informado',
        'department' => $row['nome_turma'] ?: 'Informática',
        'discipline' => 'Docência Geral',
        'status' => 'Ativo'
    ];
}

echo json_encode(['sucesso' => true, 'professores' => $professores], JSON_UNESCAPED_UNICODE);
?>