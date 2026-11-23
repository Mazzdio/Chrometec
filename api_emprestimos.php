<?php
require_once __DIR__ . '/auth.php';
iniciar_sessao_segura();
header('Content-Type: application/json; charset=utf-8');

if (!usuario_autenticado()) {
    http_response_code(401);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Sessão encerrada. Faça login novamente.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$perfil = $_SESSION['status'] ?? '';
if (!in_array($perfil, ['Professor', 'Administrador'], true)) {
    http_response_code(403);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Acesso restrito a professores e administradores.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once 'conexao.php';
$conexao->set_charset('utf8mb4');

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$ids = $input['ids'] ?? $input['chromebooks'] ?? [];
if (!is_array($ids) || count($ids) === 0) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Nenhum Chromebook selecionado.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$ids = array_values(array_unique(array_filter(array_map('intval', $ids), fn($v) => $v > 0)));
if (count($ids) === 0) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'IDs de Chromebook inválidos.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$id_usuario = (int)($_SESSION['id_usuario'] ?? 0);
if ($id_usuario <= 0) {
    http_response_code(401);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Usuário da sessão não identificado.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$turma = trim((string)($input['turma'] ?? ''));
$turno = trim((string)($input['turno'] ?? ''));

$conexao->begin_transaction();

try {
    $stmtCheck = $conexao->prepare("SELECT status, patrimonio FROM CHROMEBOOK WHERE id_chromebook = ? FOR UPDATE");
    $stmtInsert = $conexao->prepare(
        "INSERT INTO EMPRESTIMO (id_usuario, id_chromebook, data_emprestimo, data_prevista_devolucao, data_devolucao, status)
         VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 DAY), NULL, 'Ativo')"
    );
    $stmtUpdate = $conexao->prepare("UPDATE CHROMEBOOK SET status = 'in-use' WHERE id_chromebook = ?");

    if (!$stmtCheck || !$stmtInsert || !$stmtUpdate) {
        throw new Exception('Erro ao preparar consultas SQL no banco.');
    }

    foreach ($ids as $idCb) {
        $stmtCheck->bind_param("i", $idCb);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        $cb = $resCheck->fetch_assoc();

        if (!$cb) {
            throw new Exception("Chromebook #{$idCb} não encontrado no inventário.");
        }

        $statusAtual = strtolower(trim((string)($cb['status'] ?? '')));
        if ($statusAtual !== 'available' && $statusAtual !== 'disponivel' && $statusAtual !== 'disponível') {
            $pat = $cb['patrimonio'] ?: "#{$idCb}";
            throw new Exception("O equipamento {$pat} não está disponível para empréstimo.");
        }

        $stmtInsert->bind_param("ii", $id_usuario, $idCb);
        if (!$stmtInsert->execute()) {
            throw new Exception("Falha ao registrar empréstimo do Chromebook #{$idCb}.");
        }

        $stmtUpdate->bind_param("i", $idCb);
        if (!$stmtUpdate->execute()) {
            throw new Exception("Falha ao atualizar status do Chromebook #{$idCb}.");
        }
    }

    $conexao->commit();

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Empréstimo registrado e status atualizado para em uso.',
        'ids' => $ids,
        'turma' => $turma,
        'turno' => $turno
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    $conexao->rollback();
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}