<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require __DIR__ . '/includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$location = trim((string) ($_POST['location_optional'] ?? ''));
$consent = isset($_POST['consent_privacy']) && $_POST['consent_privacy'] === '1';
$csrf = $_POST['csrf_token'] ?? null;

if (!verifyCsrf(is_string($csrf) ? $csrf : null)) {
    http_response_code(400);
    exit('Ungültige Anfrage (CSRF).');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /index.php?nl=invalid#newsletter');
    exit;
}

if (!$consent) {
    header('Location: /index.php?nl=consent#newsletter');
    exit;
}

$existing = fetchOne('SELECT id FROM newsletter_subscribers WHERE email=:e LIMIT 1', ['e' => $email]);
if (!$existing) {
    $stmt = $pdo->prepare(
        'INSERT INTO newsletter_subscribers (email, location_optional, consent_privacy, ip_address, user_agent)
         VALUES (:e, :l, 1, :ip, :ua)'
    );
    $stmt->execute([
        'e' => $email,
        'l' => $location !== '' ? $location : null,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
    ]);
}

header('Location: /index.php?nl=ok#newsletter');
exit;
