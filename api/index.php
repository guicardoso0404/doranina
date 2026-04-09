<?php
$projectRoot = realpath(dirname(__DIR__));
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$route = trim($requestPath, '/');

function app_send_static_asset(string $assetPath): void
{
    $extension = strtolower(pathinfo($assetPath, PATHINFO_EXTENSION));
    $contentTypes = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',
        'txt' => 'text/plain; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
    ];

    $contentType = $contentTypes[$extension] ?? 'application/octet-stream';

    header('Content-Type: ' . $contentType);
    header('Content-Length: ' . (string)filesize($assetPath));
    header('Cache-Control: public, max-age=31536000, immutable');

    readfile($assetPath);
    exit;
}

if (str_starts_with($route, 'assets/')) {
    $assetPath = realpath($projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $route));
    $assetsRoot = realpath($projectRoot . DIRECTORY_SEPARATOR . 'assets');

    if (
        $assetPath !== false
        && $assetsRoot !== false
        && str_starts_with($assetPath, $assetsRoot . DIRECTORY_SEPARATOR)
        && is_file($assetPath)
    ) {
        app_send_static_asset($assetPath);
    }

    http_response_code(404);
    echo 'Asset nao encontrado.';
    exit;
}

if ($route === '') {
    $route = 'index.php';
} elseif ($route === 'admin') {
    $route = 'admin/index.php';
} elseif (str_ends_with($route, '/')) {
    $route .= 'index.php';
}

$route = preg_replace('#/+#', '/', $route);

if (!preg_match('#^(admin/)?[A-Za-z0-9_-]+\.php$#', $route)) {
    http_response_code(404);
    echo 'Pagina nao encontrada.';
    exit;
}

$targetPath = realpath($projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $route));

if ($targetPath === false || !str_starts_with($targetPath, $projectRoot . DIRECTORY_SEPARATOR)) {
    http_response_code(404);
    echo 'Pagina nao encontrada.';
    exit;
}

chdir(dirname($targetPath));
require $targetPath;
?>
