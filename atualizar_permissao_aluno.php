<?php
require_once __DIR__ . '/auth.php';
exigir_administrador();
header('Content-Type: application/json; charset=utf-8');

$host = '10.0.11.217';
$dbname = 'Armazem_Ribeira';
$username = 'aluno'; // Ajuste conforme seu ambiente
$password = 'ceep@1234'; // Ajuste conforme seu ambiente

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo json_encode(["sucesso" => false, "mensagem" => "Erro de conexão com o banco de dados."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe o ID do empréstimo e o novo status enviado pelo painel do administrador
    $id_emprestimo = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $novo_status = isset($_POST['status_permissao']) ? trim($_POST['status_permissao']) : ''; // Ex: 'Aprovado', 'Recusado', 'Devolvido'

    if ($id_emprestimo <= 0 || empty($novo_status)) {
        echo json_encode(["sucesso" => false, "mensagem" => "Dados inválidos enviados."]);
        exit;
    }

    try {
        // Atualiza diretamente a coluna 'status' na tabela EMPRESTIMO existente
        $stmtUpdate = $pdo->prepare("UPDATE EMPRESTIMO SET status = ? WHERE id_emprestimo = ?");
        $stmtUpdate->execute([$novo_status, $id_emprestimo]);

        if ($stmtUpdate->rowCount() > 0) {
            echo json_encode([
                "sucesso" => true, 
                "mensagem" => "Status do empréstimo atualizado para: " . $novo_status
            ]);
        } else {
            echo json_encode([
                "sucesso" => false, 
                "mensagem" => "Nenhum registro de empréstimo encontrado com este ID."
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            "sucesso" => false, 
            "mensagem" => "Erro ao atualizar no banco: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(["sucesso" => false, "mensagem" => "Método não permitido."]);
}
?>