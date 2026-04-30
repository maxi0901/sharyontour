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
        'from_email' => getenv('MAIL_FROM') ?: 'no-reply@s-art.work',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'S-ART · Shary on Tour',
    ];
}

function sendTicketMail(PDO $pdo, string $email, string $ticketIdValue, ?string $name = null, ?array $event = null): array
{
    $config = getMailConfig();

    $ticketUrl = appUrl('/ticket.php?id=' . urlencode($ticketIdValue));
    $icsUrl = appUrl('/ticket-ics.php?id=' . urlencode($ticketIdValue));
    $pdfUrl = appUrl('/ticket-pdf.php?id=' . urlencode($ticketIdValue));
    $walletUrl = appUrl('/ticket-wallet.php?id=' . urlencode($ticketIdValue));

    $eventTitle = $event['title'] ?? 'S-ART Event';
    $eventDate = isset($event['event_date']) ? formatDate($event['event_date']) : '';
    $hello = $name ? "Hallo {$name}," : 'Hallo,';

    $subject = 'Dein S-ART Gratis-Ticket · ' . $eventTitle;

    $body = $hello . "\r\n\r\n"
          . "dein digitales Ticket für \"{$eventTitle}\" ist bereit:\r\n\r\n"
          . "→ Ticket aufrufen: {$ticketUrl}\r\n"
          . "→ Kalender (.ics): {$icsUrl}\r\n"
          . "→ Apple / Google Wallet: {$walletUrl}\r\n"
          . "→ PDF: {$pdfUrl}\r\n\r\n"
          . "Datum: {$eventDate}\r\n"
          . "Ort: Kassel — der genaue Standort wird rechtzeitig bekanntgegeben.\r\n\r\n"
          . "Ticket-ID: {$ticketIdValue}\r\n\r\n"
          . "Bis bald!\r\nS-ART · Shary on Tour";

    $headers = 'From: ' . $config['from_name'] . ' <' . $config['from_email'] . ">\r\n"
             . "Content-Type: text/plain; charset=utf-8\r\n";

    if ($config['username'] === '' || $config['password'] === '') {
        logTicketEvent($ticketIdValue, $email, 'mail_skipped_config_missing', null);
        return ['success' => false, 'status' => 'mail_skipped_config_missing'];
    }

    $sent = @mail($email, $subject, $body, $headers);

    if ($sent) {
        logTicketEvent($ticketIdValue, $email, 'mail_sent', null);
        return ['success' => true, 'status' => 'mail_sent'];
    }

    logTicketEvent($ticketIdValue, $email, 'mail_failed', 'mail() returned false.');
    return ['success' => false, 'status' => 'mail_failed'];
}
