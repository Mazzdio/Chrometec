<?php
/**
 * Configuração do envio de e-mail.
 * Defina as variáveis de ambiente no servidor; não coloque senhas neste arquivo.
 */
return [
    'host'       => getenv('SMTP_HOST') ?: 'smtp.office365.com',
    'port'       => (int) (getenv('SMTP_PORT') ?: 587),
    'encryption' => strtolower(getenv('SMTP_ENCRYPTION') ?: 'starttls'),
    'username'   => trim(getenv('SMTP_USERNAME') ?: 'oculto'),
    'password'   => getenv('SMTP_PASSWORD') ?: 'oculto',
    'from_email' => trim(getenv('SMTP_FROM_EMAIL') ?: (getenv('SMTP_USERNAME') ?: 'oculto')),
    'from_name'  => getenv('SMTP_FROM_NAME') ?: 'Portal CEEP - Recuperação de Senha',
    // Ex.: https://seudominio.com.br/portal  |  Local: http://localhost/Site
    'base_url'   => rtrim(trim(getenv('PORTAL_BASE_URL') ?: 'http://10.0.11.217/esqueci_senha.html'), '/'),
];
