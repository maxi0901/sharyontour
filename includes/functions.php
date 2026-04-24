<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function appUrl(string $path = ''): string
{
    return rtrim(getenv('APP_URL') ?: '', '/') . $path;
}

function fetchAll(string $sql, array $params = []): array
{
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetchOne(string $sql, array $params = []): ?array
{
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ?: null;
}

function formatDate(?string $date): string
{
    if (!$date) {
        return '';
    }

    $dt = DateTime::createFromFormat('Y-m-d', $date);
    return $dt ? $dt->format('d.m.Y') : $date;
}

function isActivePage(string $page): string
{
    return basename($_SERVER['PHP_SELF']) === $page ? 'is-active' : '';
}

function createSlug(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    return trim((string) $text, '-') ?: 'item-' . bin2hex(random_bytes(3));
}

function upsertNewsletterSubscriber(PDO $pdo, array $data): array
{
    $existing = fetchOne('SELECT * FROM newsletter_subscribers WHERE email = :email LIMIT 1', ['email' => $data['email']]);

    if ($existing) {
        return ['created' => false, 'ticket_token' => $existing['ticket_token'], 'id' => (int) $existing['id']];
    }

    $ticketToken = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare(
        'INSERT INTO newsletter_subscribers
        (email, first_name, source, consent_privacy, ticket_token, ip_address, user_agent)
        VALUES (:email, :first_name, :source, :consent_privacy, :ticket_token, :ip_address, :user_agent)'
    );

    $stmt->execute([
        'email' => $data['email'],
        'first_name' => $data['first_name'],
        'source' => $data['source'],
        'consent_privacy' => 1,
        'ticket_token' => $ticketToken,
        'ip_address' => $data['ip_address'],
        'user_agent' => $data['user_agent'],
    ]);

    return ['created' => true, 'ticket_token' => $ticketToken, 'id' => (int) $pdo->lastInsertId()];
}
