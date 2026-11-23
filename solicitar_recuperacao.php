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
    // Mantém os DATETIME da recuperação em UTC, independentemente do fuso do servidor.
    $conexao->query("SET time_zone = '+00:00'");

    $email = trim((string) ($_POST['email'] ?? ''));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        responder(422, false, 'Informe um endereço de e-mail válido.');
    }

    $stmt = $conexao->prepare(
        'SELECT id_usuario, nome, email FROM USUARIO WHERE email = ? LIMIT 1'
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Resposta genérica evita revelar se determinado e-mail está cadastrado.
    $mensagemGenerica = 'Se o endereço estiver cadastrado, você receberá um link de recuperação em instantes.';
    if (!$usuario) {
        responder(200, true, $mensagemGenerica);
    }

    $config = require __DIR__ . '/config_email.php';
    if (
        empty($config['username']) || empty($config['password']) ||
        empty($config['from_email']) || empty($config['base_url'])
    ) {
        error_log('[Portal CEEP] SMTP/PORTAL_BASE_URL não configurados.');
        responder(500, false, 'O serviço de recuperação está temporariamente indisponível. Avise a administração.');
    }

    $baseUrl = $config['base_url'];
    if (!filter_var($baseUrl, FILTER_VALIDATE_URL) || !in_array((string) parse_url($baseUrl, PHP_URL_SCHEME), ['http', 'https'], true)) {
        error_log('[Portal CEEP] PORTAL_BASE_URL inválida.');
        responder(500, false, 'A configuração do endereço do Portal está inválida.');
    }

    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expiraEm = gmdate('Y-m-d H:i:s', time() + 3600);
    $idUsuario = (int) $usuario['id_usuario'];

    // O banco armazena somente SHA-256 do token; o token puro segue apenas no e-mail.
    $stmt = $conexao->prepare(
        'INSERT INTO recuperacao_senha (id_usuario, token_hash, expira_em, usado) VALUES (?, ?, ?, 0)'
    );
    $stmt->bind_param('iss', $idUsuario, $tokenHash, $expiraEm);
    $stmt->execute();
    $idRecuperacao = (int) $conexao->insert_id;
    $stmt->close();

    try {
        $autoload = __DIR__ . '/vendor/autoload.php';
        if (!is_file($autoload)) {
            throw new RuntimeException('Dependências do PHPMailer não encontradas.');
        }
        require_once $autoload;

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = (string) $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = (string) $config['username'];
        $mail->Password = (string) $config['password'];
        $mail->Port = (int) $config['port'];
        $mail->Timeout = 25;
        $mail->CharSet = 'UTF-8';

        if ($config['encryption'] === 'starttls') {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($config['encryption'] === 'smtps') {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } else {
            throw new RuntimeException('SMTP_ENCRYPTION deve ser starttls ou smtps.');
        }

        // Não desabilite a validação TLS: o certificado do servidor SMTP deve ser validado.
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
            ],
        ];

        $mail->setFrom((string) $config['from_email'], (string) $config['from_name']);
        $mail->addAddress((string) $usuario['email'], (string) $usuario['nome']);

        $link = $baseUrl . '/redefinir_senha.html?token=' . rawurlencode($token);
        $nomeSeguro = htmlspecialchars((string) $usuario['nome'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $linkSeguro = htmlspecialchars($link, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $mail->isHTML(true);
        $mail->Subject = 'Recuperação de senha - Portal CEEP';
        $mail->Body = "
            <div style=\"font-family:Arial,sans-serif;color:#333;line-height:1.6\">
                <h2>Olá, {$nomeSeguro}!</h2>
                <p>Você solicitou a recuperação da senha da sua conta no Portal CEEP.</p>
                <p>Use o botão abaixo para criar uma nova senha. O link expira em 1 hora e só pode ser utilizado uma vez.</p>
                <p style=\"margin:24px 0\">
                    <a href=\"{$linkSeguro}\" style=\"background:#7c3aed;color:#fff;padding:12px 20px;text-decoration:none;border-radius:6px;font-weight:bold\">Redefinir senha</a>
                </p>
                <p>Se você não solicitou esta alteração, ignore esta mensagem.</p>
            </div>
        ";
        $mail->AltBody = "Olá, {$usuario['nome']}! Acesse este link para redefinir sua senha (válido por 1 hora): {$link}";
        $mail->send();

        // Ao emitir um novo link, invalida os anteriores ainda não utilizados.
        $stmt = $conexao->prepare(
            'UPDATE recuperacao_senha SET usado = 1 WHERE id_usuario = ? AND id_recuperacao <> ? AND usado = 0'
        );
        $stmt->bind_param('ii', $idUsuario, $idRecuperacao);
        $stmt->execute();
        $stmt->close();

        responder(200, true, $mensagemGenerica);
    } catch (Throwable $e) {
        // Não deixa token ativo no banco se o e-mail não foi enviado.
        $stmt = $conexao->prepare('DELETE FROM recuperacao_senha WHERE id_recuperacao = ?');
        $stmt->bind_param('i', $idRecuperacao);
        $stmt->execute();
        $stmt->close();

        error_log('[Portal CEEP] Falha no envio de recuperação: ' . $e->getMessage());
        responder(502, false, 'Não foi possível enviar o e-mail agora. Verifique a configuração SMTP ou tente novamente mais tarde.');
    }
} catch (Throwable $e) {
    error_log('[Portal CEEP] Erro em solicitar_recuperacao.php: ' . $e->getMessage());
    responder(500, false, 'Ocorreu um erro interno ao processar a solicitação.');
}
