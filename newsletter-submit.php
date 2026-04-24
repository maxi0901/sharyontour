<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    set_flash('error', 'Ungültige Anfrage. Bitte Formular neu laden.');
    header('Location: /index.php');
    exit;
}

$input = [
    'email' => $_POST['email'] ?? '',
    'first_name' => $_POST['first_name'] ?? '',
    'consent_privacy' => $_POST['consent_privacy'] ?? '',
    'source' => $_POST['source'] ?? 'website',
];

store_old($input);
$errors = validate_newsletter_submission($input);
if ($errors) {
    set_flash('error', implode(' ', array_values($errors)));
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/index.php'));
    exit;
}

try {
    $subscriberResult = upsert_subscriber($input);
    $email = normalize_email($input['email']);
    $ticketUrl = ticket_link($subscriberResult['ticket_token']);

    $mailSent = send_ticket_email([
        'email' => $email,
        'first_name' => trim((string) $input['first_name']),
    ], $ticketUrl);

    if ($mailSent) {
        db()->prepare('UPDATE newsletter_subscribers SET ticket_sent_at = NOW() WHERE id = :id')->execute(['id' => $subscriberResult['subscriber_id']]);
        log_ticket_status($subscriberResult['subscriber_id'], $email, $subscriberResult['ticket_token'], 'sent');
    } else {
        log_ticket_status($subscriberResult['subscriber_id'], $email, $subscriberResult['ticket_token'], 'queued', 'Mailversand nicht aktiv oder fehlgeschlagen.');
    }

    clear_old();
    set_flash('success', 'Danke! Dein Ticket ist bereit: ' . $ticketUrl);
} catch (Throwable $exception) {
    error_log('Newsletter submit failed: ' . $exception->getMessage());
    set_flash('error', 'Es gab ein technisches Problem. Bitte später erneut versuchen.');
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/index.php'));
exit;
