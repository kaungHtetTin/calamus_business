<?php

/**
 * Load application settings from the root .env file.
 */
function loadEnvironment($path = null)
{
    static $loaded = false;

    if ($loaded) {
        return;
    }

    $loaded = true;
    $path = $path ?: __DIR__ . DIRECTORY_SEPARATOR . '.env';

    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $values = parse_ini_file($path, false, INI_SCANNER_RAW);

    if ($values === false) {
        throw new RuntimeException('Unable to parse environment file: ' . $path);
    }

    foreach ($values as $key => $value) {
        if (getenv($key) !== false || array_key_exists($key, $_ENV)) {
            continue;
        }

        $_ENV[$key] = $value;
        putenv($key . '=' . $value);
    }
}

/**
 * Read an environment setting with an optional default value.
 */
function envValue($key, $default = null)
{
    loadEnvironment();

    $value = $_ENV[$key] ?? getenv($key);

    return $value === false || $value === null ? $default : $value;
}

