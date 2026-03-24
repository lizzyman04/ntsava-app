<?php

/**
 * Database Bootstrap
 */

require_once __DIR__ . '/../../vendor/autoload.php';

define('BASE_PATH', dirname(__DIR__, 2));

$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }

            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }

            $key = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));

            $len = strlen($value);
            if (($len > 0 && $value[0] === '"' && $value[$len - 1] === '"') ||
                ($len > 0 && $value[0] === "'" && $value[$len - 1] === "'")) {
                $value = substr($value, 1, -1);
            }

            $value = preg_replace_callback('/\${([a-zA-Z_][a-zA-Z0-9_]*)}/', function ($matches) {
                $val = getenv($matches[1]);
                if ($val === false) {
                    $val = $_ENV[$matches[1]] ?? '';
                }
                return $val;
            }, $value);

            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('base_path')) {
    function base_path($path = '')
    {
        return BASE_PATH . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('dd')) {
    function dd(...$vars): void
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
        die(1);
    }
}