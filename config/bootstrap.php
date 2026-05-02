<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional .env loader: lets server admins drop a /.env next to the project
// root instead of touching the webserver config. Lines like KEY=VALUE are
// pushed into getenv() unless the variable is already set in the environment.
$envFile = __DIR__ . '/../.env';
if (is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        $eq = strpos($line, '=');
        if ($eq === false) {
            continue;
        }
        $key = trim(substr($line, 0, $eq));
        $value = trim(substr($line, $eq + 1));
        if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
            $value = trim($value, "\"'");
        }
        if (getenv($key) === false || getenv($key) === '') {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/migrations.php';

if (isset($pdo) && $pdo instanceof PDO) {
    runTrackingMigrations($pdo);
}
