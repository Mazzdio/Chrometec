<?php
require_once __DIR__ . '/auth.php';
iniciar_sessao_segura();
header('Content-Type: application/json; charset=utf-8');

if (!usuario_autenticado()) {
    http_response_code(401);
    echo json_encode([
        'sucesso' => false,
        'autenticado' => false,
        'mensagem' => 'Sessão encerrada. Faça login novamente.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$perfil = $_SESSION['status'] ?? '';
if (!in_array($perfil, ['Administrador', 'Professor'], true)) {
    http_response_code(403);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Acesso restrito.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once 'conexao.php';

try {
    // 1. Total de Empréstimos Ativos (onde data_devolucao é nula ou status é Ativo)
    $sqlAtivos = "SELECT COUNT(*) as total FROM EMPRESTIMO WHERE data_devolucao IS NULL OR status = 'Ativo'";
    $resAtivos = $conexao->query($sqlAtivos);
    $totalAtivos = ($resAtivos) ? $resAtivos->fetch_assoc()['total'] : 0;

    // 2. Retirados de Hoje (data_emprestimo é hoje)
    $sqlHoje = "SELECT COUNT(*) as total FROM EMPRESTIMO WHERE data_emprestimo = CURDATE()";
    $resHoje = $conexao->query($sqlHoje);
    $retiradosHoje = ($resHoje) ? $resHoje->fetch_assoc()['total'] : 0;

    // 3. Pendentes de Devolução (data_prevista_devolucao < hoje e ainda não devolvidos)
    $sqlPendentes = "SELECT COUNT(*) as total FROM EMPRESTIMO WHERE data_prevista_devolucao < CURDATE() AND (data_devolucao IS NULL OR status = 'Ativo')";
    $resPendentes = $conexao->query($sqlPendentes);
    $pendentesDevolucao = ($resPendentes) ? $resPendentes->fetch_assoc()['total'] : 0;

    // 4. Contagem total de Chromebooks no inventário para cálculo da cota
    $sqlTotalChromebooks = "SELECT COUNT(*) as total FROM CHROMEBOOK";
    $resTotalC = $conexao->query($sqlTotalChromebooks);
    $totalChromebooks = ($resTotalC) ? $resTotalC->fetch_assoc()['total'] : 0;

    $cotaOcupadaPercent = ($totalChromebooks > 0) ? round(($totalAtivos / $totalChromebooks) * 100) : 0;
    if ($cotaOcupadaPercent > 100) $cotaOcupadaPercent = 100;

    // 5. Setores e Cotas calculados com base nos status reais da tabela CHROMEBOOK
    $sqlSetores = "SELECT modelo, COUNT(*) as qtd, 
                   SUM(CASE WHEN status = 'in-use' OR status = 'em uso' THEN 1 ELSE 0 END) as em_uso 
                   FROM CHROMEBOOK GROUP BY modelo";
    $resSetores = $conexao->query($sqlSetores);
    
    $setores = [];
    if ($resSetores && $resSetores->num_rows > 0) {
        while ($row = $resSetores->fetch_assoc()) {
            $capacidade = intval($row['qtd']);
            $utilizados = intval($row['em_uso']);
            $porcentagem = ($capacidade > 0) ? round(($utilizados / $capacidade) * 100) : 0;
            
            $setores[] = [
                'nome' => $row['modelo'] ? 'Modelo ' . $row['modelo'] : 'Padrão',
                'utilizados' => $utilizados,
                'capacidade' => $capacidade,
                'porcentagem' => $porcentagem
            ];
        }
    }

    echo json_encode([
        'sucesso' => true,
        'dados' => [
            'total_ativos' => (int)$totalAtivos,
            'retirados_hoje' => (int)$retiradosHoje,
            'pendentes' => (int)$pendentesDevolucao,
            'cota_percentual' => $cotaOcupadaPercent,
            'setores' => $setores
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao carregar dados do painel: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>