<?php

declare(strict_types=1);

function getMailConfig(): array
{
    return [
        'host' => getenv('SMTP_HOST') ?: '',
        'port' => (int) (getenv('SMTP_PORT') ?: 587),
        'username' => getenv('SMTP_USER') ?: '',
        'password' => getenv('SMTP_PASS') ?: '',
        'encryption' => getenv('SMTP_ENCRYPTION') ?: 'tls',
        'from_email' => getenv('MAIL_FROM') ?: 'no-reply@example.com',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'S-ART / Shary on Tour',
    ];
}

function sendTicketMail(PDO $pdo, string $email, string $ticketToken, ?string $firstName = null): array
{
    $config = getMailConfig();

    if ($config['host'] === '' || $config['username'] === '' || $config['password'] === '') {
        logTicketMail($pdo, $email, $ticketToken, 'mail_skipped_config_missing', 'SMTP configuration missing.');
        return ['success' => false, 'status' => 'mail_skipped_config_missing'];
    }

    // Placeholder for PHPMailer or native mail integration.
    // Keep non-fatal if sending fails.
    $subject = 'Dein S-ART Ticket';
    $namePart = $firstName ? "Hallo {$firstName},\n\n" : "Hallo,\n\n";
    $message = $namePart . "dein digitales Ticket ist bereit: "
        . (getenv('APP_URL') ?: '') . '/ticket.php?token=' . urlencode($ticketToken);

    $headers = 'From: ' . $config['from_name'] . ' <' . $config['from_email'] . '>';
    $sent = @mail($email, $subject, $message, $headers);

    if ($sent) {
        logTicketMail($pdo, $email, $ticketToken, 'mail_sent', null);
        return ['success' => true, 'status' => 'mail_sent'];
    }

    logTicketMail($pdo, $email, $ticketToken, 'mail_failed', 'mail() returned false.');
    return ['success' => false, 'status' => 'mail_failed'];
}

function logTicketMail(PDO $pdo, string $email, string $ticketToken, string $status, ?string $error = null): void
{
    $subscriberStmt = $pdo->prepare('SELECT id FROM newsletter_subscribers WHERE email = :email LIMIT 1');
    $subscriberStmt->execute(['email' => $email]);
    $subscriber = $subscriberStmt->fetch();

    $stmt = $pdo->prepare(
        'INSERT INTO ticket_logs (subscriber_id, email, ticket_token, status, error_message)
         VALUES (:subscriber_id, :email, :ticket_token, :status, :error_message)'
    );

    $stmt->execute([
        'subscriber_id' => $subscriber['id'] ?? null,
        'email' => $email,
        'ticket_token' => $ticketToken,
        'status' => $status,
        'error_message' => $error,
    ]);

    if ($status === 'mail_sent') {
        $update = $pdo->prepare('UPDATE newsletter_subscribers SET ticket_sent_at = NOW() WHERE email = :email');
        $update->execute(['email' => $email]);
    }
}
