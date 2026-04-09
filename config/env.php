<?php

function env_value(string $name, mixed $default = null): mixed
{
    if (array_key_exists($name, $_ENV)) {
        return $_ENV[$name];
    }

    if (array_key_exists($name, $_SERVER)) {
        return $_SERVER[$name];
    }

    $value = getenv($name);

    return $value === false ? $default : $value;
}

function env_string(string $name, string $default = ''): string
{
    $value = env_value($name, $default);

    if ($value === null) {
        return $default;
    }

    return is_string($value) ? $value : (string)$value;
}

function env_int(string $name, int $default = 0): int
{
    $value = env_value($name, null);

    if ($value === null || $value === '') {
        return $default;
    }

    return (int)$value;
}
?>
