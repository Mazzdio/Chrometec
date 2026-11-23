<?php
require_once __DIR__ . '/auth.php';
iniciar_sessao_segura();
if (!usuario_autenticado() || ($_SESSION['status'] ?? '') !== 'Aluno') { http_response_code(401); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['sucesso'=>false,'emprestimo'=>null,'mensagem'=>'Sessão de aluno inválida.'], JSON_UNESCAPED_UNICODE); exit; }
header('Content-Type: application/json; charset=utf-8');

require_once 'conexao.php';

// Pega o ID do usuário logado na sessão do PHP
$id_usuario = $_SESSION['id_usuario'] ?? $_SESSION['aluno_id'] ?? $_SESSION['id'] ?? 0;

if ($id_usuario <= 0) {
    echo json_encode(['sucesso' => false, 'emprestimo' => null], JSON_UNESCAPED_UNICODE);
    exit;
}

// Busca o empréstimo ativo do aluno na tabela EMPRESTIMO cruzando com a tabela CHROMEBOOK
$sql = "SELECT e.id_emprestimo, e.data_emprestimo, e.data_prevista_devolucao, 
               c.id_chromebook, c.patrimonio, c.modelo 
        FROM EMPRESTIMO e
        JOIN CHROMEBOOK c ON e.id_chromebook = c.id_chromebook
        WHERE e.id_usuario = ? AND (e.data_devolucao IS NULL OR e.status = 'Ativo')
        LIMIT 1";

$stmt = $conexao->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {
        $retirado = !empty($row['data_emprestimo']) ? date('d/m/Y', strtotime($row['data_emprestimo'])) : date('d/m/Y');
        $devolver = !empty($row['data_prevista_devolucao']) ? date('d/m/Y', strtotime($row['data_prevista_devolucao'])) : date('d/m/Y', strtotime('+1 day'));

        echo json_encode([
            'sucesso' => true,
            'emprestimo' => [
                'id' => (int)$row['id_emprestimo'],
                'patrimonio' => $row['patrimonio'] ?? 'CEEP-' . str_pad($row['id_chromebook'], 4, '0', STR_PAD_LEFT),
                'modelo' => $row['modelo'] ?? 'Padrão',
                'retirado_em' => $retirado,
                'devolver_ate' => $devolver
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $stmt->close();
}

echo json_encode(['sucesso' => true, 'emprestimo' => null], JSON_UNESCAPED_UNICODE);
exit;
?>