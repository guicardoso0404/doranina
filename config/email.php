<?php
require_once __DIR__ . '/env.php';

define('SMTP_HOST', env_string('SMTP_HOST', 'smtp.gmail.com'));
define('SMTP_PORT', env_int('SMTP_PORT', 587));
define('SMTP_USER', env_string('SMTP_USER', ''));
define('SMTP_PASS', env_string('SMTP_PASS', ''));
define('SMTP_FROM_EMAIL', env_string('SMTP_FROM_EMAIL', SMTP_USER));
define('SMTP_FROM_NAME', env_string('SMTP_FROM_NAME', 'DoraNina'));

define('EMAIL_PEDIDOS_DESTINO', env_string('EMAIL_PEDIDOS_DESTINO', ''));
define('EMAIL_PEDIDOS_NOME', env_string('EMAIL_PEDIDOS_NOME', 'DoraNina'));

function app_email_is_configured(): bool
{
    return SMTP_HOST !== ''
        && SMTP_PORT > 0
        && SMTP_USER !== ''
        && SMTP_PASS !== ''
        && SMTP_FROM_EMAIL !== ''
        && EMAIL_PEDIDOS_DESTINO !== '';
}
?>
