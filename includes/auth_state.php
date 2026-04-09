<?php
require_once __DIR__ . '/../config/env.php';

function app_auth_cookie_name(): string
{
    return env_string('AUTH_COOKIE_NAME', 'doranina_auth');
}

function app_auth_secret(): string
{
    return env_string('APP_SECRET', 'local-dev-only-secret-change-me');
}

function app_is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }

    return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
}

function app_base64url_encode(string $value): string
{
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}

function app_base64url_decode(string $value): string|false
{
    $padding = strlen($value) % 4;
    if ($padding > 0) {
        $value .= str_repeat('=', 4 - $padding);
    }

    return base64_decode(strtr($value, '-_', '+/'));
}

function app_cookie_options(int $expires): array
{
    return [
        'expires' => $expires,
        'path' => '/',
        'secure' => app_is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ];
}

function app_read_auth_payload(): array
{
    $cookie = $_COOKIE[app_auth_cookie_name()] ?? '';

    if ($cookie === '' || !str_contains($cookie, '.')) {
        return [];
    }

    [$payloadBase64, $signature] = explode('.', $cookie, 2);
    $expectedSignature = hash_hmac('sha256', $payloadBase64, app_auth_secret());

    if (!hash_equals($expectedSignature, $signature)) {
        return [];
    }

    $payloadJson = app_base64url_decode($payloadBase64);
    if ($payloadJson === false) {
        return [];
    }

    $payload = json_decode($payloadJson, true);
    if (!is_array($payload)) {
        return [];
    }

    if (($payload['exp'] ?? 0) < time()) {
        return [];
    }

    return $payload;
}

function app_boot_auth(): void
{
    static $booted = false;

    if ($booted) {
        return;
    }

    $booted = true;
    $payload = app_read_auth_payload();
    $_SESSION = [];

    if ($payload === []) {
        return;
    }

    $_SESSION['usuario_id'] = (int)($payload['id'] ?? 0);
    $_SESSION['usuario_nome'] = (string)($payload['nome'] ?? '');
    $_SESSION['usuario_email'] = (string)($payload['email'] ?? '');
    $_SESSION['usuario_tipo'] = (string)($payload['tipo'] ?? 'cliente');
}

function app_login_user(array $usuario): void
{
    $payload = [
        'id' => (int)($usuario['id'] ?? 0),
        'nome' => (string)($usuario['nome'] ?? ''),
        'email' => (string)($usuario['email'] ?? ''),
        'tipo' => (string)($usuario['tipo'] ?? 'cliente'),
        'exp' => time() + (60 * 60 * 24 * 30),
    ];

    $payloadBase64 = app_base64url_encode(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $cookieValue = $payloadBase64 . '.' . hash_hmac('sha256', $payloadBase64, app_auth_secret());

    setcookie(app_auth_cookie_name(), $cookieValue, app_cookie_options($payload['exp']));
    $_COOKIE[app_auth_cookie_name()] = $cookieValue;

    $_SESSION['usuario_id'] = $payload['id'];
    $_SESSION['usuario_nome'] = $payload['nome'];
    $_SESSION['usuario_email'] = $payload['email'];
    $_SESSION['usuario_tipo'] = $payload['tipo'];
}

function app_logout_user(): void
{
    setcookie(app_auth_cookie_name(), '', app_cookie_options(time() - 3600));
    unset($_COOKIE[app_auth_cookie_name()]);
    $_SESSION = [];
}

function app_safe_redirect_path(?string $redirect, string $fallback = 'index.php'): string
{
    if (!is_string($redirect)) {
        return $fallback;
    }

    $redirect = trim($redirect);
    if ($redirect === '' || str_starts_with($redirect, '//')) {
        return $fallback;
    }

    $parts = parse_url($redirect);
    if ($parts === false || isset($parts['scheme']) || isset($parts['host'])) {
        return $fallback;
    }

    return $redirect;
}

app_boot_auth();
?>
