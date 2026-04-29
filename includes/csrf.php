<?php

declare(strict_types=1);

function ensureCsrfSessionStarted(): bool
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return true;
    }

    if (headers_sent()) {
        return false;
    }

    return session_start();
}

function csrfToken(): string
{
    if (!ensureCsrfSessionStarted()) {
        throw new RuntimeException('Session ist nicht verfügbar. CSRF-Token kann nicht erstellt werden.');
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function verifyCsrf(?string $token): bool
{
    if (!ensureCsrfSessionStarted()) {
        return false;
    }

    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
