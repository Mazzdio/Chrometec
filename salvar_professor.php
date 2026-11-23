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
$departamento = trim($_POST['department'] ?? '');
$senha = $_POST['senha'] ?? '';

if (!$nome || !$matricula || !$email || !$departamento) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Descobrir o id_turma correspondente ao departamento selecionado
$stmtTurma = $conexao->prepare("SELECT id_turma FROM TURMA WHERE nome_turma = ? LIMIT 1");
$stmtTurma->bind_param("s", $departamento);
$stmtTurma->execute();
$resTurma = $stmtTurma->get_result();

if ($resTurma->num_rows === 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Departamento inválido no banco de dados.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$rowTurma = $resTurma->fetch_assoc();
$id_turma = $rowTurma['id_turma'];
$stmtTurma->close();

if ($id) {
    // Atualizar professor existente (se preencheu senha, atualiza com hash; senão, mantém a antiga)
    if (!empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conexao->prepare("UPDATE USUARIO SET nome = ?, matricula = ?, telefone = ?, email = ?, senha = ?, id_turma = ? WHERE id_usuario = ?");
        $stmt->bind_param("sssssii", $nome, $matricula, $telefone, $email, $senhaHash, $id_turma, $id);
    } else {
        $stmt = $conexao->prepare("UPDATE USUARIO SET nome = ?, matricula = ?, telefone = ?, email = ?, id_turma = ? WHERE id_usuario = ?");
        $stmt->bind_param("ssssii", $nome, $matricula, $telefone, $email, $id_turma, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Professor atualizado com sucesso!'], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar: ' . $stmt->error], JSON_UNESCAPED_UNICODE);
    }
    $stmt->close();
} else {
    // Inserir novo professor (senha obrigatória)
    if (empty($senha)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Informe uma senha para o novo professor.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $status_usuario = 'Professor';

    $stmt = $conexao->prepare("INSERT INTO USUARIO (nome, matricula, telefone, email, senha, id_turma, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $nome, $matricula, $telefone, $email, $senhaHash, $id_turma, $status_usuario);
    
    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Professor cadastrado com sucesso!'], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao cadastrar: ' . $stmt->error], JSON_UNESCAPED_UNICODE);
    }
    $stmt->close();
}
?>