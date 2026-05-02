<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require __DIR__ . '/includes/csrf.php';
require __DIR__ . '/includes/newsletter-log.php';
require __DIR__ . '/config/mail.php';

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
    logNewsletterEvent('signup_invalid_email', ['email' => $email]);
    header('Location: /index.php?nl=invalid#newsletter');
    exit;
}

if (!$consent) {
    logNewsletterEvent('signup_no_consent', ['email' => $email]);
    header('Location: /index.php?nl=consent#newsletter');
    exit;
}

$existing = fetchOne('SELECT * FROM newsletter_subscribers WHERE email=:e LIMIT 1', ['e' => $email]);

$confirmToken = bin2hex(random_bytes(32));
$unsubscribeToken = bin2hex(random_bytes(32));

$hasStatus = hasColumn('newsletter_subscribers', 'status');
$hasConfirmToken = hasColumn('newsletter_subscribers', 'confirm_token');
$hasUnsubscribeToken = hasColumn('newsletter_subscribers', 'unsubscribe_token');

try {
    if (!$existing) {
        if ($hasStatus && $hasConfirmToken && $hasUnsubscribeToken) {
            $stmt = $pdo->prepare(
                'INSERT INTO newsletter_subscribers
                   (email, location_optional, consent_privacy, status, confirm_token, unsubscribe_token, ip_address, user_agent)
                 VALUES (:e, :l, 1, "pending", :ct, :ut, :ip, :ua)'
            );
            $stmt->execute([
                'e' => $email,
                'l' => $location !== '' ? $location : null,
                'ct' => $confirmToken,
                'ut' => $unsubscribeToken,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        } else {
            // Fallback for environments where the migration hasn't run yet.
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
        logNewsletterEvent('signup_new_pending', ['email' => $email]);
    } else {
        $status = $hasStatus ? ($existing['status'] ?? null) : null;

        if ($status === 'confirmed') {
            logNewsletterEvent('signup_already_confirmed', ['email' => $email]);
            header('Location: /index.php?nl=already#newsletter');
            exit;
        }

        if ($status === 'unsubscribed') {
            // Re-subscribe: reset to pending and issue a fresh confirm token.
            $stmt = $pdo->prepare(
                'UPDATE newsletter_subscribers
                   SET status="pending", confirm_token=:ct, confirmed_at=NULL, unsubscribed_at=NULL,
                       ip_address=:ip, user_agent=:ua
                 WHERE id=:id'
            );
            $stmt->execute([
                'ct' => $confirmToken,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'id' => (int) $existing['id'],
            ]);
            logNewsletterEvent('signup_resubscribe', ['email' => $email, 'subscriber_id' => (int) $existing['id']]);
        } elseif ($hasStatus && $hasConfirmToken) {
            // Pending row: refresh the confirm token and resend the mail.
            $stmt = $pdo->prepare('UPDATE newsletter_subscribers SET confirm_token=:ct WHERE id=:id');
            $stmt->execute(['ct' => $confirmToken, 'id' => (int) $existing['id']]);
            logNewsletterEvent('signup_resend_pending', ['email' => $email, 'subscriber_id' => (int) $existing['id']]);
        } else {
            // Old row with no status column → treat as already subscribed.
            logNewsletterEvent('signup_legacy_existing', ['email' => $email]);
            header('Location: /index.php?nl=already#newsletter');
            exit;
        }
    }
} catch (Throwable $e) {
    logNewsletterEvent('signup_db_error', ['email' => $email, 'error' => $e->getMessage()]);
    header('Location: /index.php?nl=error#newsletter');
    exit;
}

// Send confirmation mail (double-opt-in). If SMTP credentials are missing,
// the user still sees the success message; the admin can resend manually.
$mailResult = sendNewsletterConfirmationMail($email, $confirmToken);
if ($mailResult['success']) {
    logNewsletterEvent('confirmation_mail_sent', ['email' => $email]);
} else {
    logNewsletterEvent('confirmation_mail_failed', [
        'email' => $email,
        'error' => $mailResult['error'] ?? 'unknown',
    ]);
}

header('Location: /index.php?nl=pending#newsletter');
exit;
