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

require_once __DIR__ . '/conexao.php';
$conexao->set_charset('utf8mb4');

// Lista apenas as turmas escolares (1º ao 3º ano)
$sqlTurmas = "SELECT t.id_turma, t.nome_turma, t.serie, t.turno, t.ano_letivo,
                     COUNT(u.id_usuario) AS total_alunos
              FROM TURMA t
              LEFT JOIN USUARIO u
                ON u.id_turma = t.id_turma AND u.status = 'Aluno'
              WHERE t.serie IN ('1', '2', '3')
              GROUP BY t.id_turma, t.nome_turma, t.serie, t.turno, t.ano_letivo
              ORDER BY t.serie ASC, t.nome_turma ASC";

$resTurmas = $conexao->query($sqlTurmas);
$turmas = [];
if ($resTurmas) {
    while ($row = $resTurmas->fetch_assoc()) {
        $turmas[] = [
            'id_turma' => (int)$row['id_turma'],
            'nome_turma' => $row['nome_turma'],
            'serie' => $row['serie'],
            'turno' => $row['turno'] ?? 'Integral',
            'ano_letivo' => (int)($row['ano_letivo'] ?? date('Y')),
            'total_alunos' => (int)$row['total_alunos']
        ];
    }
}

// Lista alunos e verifica se possuem Chromebook em empréstimo ativo no momento
$sqlAlunos = "SELECT u.id_usuario, u.nome, u.matricula, u.email, u.telefone, u.id_turma,
                     t.nome_turma, t.serie,
                     e.id_emprestimo, c.patrimonio AS chromebook_em_uso
              FROM USUARIO u
              JOIN TURMA t ON t.id_turma = u.id_turma
              LEFT JOIN EMPRESTIMO e
                ON e.id_usuario = u.id_usuario
               AND e.status = 'Ativo'
               AND e.data_devolucao IS NULL
              LEFT JOIN CHROMEBOOK c ON c.id_chromebook = e.id_chromebook
              WHERE u.status = 'Aluno'
              ORDER BY u.nome ASC";

$resAlunos = $conexao->query($sqlAlunos);
$alunos = [];
if ($resAlunos) {
    while ($row = $resAlunos->fetch_assoc()) {
        $alunos[] = [
            'id_usuario' => (int)$row['id_usuario'],
            'nome' => $row['nome'],
            'matricula' => $row['matricula'] ?? '—',
            'email' => $row['email'] ?? '—',
            'telefone' => $row['telefone'] ?? '—',
            'id_turma' => (int)$row['id_turma'],
            'nome_turma' => $row['nome_turma'],
            'serie' => $row['serie'],
            'com_chromebook' => $row['id_emprestimo'] !== null,
            'chromebook_patrimonio' => $row['chromebook_em_uso'] ?? null
        ];
    }
}

echo json_encode([
    'sucesso' => true,
    'turmas' => $turmas,
    'alunos' => $alunos
], JSON_UNESCAPED_UNICODE);