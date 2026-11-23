<?php
declare(strict_types=1);

date_default_timezone_set('UTC');
header('Content-Type: application/json; charset=utf-8');

function responder(int $status, bool $sucesso, string $mensagem): never
{
    http_response_code($status);
    echo json_encode(
        ['sucesso' => $sucesso, 'mensagem' => $mensagem],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responder(405, false, 'Método não permitido.');
}

require_once __DIR__ . '/conexao.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexao->query("SET time_zone = '+00:00'");

    $tokenPuro = trim((string) ($_POST['token'] ?? ''));
    $novaSenha = (string) ($_POST['senha'] ?? '');

    if ($tokenPuro === '' || $novaSenha === '') {
        responder(422, false, 'Dados incompletos.');
    }
    if (!preg_match('/^[a-f0-9]{64}$/i', $tokenPuro)) {
        responder(400, false, 'Este link de recuperação é inválido. Solicite outro.');
    }
    if (
        strlen($novaSenha) < 8 ||
        !preg_match('/[A-Z]/', $novaSenha) ||
        !preg_match('/[0-9]/', $novaSenha) ||
        !preg_match('/[^a-zA-Z0-9]/', $novaSenha)
    ) {
        responder(422, false, 'A senha deve ter pelo menos 8 caracteres, uma letra maiúscula, um número e um caractere especial.');
    }

    $tokenHash = hash('sha256', $tokenPuro);
    $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
    if ($senhaHash === false) {
        throw new RuntimeException('Não foi possível gerar o hash da senha.');
    }

    $conexao->begin_transaction();
    try {
        // O bloqueio evita que duas requisições usem o mesmo token simultaneamente.
        $stmt = $conexao->prepare(
            'SELECT id_recuperacao, id_usuario, expira_em, usado
             FROM recuperacao_senha
             WHERE token_hash = ?
             LIMIT 1 FOR UPDATE'
        );
        $stmt->bind_param('s', $tokenHash);
        $stmt->execute();
        $recuperacao = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$recuperacao || (int) $recuperacao['usado'] !== 0) {
            $conexao->rollback();
            responder(400, false, 'Este link de recuperação é inválido ou já foi utilizado. Solicite outro.');
        }

        $expiraEm = new DateTimeImmutable((string) $recuperacao['expira_em'], new DateTimeZone('UTC'));
        $agora = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        if ($expiraEm <= $agora) {
            $conexao->rollback();
            responder(400, false, 'Este link de recuperação expirou. Solicite um novo.');
        }

        $idUsuario = (int) $recuperacao['id_usuario'];
        $idRecuperacao = (int) $recuperacao['id_recuperacao'];

        $stmt = $conexao->prepare('UPDATE USUARIO SET senha = ? WHERE id_usuario = ?');
        $stmt->bind_param('si', $senhaHash, $idUsuario);
        $stmt->execute();
        if ($stmt->affected_rows !== 1) {
            $stmt->close();
            throw new RuntimeException('A conta associada ao token não foi atualizada.');
        }
        $stmt->close();

        $stmt = $conexao->prepare(
            'UPDATE recuperacao_senha SET usado = 1 WHERE id_recuperacao = ? AND usado = 0'
        );
        $stmt->bind_param('i', $idRecuperacao);
        $stmt->execute();
        if ($stmt->affected_rows !== 1) {
            $stmt->close();
            throw new RuntimeException('O token não pôde ser invalidado.');
        }
        $stmt->close();

        // Invalida quaisquer outros links pendentes da mesma conta.
        $stmt = $conexao->prepare(
            'UPDATE recuperacao_senha SET usado = 1 WHERE id_usuario = ? AND id_recuperacao <> ? AND usado = 0'
        );
        $stmt->bind_param('ii', $idUsuario, $idRecuperacao);
        $stmt->execute();
        $stmt->close();

        $conexao->commit();
        responder(200, true, 'Senha alterada com sucesso! Você já pode fazer login.');
    } catch (Throwable $e) {
        $conexao->rollback();
        throw $e;
    }
} catch (Throwable $e) {
    error_log('[Portal CEEP] Erro em redefinir_senha.php: ' . $e->getMessage());
    responder(500, false, 'Não foi possível redefinir a senha agora. Tente novamente.');
}
