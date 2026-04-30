<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function appUrl(string $path = ''): string
{
    $envUrl = rtrim(getenv('APP_URL') ?: '', '/');
    if ($envUrl !== '') {
        return $envUrl . $path;
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . $path;
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

function formatDateLong(?string $date): string
{
    if (!$date) {
        return '';
    }
    $months = [1=>'Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'];
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dt) return $date;
    return $dt->format('d') . '. ' . $months[(int)$dt->format('n')] . ' ' . $dt->format('Y');
}

function isActivePage(string $page): string
{
    return basename($_SERVER['PHP_SELF']) === $page ? 'is-active' : '';
}

function createSlug(string $text): string
{
    $text = strtolower(trim($text));
    $text = str_replace(['ä','ö','ü','ß'], ['ae','oe','ue','ss'], $text);
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    return trim((string) $text, '-') ?: 'item-' . bin2hex(random_bytes(3));
}


function hasColumn(string $table, string $column): bool
{
    global $pdo;
    $sql = 'SELECT COUNT(*) AS c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column';
    $row = fetchOne($sql, ['table' => $table, 'column' => $column]);
    return (int) ($row['c'] ?? 0) > 0;
}

function generateUuidV4(): string
{
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function getOpeningEvent(): ?array
{
    $hasIsOpening = hasColumn('events', 'is_opening');

    if ($hasIsOpening) {
        return fetchOne("SELECT * FROM events WHERE is_opening=1 AND status<>'past' ORDER BY event_date ASC LIMIT 1");
    }

    return fetchOne("SELECT * FROM events WHERE status='upcoming' ORDER BY event_date ASC LIMIT 1");
}

function countTicketsForEvent(int $eventId): int
{
    $row = fetchOne('SELECT COUNT(*) AS c FROM tickets WHERE event_id=:e AND status="active"', ['e' => $eventId]);
    return (int) ($row['c'] ?? 0);
}

function getTicketByTicketId(string $ticketId): ?array
{
    $isOpeningSelect = hasColumn('events', 'is_opening') ? 'e.is_opening' : '0 AS is_opening';

    return fetchOne(
        "SELECT t.*, e.title AS event_title, e.event_date, e.event_time, e.city, {$isOpeningSelect}
         FROM tickets t INNER JOIN events e ON e.id = t.event_id
         WHERE t.ticket_id=:tid LIMIT 1",
        ['tid' => $ticketId]
    );
}

function findTicketByEmailAndEvent(int $eventId, string $email): ?array
{
    return fetchOne('SELECT * FROM tickets WHERE event_id=:e AND email=:m LIMIT 1', ['e' => $eventId, 'm' => $email]);
}

function logTicketEvent(?string $ticketId, ?string $email, string $status, ?string $error = null): void
{
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO ticket_logs (ticket_id, email, status, error_message) VALUES (:t,:e,:s,:err)');
    $stmt->execute(['t' => $ticketId, 'e' => $email, 's' => $status, 'err' => $error]);
}
