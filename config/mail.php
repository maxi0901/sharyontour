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

/**
 * Newsletter SMTP config. Credentials come exclusively from env vars
 * (NEWSLETTER_SMTP_USER / NEWSLETTER_SMTP_PASS) so that the password is
 * never committed to the repository. Set them in /etc/environment, in the
 * webserver vhost, or via a .env loader before bootstrap.php is included.
 */
function getNewsletterMailConfig(): array
{
    return [
        'host'       => getenv('NEWSLETTER_SMTP_HOST') ?: 'mxe989.netcup.net',
        'port'       => (int) (getenv('NEWSLETTER_SMTP_PORT') ?: 465),
        'username'   => getenv('NEWSLETTER_SMTP_USER') ?: '',
        'password'   => getenv('NEWSLETTER_SMTP_PASS') ?: '',
        'encryption' => getenv('NEWSLETTER_SMTP_ENCRYPTION') ?: 'ssl',
        'from_email' => getenv('NEWSLETTER_FROM_EMAIL') ?: 'Newsletter@sharyontour.de',
        'from_name'  => getenv('NEWSLETTER_FROM_NAME') ?: 'Shary on Tour',
    ];
}

/**
 * Generic SMTP dispatcher. Returns ['success' => bool, 'error' => ?string].
 * Caller is responsible for logging.
 */
function dispatchMail(array $config, string $to, ?string $toName, string $subject, string $html, string $text = ''): array
{
    if (($config['username'] ?? '') === '' || ($config['password'] ?? '') === '') {
        return ['success' => false, 'error' => 'SMTP credentials not configured (env vars missing)'];
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->Port       = (int) $config['port'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = ($config['encryption'] ?? 'ssl') === 'ssl'
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->CharSet    = 'UTF-8';
        $mail->Encoding   = 'base64';

        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addReplyTo($config['from_email'], $config['from_name']);
        $mail->addAddress($to, (string) ($toName ?? ''));

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html;
        $mail->AltBody = $text !== '' ? $text : strip_tags($html);

        $mail->send();
        return ['success' => true, 'error' => null];
    } catch (PHPMailerException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    } catch (Throwable $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function buildNewsletterConfirmMail(string $email, string $confirmToken): array
{
    $confirmUrl = appUrl('/newsletter-confirm.php?token=' . urlencode($confirmToken));
    $subject = 'Bitte bestätige deine Newsletter-Anmeldung · Shary on Tour';

    $emailHtml = e($email);
    $confirmUrlHtml = e($confirmUrl);

    $html = <<<HTML
<!doctype html>
<html lang="de">
<head><meta charset="utf-8"><title>Newsletter bestätigen</title></head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#1a1a1a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:24px 0;">
  <tr><td align="center">
    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
      <tr><td style="padding:32px 32px 8px 32px;">
        <p style="margin:0;color:#888;font-size:13px;letter-spacing:1px;">SHARY ON TOUR · NEWSLETTER</p>
        <h1 style="margin:8px 0 16px 0;font-size:24px;line-height:1.2;">Bestätige deine Anmeldung</h1>
        <p style="margin:0 0 16px 0;font-size:16px;">Hallo,</p>
        <p style="margin:0 0 24px 0;font-size:16px;">du hast dich mit <strong>{$emailHtml}</strong> für den Shary-on-Tour-Newsletter angemeldet. Um die Anmeldung abzuschließen, klick bitte auf den folgenden Button:</p>
      </td></tr>
      <tr><td style="padding:0 32px 24px 32px;">
        <a href="{$confirmUrlHtml}" style="display:inline-block;background:#ff2d8e;color:#ffffff;text-decoration:none;padding:14px 22px;border-radius:8px;font-weight:600;">Anmeldung bestätigen →</a>
      </td></tr>
      <tr><td style="padding:0 32px 24px 32px;border-top:1px solid #eee;">
        <p style="margin:16px 0 4px 0;font-size:13px;color:#777;">Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser:</p>
        <p style="margin:0;font-size:13px;color:#555;word-break:break-all;"><a href="{$confirmUrlHtml}" style="color:#ff2d8e;">{$confirmUrlHtml}</a></p>
      </td></tr>
      <tr><td style="padding:16px 32px 32px 32px;background:#fafafa;">
        <p style="margin:0;font-size:12px;color:#888;">Du hast diese E-Mail erhalten, weil sich jemand mit deiner Adresse angemeldet hat. Wenn du das nicht warst, ignoriere die Mail einfach – ohne Bestätigung wirst du keine weiteren Newsletter erhalten.</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
HTML;

    $text = "Hallo,\r\n\r\n"
          . "du hast dich mit {$email} für den Shary-on-Tour-Newsletter angemeldet.\r\n"
          . "Bitte bestätige deine Anmeldung über folgenden Link:\r\n\r\n"
          . $confirmUrl . "\r\n\r\n"
          . "Falls du das nicht warst, ignoriere diese E-Mail.\r\n\r\n"
          . "Shary on Tour";

    return [$html, $text, $subject];
}

function sendNewsletterConfirmationMail(string $email, string $confirmToken): array
{
    $config = getNewsletterMailConfig();
    [$html, $text, $subject] = buildNewsletterConfirmMail($email, $confirmToken);
    return dispatchMail($config, $email, null, $subject, $html, $text);
}

/**
 * Wrap a campaign body with a footer that contains the per-recipient
 * unsubscribe link. The body itself is provided by the admin and is
 * passed through unmodified (admin trusts itself).
 */
function buildNewsletterCampaignBodies(string $bodyHtml, ?string $bodyText, string $unsubscribeToken): array
{
    $unsubscribeUrl = appUrl('/newsletter-unsubscribe.php?token=' . urlencode($unsubscribeToken));
    $unsubscribeUrlHtml = e($unsubscribeUrl);

    $footerHtml = <<<HTML
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:32px;border-top:1px solid #eee;">
  <tr><td style="padding:18px 0 4px 0;font-size:12px;color:#888;text-align:center;">
    Du erhältst diese Mail als bestätigter Abonnent des Shary-on-Tour-Newsletters.
  </td></tr>
  <tr><td style="padding:4px 0 24px 0;font-size:12px;color:#888;text-align:center;">
    <a href="{$unsubscribeUrlHtml}" style="color:#888;">Vom Newsletter abmelden</a>
  </td></tr>
</table>
HTML;

    $html = $bodyHtml . $footerHtml;

    $text = ($bodyText !== null && $bodyText !== '') ? $bodyText : strip_tags($bodyHtml);
    $text .= "\r\n\r\n---\r\nDu erhältst diese Mail als bestätigter Abonnent des Shary-on-Tour-Newsletters.\r\n"
          .  "Abmelden: " . $unsubscribeUrl . "\r\n";

    return [$html, $text];
}

function sendNewsletterCampaignMail(string $email, string $subject, string $bodyHtml, ?string $bodyText, string $unsubscribeToken): array
{
    $config = getNewsletterMailConfig();
    [$html, $text] = buildNewsletterCampaignBodies($bodyHtml, $bodyText, $unsubscribeToken);
    return dispatchMail($config, $email, null, $subject, $html, $text);
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
