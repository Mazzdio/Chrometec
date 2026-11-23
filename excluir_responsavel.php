
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

function responder($sucesso, $mensagem, $status = 200) {
    http_response_code($status);

    echo json_encode([
        'sucesso' => $sucesso,
        'mensagem' => $mensagem
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Somente requisições POST são permitidas.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método não permitido.', 405);
}

// Confere se existe uma sessão administrativa válida.
exigir_administrador();

// Identificador do administrador que será excluído.
$idExcluir = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$idExcluir || $idExcluir <= 0) {
    responder(false, 'ID do administrador inválido.', 400);
}

// Impede que o administrador exclua a própria conta.
$idLogado = (int) $_SESSION['id_usuario'];

if ($idExcluir === $idLogado) {
    responder(false, 'Você não pode excluir sua própria conta.', 403);
}

try {
    // Inicia uma transação para proteger as verificações e a exclusão.
    $conexao->begin_transaction();

    // Localiza e bloqueia o registro que será analisado.
    $stmtUsuario = $conexao->prepare(
        "SELECT id_usuario, nome, status
         FROM USUARIO
         WHERE id_usuario = ?
         FOR UPDATE"
    );

    if (!$stmtUsuario) {
        throw new Exception('Erro ao preparar a consulta do administrador.');
    }

    $stmtUsuario->bind_param('i', $idExcluir);
    $stmtUsuario->execute();
    $stmtUsuario->bind_result($idEncontrado, $nomeEncontrado, $statusEncontrado);

    $usuarioExiste = $stmtUsuario->fetch();
    $stmtUsuario->close();

    if (!$usuarioExiste) {
        $conexao->rollback();
        responder(false, 'Administrador não encontrado.', 404);
    }

    // Não permite excluir contas que não sejam de administradores.
    if ($statusEncontrado !== 'Administrador') {
        $conexao->rollback();
        responder(false, 'A conta selecionada não é de um administrador.', 403);
    }

    // Bloqueia os registros administrativos durante a verificação.
    $resultadoAdms = $conexao->query(
        "SELECT id_usuario
         FROM USUARIO
         WHERE status = 'Administrador'
         FOR UPDATE"
    );

    if (!$resultadoAdms) {
        throw new Exception('Erro ao verificar os administradores cadastrados.');
    }

    $quantidadeAdms = $resultadoAdms->num_rows;
    $resultadoAdms->free();

    // Impede a exclusão do último administrador.
    if ($quantidadeAdms <= 1) {
        $conexao->rollback();
        responder(
            false,
            'Não é possível excluir o último administrador do sistema.',
            403
        );
    }

    // Verifica se existem empréstimos vinculados à conta.
    $stmtEmprestimos = $conexao->prepare(
        "SELECT id_emprestimo
         FROM EMPRESTIMO
         WHERE id_usuario = ?
         LIMIT 1"
    );

    if (!$stmtEmprestimos) {
        throw new Exception('Erro ao verificar os empréstimos do administrador.');
    }

    $stmtEmprestimos->bind_param('i', $idExcluir);
    $stmtEmprestimos->execute();
    $stmtEmprestimos->store_result();

    $possuiEmprestimos = $stmtEmprestimos->num_rows > 0;
    $stmtEmprestimos->close();

    if ($possuiEmprestimos) {
        $conexao->rollback();

        responder(
            false,
            'Este administrador possui empréstimos registrados. A exclusão foi bloqueada para preservar o histórico do sistema.',
            409
        );
    }

    // Todas as verificações passaram. Exclui o administrador.
    // Os registros de recuperação de senha são removidos
    // automaticamente pela chave estrangeira ON DELETE CASCADE.
    $stmtExcluir = $conexao->prepare(
        "DELETE FROM USUARIO
         WHERE id_usuario = ?
           AND status = 'Administrador'"
    );

    if (!$stmtExcluir) {
        throw new Exception('Erro ao preparar a exclusão do administrador.');
    }

    $stmtExcluir->bind_param('i', $idExcluir);
    $stmtExcluir->execute();

    if ($stmtExcluir->affected_rows !== 1) {
        $stmtExcluir->close();
        throw new Exception('Não foi possível confirmar a exclusão do administrador.');
    }

    $stmtExcluir->close();

    // Confirma a operação no banco.
    $conexao->commit();

    responder(
        true,
        'Administrador ' . $nomeEncontrado . ' excluído com sucesso.'
    );

} catch (Throwable $erro) {

    // Desfaz qualquer alteração caso ocorra um erro.
    try {
        $conexao->rollback();
    } catch (Throwable $erroRollback) {
        // Não substitui a mensagem do erro original.
    }

    error_log('Erro ao excluir administrador: ' . $erro->getMessage());

    responder(
        false,
        'Ocorreu um erro interno ao tentar excluir o administrador. Consulte os registros do servidor.',
        500
    );
}
