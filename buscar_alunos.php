<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

require_once 'conexao.php';

// Opcional: valida se é administrador acessando
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'Administrador') {
    http_response_code(403);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Acesso negado.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Consulta unindo USUARIO e TURMA usando as chaves estrangeiras corretas do seu banco
$sql = "SELECT u.id_usuario, u.nome, u.matricula, u.email, u.telefone, t.nome_turma, u.status 
        FROM USUARIO u 
        LEFT JOIN TURMA t ON u.id_turma = t.id_turma 
        WHERE u.status = 'Aluno' 
        ORDER BY u.nome ASC";

$resultado = $conexao->query($sql);

if (!$resultado) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao consultar o banco de dados: ' . $conexao->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$alunos = [];

while ($row = $resultado->fetch_assoc()) {
    $alunos[] = [
        'id' => $row['id_usuario'],
        'name' => $row['nome'],
        'email' => $row['email'] ?: 'E-mail não cadastrado',
        'registration' => $row['matricula'],
        'phone' => $row['telefone'] ?: 'Telefone não informado',
        'className' => $row['nome_turma'] ?? 'Não informada',
        'status' => 'Ativo' // Como no seu banco o status é 'Aluno', exibimos como Ativo no painel
    ];
}

// Retorna o JSON com a lista exata de alunos cadastrados
echo json_encode([
    'sucesso' => true,
    'alunos' => $alunos
], JSON_UNESCAPED_UNICODE);
?>