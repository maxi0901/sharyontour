<?php

declare(strict_types=1);

/**
 * Database connection configuration for Netcup-compatible shared hosting.
 * Adjust values via environment variables or host-level include overrides.
 */
return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => (int) (getenv('DB_PORT') ?: 3306),
    'dbname' => getenv('DB_NAME') ?: 'sharyontour',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') ?: '',
    'charset' => 'utf8mb4',
];
