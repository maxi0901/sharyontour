<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/migrations.php';

if (isset($pdo) && $pdo instanceof PDO) {
    runTrackingMigrations($pdo);
}
