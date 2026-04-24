<?php

declare(strict_types=1);

session_start();

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = require __DIR__ . '/../config/database.php';
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['dbname'],
        $config['charset']
    );

    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return !empty($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function old(string $key, string $default = ''): string
{
    return $_SESSION['old'][$key] ?? $default;
}

function store_old(array $input): void
{
    $_SESSION['old'] = $input;
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

function normalize_email(string $email): string
{
    return mb_strtolower(trim($email));
}

function generate_token(int $length = 32): string
{
    return bin2hex(random_bytes($length / 2));
}

function fetch_events(string $status = 'upcoming', int $limit = 6): array
{
    $sql = 'SELECT * FROM events WHERE status = :status ORDER BY event_date ASC, event_time ASC LIMIT :limit';
    if ($status === 'past') {
        $sql = 'SELECT * FROM events WHERE status = :status ORDER BY event_date DESC, event_time DESC LIMIT :limit';
    }

    $stmt = db()->prepare($sql);
    $stmt->bindValue(':status', $status);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function fetch_featured_artworks(int $limit = 6): array
{
    $stmt = db()->prepare('SELECT * FROM artworks WHERE is_visible = 1 ORDER BY sort_order ASC, created_at DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function fetch_current_location(): ?array
{
    $stmt = db()->query("SELECT * FROM tour_locations WHERE status = 'current' ORDER BY date_from DESC LIMIT 1");
    $row = $stmt->fetch();

    if ($row) {
        return $row;
    }

    $stmt = db()->query("SELECT * FROM tour_locations WHERE status = 'upcoming' ORDER BY date_from ASC LIMIT 1");
    $next = $stmt->fetch();

    return $next ?: null;
}

function validate_newsletter_submission(array $input): array
{
    $errors = [];
    $email = normalize_email($input['email'] ?? '');

    if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Bitte gib eine gültige E-Mail-Adresse ein.';
    }

    if (($input['consent_privacy'] ?? '') !== '1') {
        $errors['consent_privacy'] = 'Bitte stimme der Datenschutzerklärung zu.';
    }

    return $errors;
}

function upsert_subscriber(array $input): array
{
    $pdo = db();
    $email = normalize_email($input['email']);
    $firstName = trim((string) ($input['first_name'] ?? ''));
    $source = trim((string) ($input['source'] ?? 'website'));
    $token = generate_token(32);

    $stmt = $pdo->prepare('SELECT id, ticket_token FROM newsletter_subscribers WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $existing = $stmt->fetch();

    if ($existing) {
        return [
            'subscriber_id' => (int) $existing['id'],
            'ticket_token' => (string) $existing['ticket_token'],
            'is_new' => false,
        ];
    }

    $insert = $pdo->prepare(
        'INSERT INTO newsletter_subscribers
        (email, first_name, source, consent_privacy, double_opt_in_token, ticket_token, created_at, ip_address, user_agent)
        VALUES (:email, :first_name, :source, :consent_privacy, :double_opt_in_token, :ticket_token, NOW(), :ip_address, :user_agent)'
    );

    $insert->execute([
        'email' => $email,
        'first_name' => $firstName !== '' ? $firstName : null,
        'source' => $source,
        'consent_privacy' => 1,
        'double_opt_in_token' => generate_token(48),
        'ticket_token' => $token,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
    ]);

    return [
        'subscriber_id' => (int) $pdo->lastInsertId(),
        'ticket_token' => $token,
        'is_new' => true,
    ];
}

function ticket_link(string $token): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return sprintf('%s://%s/ticket.php?token=%s', $scheme, $host, urlencode($token));
}

function log_ticket_status(int $subscriberId, string $email, string $token, string $status, ?string $error = null): void
{
    $stmt = db()->prepare(
        'INSERT INTO ticket_logs (subscriber_id, email, ticket_token, sent_at, status, error_message)
         VALUES (:subscriber_id, :email, :ticket_token, NOW(), :status, :error_message)'
    );

    $stmt->execute([
        'subscriber_id' => $subscriberId,
        'email' => $email,
        'ticket_token' => $token,
        'status' => $status,
        'error_message' => $error,
    ]);
}

/**
 * Optional mail sending hook; compatible with PHPMailer if installed later.
 */
function send_ticket_email(array $subscriber, string $ticketUrl): bool
{
    $mailConfig = require __DIR__ . '/../config/mail.php';
    $subject = 'Dein S-Art Tour Ticket';
    $recipientName = trim((string) ($subscriber['first_name'] ?? ''));
    $greetingName = $recipientName !== '' ? $recipientName : $subscriber['email'];
    $message = "Hallo {$greetingName},\n\nvielen Dank für dein Interesse an S-Art Tour. "
        . "Dein digitales Ticket findest du hier:\n{$ticketUrl}\n\n"
        . "Hinweis: Kein Kauf erforderlich.\n\nLiebe Grüße\nS-Art Team";

    if (!empty($mailConfig['smtp']['enabled'])) {
        // Placeholder for PHPMailer SMTP flow.
        return false;
    }

    $headers = sprintf('From: %s <%s>', $mailConfig['from_name'], $mailConfig['from_email']);

    return @mail($subscriber['email'], $subject, $message, $headers);
}

function require_admin_bootstrap(): void
{
    // Placeholder for future auth middleware.
    // Left intentionally lightweight so login can be layered in later.
}
