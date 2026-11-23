
<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/conexao.php';

function responder($sucesso, $mensagem, $codigo = 200) {
    http_response_code($codigo);
    echo json_encode([
        'sucesso' => $sucesso,
        'mensagem' => $mensagem
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Somente administradores podem excluir alunos.
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'Administrador') {
    responder(false, 'Acesso negado.', 403);
}

// A exclusão deve ser feita por POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método de requisição inválido.', 405);
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    responder(false, 'ID do aluno inválido.', 400);
}

try {
    $conexao->begin_transaction();

    // Confere se o usuário existe e realmente é um aluno.
    $stmt = $conexao->prepare(
        "SELECT id_usuario, nome
         FROM USUARIO
         WHERE id_usuario = ? AND status = 'Aluno'
         FOR UPDATE"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $aluno = $resultado->fetch_assoc();
    $stmt->close();

    if (!$aluno) {
        $conexao->rollback();
        responder(false, 'Aluno não encontrado ou cadastro não pertence a um aluno.', 404);
    }

    // Preserva o histórico: nenhum aluno com empréstimos pode ser excluído.
    $stmt = $conexao->prepare(
        "SELECT COUNT(*) AS total
         FROM EMPRESTIMO
         WHERE id_usuario = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $dados = $resultado->fetch_assoc();
    $stmt->close();

    if ((int)$dados['total'] > 0) {
        $conexao->rollback();

        responder(
            false,
            'Não é possível excluir este aluno porque existem empréstimos vinculados ao cadastro. O histórico precisa ser preservado.'
        );
    }

    // Exclui somente o cadastro de aluno confirmado.
    $stmt = $conexao->prepare(
        "DELETE FROM USUARIO
         WHERE id_usuario = ? AND status = 'Aluno'"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows !== 1) {
        $stmt->close();
        $conexao->rollback();
        responder(false, 'Não foi possível excluir o cadastro do aluno.');
    }

    $stmt->close();
    $conexao->commit();

    responder(true, 'Aluno excluído com sucesso!');

} catch (Throwable $erro) {
    try {
        $conexao->rollback();
    } catch (Throwable $erroRollback) {
        // A transação pode já ter sido encerrada.
    }

    error_log('Erro ao excluir aluno: ' . $erro->getMessage());

    responder(
        false,
        'Ocorreu um erro ao excluir o aluno. Verifique os registros vinculados ou contate o responsável pelo sistema.',
        500
    );
}
?>
