<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

const ADMIN_PASSWORD_HASH = '$2y$12$vGzOcdKMp2j/UBonhVkYp./vBceTWRZrMGX9GcrJAEgdPB23n7kja';

function isAdminLoggedIn(): bool
{
    return !empty($_SESSION['admin_logged_in']);
}

function requireAdminLogin(): void
{
    if (isAdminLoggedIn()) {
        return;
    }

    $target = $_SERVER['REQUEST_URI'] ?? '/admin/';
    header('Location: /admin/login.php?redirect=' . rawurlencode($target));
    exit;
}
