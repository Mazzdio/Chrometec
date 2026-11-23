<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexao.php';
function resposta($ok, $mensagem, $codigo = 200) {
    http_response_code($codigo);
    echo json_encode(['sucesso' => $ok, 'mensagem' => $mensagem], JSON_UNESCAPED_UNICODE);
    exit;
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') resposta(false, 'Método não permitido.', 405);
if ((int)($_SESSION['id_usuario'] ?? 0) <= 0 || ($_SESSION['status'] ?? '') !== 'Administrador') resposta(false, 'Apenas administradores podem analisar devoluções.', 403);
$dados = json_decode(file_get_contents('php://input'), true);
$idEmprestimo = (int)($dados['id_emprestimo'] ?? 0);
$acao = (string)($dados['acao'] ?? '');
if ($idEmprestimo <= 0 || !in_array($acao, ['aceitar', 'recusar'], true)) resposta(false, 'Dados da solicitação inválidos.', 400);
$conexao->begin_transaction();
try {
    $stmt = $conexao->prepare("SELECT id_chromebook FROM EMPRESTIMO WHERE id_emprestimo = ? AND status = 'Devolução solicitada' AND data_devolucao IS NULL FOR UPDATE");
    if (!$stmt) throw new Exception('Não foi possível localizar a solicitação.');
    $stmt->bind_param('i', $idEmprestimo);
    $stmt->execute();
    $emprestimo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$emprestimo) throw new Exception('Esta solicitação já foi analisada ou não existe. Atualize a lista.');
    $idChromebook = (int)$emprestimo['id_chromebook'];
    if ($acao === 'aceitar') {
        $stmt = $conexao->prepare("UPDATE EMPRESTIMO SET status = 'Devolvido', data_devolucao = CURDATE() WHERE id_emprestimo = ? AND status = 'Devolução solicitada' AND data_devolucao IS NULL");
        if (!$stmt) throw new Exception('Não foi possível concluir o empréstimo.');
        $stmt->bind_param('i', $idEmprestimo);
        if (!$stmt->execute() || $stmt->affected_rows !== 1) throw new Exception('Não foi possível confirmar a devolução.');
        $stmt->close();
        $stmt = $conexao->prepare("UPDATE CHROMEBOOK SET status = 'available' WHERE id_chromebook = ? AND status = 'in-use'");
        if (!$stmt) throw new Exception('Não foi possível liberar o Chromebook.');
        $stmt->bind_param('i', $idChromebook);
        if (!$stmt->execute() || $stmt->affected_rows !== 1) throw new Exception('O empréstimo foi encerrado, mas não foi possível liberar o Chromebook. Verifique o inventário.');
        $stmt->close();
        $mensagem = 'Devolução aceita. O empréstimo foi encerrado e o Chromebook está disponível.';
    } else {
        // A recusa reativa o empréstimo; o equipamento continua em uso pelo aluno.
        $stmt = $conexao->prepare("UPDATE EMPRESTIMO SET status = 'Ativo' WHERE id_emprestimo = ? AND status = 'Devolução solicitada' AND data_devolucao IS NULL");
        if (!$stmt) throw new Exception('Não foi possível recusar a solicitação.');
        $stmt->bind_param('i', $idEmprestimo);
        if (!$stmt->execute() || $stmt->affected_rows !== 1) throw new Exception('Não foi possível atualizar a solicitação.');
        $stmt->close();
        $mensagem = 'Solicitação recusada. O empréstimo continua ativo e o Chromebook permanece em uso.';
    }
    $conexao->commit();
    resposta(true, $mensagem);
} catch (Throwable $e) {
    $conexao->rollback();
    resposta(false, $e->getMessage(), 400);
}
?>