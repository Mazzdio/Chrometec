<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexao.php';
function resposta($ok, $mensagem, $codigo = 200) {
    http_response_code($codigo);
    echo json_encode(['sucesso' => $ok, 'mensagem' => $mensagem], JSON_UNESCAPED_UNICODE);
    exit;
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') resposta(false, 'Método não permitido.', 405);
$idUsuario = (int)($_SESSION['id_usuario'] ?? 0);
$tipo = (string)($_SESSION['status'] ?? '');
if ($idUsuario <= 0 || $tipo !== 'Aluno') resposta(false, 'Entre novamente com uma conta de aluno.', 403);
$dados = json_decode(file_get_contents('php://input'), true);
$idChromebook = (int)($dados['id_chromebook'] ?? 0);
if ($idChromebook <= 0) resposta(false, 'Chromebook inválido.', 400);
$conexao->begin_transaction();
try {
    $stmt = $conexao->prepare("SELECT id_emprestimo, status FROM EMPRESTIMO WHERE id_chromebook = ? AND id_usuario = ? AND data_devolucao IS NULL AND status IN ('Ativo', 'Devolução solicitada') ORDER BY id_emprestimo DESC LIMIT 1 FOR UPDATE");
    if (!$stmt) throw new Exception('Não foi possível localizar seu empréstimo.');
    $stmt->bind_param('ii', $idChromebook, $idUsuario);
    $stmt->execute();
    $emprestimo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$emprestimo) throw new Exception('Não há empréstimo ativo deste Chromebook vinculado à sua conta.');
    if ($emprestimo['status'] === 'Devolução solicitada') throw new Exception('Sua solicitação já está aguardando análise do administrador.');
    $idEmprestimo = (int)$emprestimo['id_emprestimo'];
    $stmt = $conexao->prepare("UPDATE EMPRESTIMO SET status = 'Devolução solicitada' WHERE id_emprestimo = ? AND status = 'Ativo' AND data_devolucao IS NULL");
    if (!$stmt) throw new Exception('Não foi possível registrar a solicitação.');
    $stmt->bind_param('i', $idEmprestimo);
    if (!$stmt->execute() || $stmt->affected_rows !== 1) throw new Exception('A solicitação não foi registrada. Atualize a página e tente novamente.');
    $stmt->close();
    // O status do Chromebook permanece in-use até a confirmação do administrador.
    $conexao->commit();
    resposta(true, 'Solicitação enviada. Aguarde a análise do administrador.');
} catch (Throwable $e) {
    $conexao->rollback();
    resposta(false, $e->getMessage(), 400);
}
?>