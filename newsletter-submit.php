<?php

declare(strict_types=1);

require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/csrf.php';
require __DIR__ . '/config/mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$firstName = trim((string) ($_POST['first_name'] ?? ''));
$source = trim((string) ($_POST['source'] ?? 'homepage'));
$consent = isset($_POST['consent_privacy']) && $_POST['consent_privacy'] === '1';
$csrfToken = $_POST['csrf_token'] ?? null;

if (!verifyCsrf(is_string($csrfToken) ? $csrfToken : null)) {
    http_response_code(400);
    exit('Ungültige Anfrage (CSRF).');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    exit('Bitte eine gültige E-Mail-Adresse angeben.');
}

if (!$consent) {
    http_response_code(422);
    exit('Datenschutz-Zustimmung ist erforderlich.');
}

$result = upsertNewsletterSubscriber($pdo, [
    'email' => $email,
    'first_name' => $firstName !== '' ? $firstName : null,
    'source' => $source !== '' ? $source : 'homepage',
    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
]);

sendTicketMail($pdo, $email, $result['ticket_token'], $firstName !== '' ? $firstName : null);

$message = $result['created']
    ? 'Danke! Dein Ticket wurde erstellt.'
    : 'Diese E-Mail ist bereits registriert. Dein vorhandenes Ticket wurde geladen.';

header('Location: /ticket.php?token=' . urlencode($result['ticket_token']) . '&msg=' . urlencode($message));
exit;
