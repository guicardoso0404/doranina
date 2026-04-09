<?php
require_once __DIR__ . '/env.php';

$conn = null;
$connError = null;

function app_db_build_pgsql_config(): ?array
{
    $databaseUrl = trim(env_string('DATABASE_URL', ''));

    if ($databaseUrl === '') {
        return null;
    }

    if (preg_match('/((?:postgres(?:ql)?|pgsql):\\/\\/\\S+)/i', $databaseUrl, $matches) === 1) {
        $databaseUrl = trim($matches[1], "\"' \t\n\r\0\x0B");
    }

    $parsed = parse_url($databaseUrl);

    if ($parsed === false) {
        throw new RuntimeException('DATABASE_URL invalida.');
    }

    $scheme = strtolower((string)($parsed['scheme'] ?? ''));

    if (!in_array($scheme, ['postgres', 'postgresql', 'pgsql'], true)) {
        throw new RuntimeException('DATABASE_URL precisa usar o protocolo PostgreSQL.');
    }

    $host = $parsed['host'] ?? '';
    $database = ltrim((string)($parsed['path'] ?? ''), '/');

    if ($host === '' || $database === '') {
        throw new RuntimeException('DATABASE_URL precisa informar host e banco.');
    }

    $config = [
        'driver' => 'pgsql',
        'dsn' => [
            'host' => $host,
            'port' => (string)($parsed['port'] ?? 5432),
            'dbname' => $database,
        ],
        'username' => isset($parsed['user']) ? urldecode((string)$parsed['user']) : '',
        'password' => isset($parsed['pass']) ? urldecode((string)$parsed['pass']) : '',
    ];

    if (!empty($parsed['query'])) {
        parse_str($parsed['query'], $queryParams);

        foreach ($queryParams as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $config['dsn'][(string)$key] = (string)$value;
        }
    }

    return $config;
}

function app_db_build_mysql_config(): array
{
    return [
        'driver' => 'mysql',
        'dsn' => [
            'host' => env_string('DB_HOST', '127.0.0.1'),
            'port' => (string)env_int('DB_PORT', 3307),
            'dbname' => env_string('DB_NAME', 'loja_bolos'),
            'charset' => 'utf8mb4',
        ],
        'username' => env_string('DB_USER', 'root'),
        'password' => env_string('DB_PASSWORD', ''),
    ];
}

function app_db_build_dsn(array $parts): string
{
    $driver = $parts['driver'] ?? '';
    $dsnParts = [];

    foreach (($parts['dsn'] ?? []) as $key => $value) {
        $sanitizedValue = str_replace(["\\", "'", ';', "\r", "\n"], ["\\\\", "\\'", '', '', ''], (string)$value);

        if (preg_match('/[=\\s]/', $sanitizedValue) === 1) {
            $sanitizedValue = "'" . $sanitizedValue . "'";
        }

        $dsnParts[] = $key . '=' . $sanitizedValue;
    }

    return $driver . ':' . implode(';', $dsnParts);
}

try {
    $config = app_db_build_pgsql_config() ?? app_db_build_mysql_config();
    $driver = $config['driver'] ?? '';

    if (!in_array($driver, PDO::getAvailableDrivers(), true)) {
        throw new RuntimeException('O driver PDO para ' . $driver . ' nao esta disponivel neste ambiente.');
    }

    $conn = new PDO(
        app_db_build_dsn($config),
        $config['username'] ?? null,
        $config['password'] ?? null,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (Throwable $e) {
    $conn = null;
    $connError = $e->getMessage();
}

function app_db_ready(): bool
{
    global $conn;

    return $conn instanceof PDO;
}

function app_require_database(): void
{
    if (app_db_ready()) {
        return;
    }

    http_response_code(503);
    exit('Banco de dados indisponivel. Configure DATABASE_URL ou as variaveis DB_* para usar esta funcionalidade.');
}

function app_db_error_message(): string
{
    global $connError;

    return $connError ?: 'Banco de dados indisponivel.';
}

function app_db(): PDO
{
    global $conn;

    if (!$conn instanceof PDO) {
        throw new RuntimeException(app_db_error_message());
    }

    return $conn;
}

function app_db_driver(): string
{
    return app_db()->getAttribute(PDO::ATTR_DRIVER_NAME);
}

function app_db_select_all(string $sql, array $params = []): array
{
    $stmt = app_db()->prepare($sql);
    $stmt->execute(array_values($params));

    return $stmt->fetchAll();
}

function app_db_select_one(string $sql, array $params = []): ?array
{
    $stmt = app_db()->prepare($sql);
    $stmt->execute(array_values($params));
    $row = $stmt->fetch();

    return $row === false ? null : $row;
}

function app_db_select_value(string $sql, array $params = []): mixed
{
    $stmt = app_db()->prepare($sql);
    $stmt->execute(array_values($params));
    $value = $stmt->fetchColumn();

    return $value === false ? null : $value;
}

function app_db_execute(string $sql, array $params = []): bool
{
    $stmt = app_db()->prepare($sql);

    return $stmt->execute(array_values($params));
}
?>
