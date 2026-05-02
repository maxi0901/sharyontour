<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../lib/mailer-autoload.php';
}

function getMailConfig(): array
{
    return [
        'host'       => getenv('SMTP_HOST') ?: 'mxe989.netcup.net',
        'port'       => (int) (getenv('SMTP_PORT') ?: 465),
        'username'   => getenv('SMTP_USER') ?: 'tickets@sharyontour.de',
        'password'   => getenv('SMTP_PASS') ?: '2yu!816Qn',
        'encryption' => getenv('SMTP_ENCRYPTION') ?: 'ssl',
        'from_email' => getenv('MAIL_FROM') ?: 'tickets@sharyontour.de',
        'from_name'  => getenv('MAIL_FROM_NAME') ?: 'S-ART · Shary on Tour',
    ];
}

function buildTicketMailBodies(string $ticketIdValue, ?string $name, ?array $event): array
{
    $eventTitle = $event['title'] ?? 'S-ART Event';
    $eventDate  = isset($event['event_date']) ? formatDate($event['event_date']) : '';
    $hello      = $name ? "Hallo {$name}," : 'Hallo,';

    $tid = urlencode($ticketIdValue);
    $ticketUrl = appUrl('/track-click.php?id=' . $tid . '&t=ticket');
    $icsUrl    = appUrl('/track-click.php?id=' . $tid . '&t=ics');
    $pdfUrl    = appUrl('/track-click.php?id=' . $tid . '&t=pdf');
    $walletUrl = appUrl('/track-click.php?id=' . $tid . '&t=wallet');
    $pixelUrl  = appUrl('/track-open.php?id=' . $tid);

    $subject = 'Dein S-ART Gratis-Ticket · ' . $eventTitle;

    $text = $hello . "\r\n\r\n"
          . "dein digitales Ticket für \"{$eventTitle}\" ist bereit:\r\n\r\n"
          . "→ Ticket aufrufen: {$ticketUrl}\r\n"
          . "→ Kalender (.ics): {$icsUrl}\r\n"
          . "→ Apple / Google Wallet: {$walletUrl}\r\n"
          . "→ PDF: {$pdfUrl}\r\n\r\n"
          . "Datum: {$eventDate}\r\n"
          . "Ort: Kassel — der genaue Standort wird rechtzeitig bekanntgegeben.\r\n\r\n"
          . "Ticket-ID: {$ticketIdValue}\r\n\r\n"
          . "Bis bald!\r\nS-ART · Shary on Tour";

    $eventTitleHtml = e($eventTitle);
    $eventDateHtml  = e($eventDate);
    $helloHtml      = e($hello);
    $tidHtml        = e($ticketIdValue);

    $html = <<<HTML
<!doctype html>
<html lang="de">
<head><meta charset="utf-8"><title>{$eventTitleHtml}</title></head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#1a1a1a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:24px 0;">
  <tr><td align="center">
    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
      <tr><td style="padding:32px 32px 8px 32px;">
        <p style="margin:0;color:#888;font-size:13px;letter-spacing:1px;">S-ART · SHARY ON TOUR</p>
        <h1 style="margin:8px 0 16px 0;font-size:26px;line-height:1.2;">Dein Gratis-Ticket ist bereit</h1>
        <p style="margin:0 0 16px 0;font-size:16px;">{$helloHtml}</p>
        <p style="margin:0 0 24px 0;font-size:16px;">dein digitales Ticket für <strong>{$eventTitleHtml}</strong> wartet auf dich.</p>
      </td></tr>
      <tr><td style="padding:0 32px 16px 32px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
          <tr><td style="padding:8px 0;">
            <a href="{$ticketUrl}" style="display:inline-block;background:#ff2d8e;color:#ffffff;text-decoration:none;padding:14px 22px;border-radius:8px;font-weight:600;">Ticket aufrufen →</a>
          </td></tr>
        </table>
      </td></tr>
      <tr><td style="padding:0 32px 24px 32px;">
        <p style="margin:0 0 8px 0;font-size:14px;color:#555;">Weitere Optionen:</p>
        <ul style="margin:0;padding:0 0 0 18px;font-size:14px;color:#333;line-height:1.7;">
          <li><a href="{$pdfUrl}" style="color:#ff2d8e;">PDF herunterladen</a></li>
          <li><a href="{$icsUrl}" style="color:#ff2d8e;">Termin in Kalender (.ics)</a></li>
          <li><a href="{$walletUrl}" style="color:#ff2d8e;">Apple / Google Wallet</a></li>
        </ul>
      </td></tr>
      <tr><td style="padding:0 32px 24px 32px;border-top:1px solid #eee;">
        <p style="margin:16px 0 4px 0;font-size:14px;color:#555;"><strong>Datum:</strong> {$eventDateHtml}</p>
        <p style="margin:0 0 4px 0;font-size:14px;color:#555;"><strong>Ort:</strong> Kassel — der genaue Standort wird rechtzeitig bekanntgegeben.</p>
        <p style="margin:8px 0 0 0;font-size:12px;color:#888;">Ticket-ID: <code>{$tidHtml}</code></p>
      </td></tr>
      <tr><td style="padding:16px 32px 32px 32px;background:#fafafa;">
        <p style="margin:0;font-size:12px;color:#888;">Bis bald!<br>S-ART · Shary on Tour</p>
      </td></tr>
    </table>
    <img src="{$pixelUrl}" width="1" height="1" alt="" style="display:block;border:0;outline:none;">
  </td></tr>
</table>
</body>
</html>
HTML;

    return [$html, $text, $subject];
}

function sendTicketMail(PDO $pdo, string $email, string $ticketIdValue, ?string $name = null, ?array $event = null): array
{
    $config = getMailConfig();

    if ($config['username'] === '' || $config['password'] === '') {
        logTicketEvent($ticketIdValue, $email, 'mail_skipped_config_missing', null);
        return ['success' => false, 'status' => 'mail_skipped_config_missing'];
    }

    [$html, $text, $subject] = buildTicketMailBodies($ticketIdValue, $name, $event);

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->Port       = $config['port'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['encryption'] === 'ssl'
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->CharSet    = 'UTF-8';
        $mail->Encoding   = 'base64';

        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addReplyTo($config['from_email'], $config['from_name']);
        $mail->addAddress($email, (string) ($name ?? ''));

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html;
        $mail->AltBody = $text;

        $mail->send();
    } catch (PHPMailerException $e) {
        logTicketEvent($ticketIdValue, $email, 'mail_failed', $e->getMessage());
        return ['success' => false, 'status' => 'mail_failed', 'error' => $e->getMessage()];
    } catch (Throwable $e) {
        logTicketEvent($ticketIdValue, $email, 'mail_failed', $e->getMessage());
        return ['success' => false, 'status' => 'mail_failed', 'error' => $e->getMessage()];
    }

    try {
        $pdo->prepare('UPDATE tickets SET mail_sent_at = NOW() WHERE ticket_id = :tid')
            ->execute(['tid' => $ticketIdValue]);
    } catch (Throwable $e) {
        error_log('Failed to update mail_sent_at: ' . $e->getMessage());
    }

    logTicketEvent($ticketIdValue, $email, 'mail_sent', null);
    return ['success' => true, 'status' => 'mail_sent'];
}
