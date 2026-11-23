<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexao.php';
function resposta($ok, $mensagem, $solicitacoes = [], $codigo = 200) {
    http_response_code($codigo);
    echo json_encode(['sucesso' => $ok, 'mensagem' => $mensagem, 'solicitacoes' => $solicitacoes], JSON_UNESCAPED_UNICODE);
    exit;
}
if ((int)($_SESSION['id_usuario'] ?? 0) <= 0 || ($_SESSION['status'] ?? '') !== 'Administrador') resposta(false, 'Acesso restrito a administradores.', [], 403);
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') resposta(false, 'Método não permitido.', [], 405);
$sql = "SELECT e.id_emprestimo, e.data_emprestimo, u.nome AS aluno, c.patrimonio, c.modelo
        FROM EMPRESTIMO e
        INNER JOIN USUARIO u ON u.id_usuario = e.id_usuario
        INNER JOIN CHROMEBOOK c ON c.id_chromebook = e.id_chromebook
        WHERE e.status = 'Devolução solicitada' AND e.data_devolucao IS NULL
        ORDER BY e.data_emprestimo ASC, e.id_emprestimo ASC";
$resultado = $conexao->query($sql);
if (!$resultado) resposta(false, 'Não foi possível consultar as solicitações.', [], 500);
$solicitacoes = [];
while ($linha = $resultado->fetch_assoc()) {
    $solicitacoes[] = [
        'id_emprestimo' => (int)$linha['id_emprestimo'],
        'aluno' => $linha['aluno'] ?? 'Aluno não identificado',
        'patrimonio' => $linha['patrimonio'] ?? 'Sem patrimônio',
        'modelo' => $linha['modelo'] ?? 'Padrão',
        'data_emprestimo_formatada' => !empty($linha['data_emprestimo']) ? date('d/m/Y', strtotime($linha['data_emprestimo'])) : 'Não informada'
    ];
}
resposta(true, '', $solicitacoes);
?>