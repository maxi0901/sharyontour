<?php
require __DIR__ . '/config/bootstrap.php';
require __DIR__ . '/includes/csrf.php';
require __DIR__ . '/config/mail.php';

$pageTitle = 'Gratis Ticket · Container Opening Kassel';

$opening = getOpeningEvent();
$error = null;
$success = null;
$createdTicket = null;
$existingMessage = null;

if (!$opening) {
    $error = 'Aktuell ist kein Opening-Event verfügbar.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $opening) {
    $csrf = $_POST['csrf_token'] ?? null;
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $name = trim((string) ($_POST['name'] ?? ''));
    $consent = isset($_POST['consent_privacy']) && $_POST['consent_privacy'] === '1';

    if (!verifyCsrf(is_string($csrf) ? $csrf : null)) {
        $error = 'Sicherheits-Token ungültig. Bitte Seite neu laden.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Bitte eine gültige E-Mail-Adresse angeben.';
    } elseif (!$consent) {
        $error = 'Datenschutz-Zustimmung ist erforderlich.';
    } else {
        $eventId = (int) $opening['id'];
        $maxTickets = (int) $opening['max_tickets'];

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM tickets WHERE event_id=:e AND status="active" FOR UPDATE');
            $stmt->execute(['e' => $eventId]);
            $taken = (int) $stmt->fetch()['c'];

            if ($taken >= $maxTickets) {
                $pdo->rollBack();
                $error = 'Leider sind alle Tickets vergeben.';
            } else {
                $existing = findTicketByEmailAndEvent($eventId, $email);
                if ($existing) {
                    $pdo->commit();
                    $existingMessage = 'Diese E-Mail hat bereits ein Ticket. Wir haben dir den Link erneut zugesendet.';
                    sendTicketMail($pdo, $email, $existing['ticket_id'], $name !== '' ? $name : null, $opening);
                    $createdTicket = $existing;
                } else {
                    $ticketId = generateUuidV4();
                    $insert = $pdo->prepare(
                        'INSERT INTO tickets (event_id, email, name, ticket_id, status, ip_address, user_agent)
                         VALUES (:e, :em, :n, :tid, "active", :ip, :ua)'
                    );
                    $insert->execute([
                        'e' => $eventId,
                        'em' => $email,
                        'n' => $name !== '' ? $name : null,
                        'tid' => $ticketId,
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                        'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                    ]);
                    $pdo->commit();

                    $created = findTicketByEmailAndEvent($eventId, $email);
                    sendTicketMail($pdo, $email, $created['ticket_id'], $name !== '' ? $name : null, $opening);
                    $createdTicket = $created;
                    $success = 'Dein Gratis-Ticket wurde erstellt! Wir haben dir auch eine Bestätigungs-Mail gesendet.';
                }
            }
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('Ticket creation failed: ' . $ex->getMessage());
            $error = 'Es gab ein technisches Problem. Bitte später erneut versuchen.';
        }
    }
}

if ($createdTicket) {
    header('Location: /ticket.php?id=' . urlencode($createdTicket['ticket_id']) . ($success ? '&new=1' : ''));
    exit;
}

$soldTickets = $opening ? countTicketsForEvent((int) $opening['id']) : 0;
$maxTickets = $opening ? (int) $opening['max_tickets'] : 600;
$remaining = max(0, $maxTickets - $soldTickets);
$lowStock = $remaining > 0 && $remaining < 100;
$soldOut = $remaining <= 0;

require __DIR__ . '/includes/header.php';
?>

<section class="section container ticket-buy reveal">
  <p class="kicker">CONTAINER OPENING · KASSEL</p>
  <h1>GRATIS<br><span class="text-red">TICKET</span></h1>
  <p class="subline">22.08.2026 · Kassel · Limitiert auf 600 Tickets</p>

  <div class="ticket-buy-grid">
    <div class="ticket-buy-info">
      <p>Dein digitales Ticket wird sofort erstellt und per E-Mail bestätigt. Mit Apple Wallet, Google Wallet, Kalender (.ics) und PDF kompatibel.</p>

      <div class="ticket-stock-box <?= $soldOut ? 'is-sold' : ($lowStock ? 'is-low' : '') ?>">
        <?php if ($soldOut): ?>
          <strong>AUSVERKAUFT</strong>
          <p>Trag dich für die Warteliste ein und werde benachrichtigt.</p>
        <?php else: ?>
          <strong>Noch <?= $remaining ?> von <?= $maxTickets ?> Tickets verfügbar</strong>
          <?php if ($lowStock): ?>
            <p class="warning">⚠ Letzte Tickets — sichere dir deinen Platz jetzt.</p>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <ul class="ticket-buy-list">
        <li>Limit: 1 Ticket pro E-Mail</li>
        <li>Datum: 22.08.2026, Kassel</li>
        <li>Standort wird rechtzeitig bekanntgegeben</li>
      </ul>
    </div>

    <form method="post" class="ticket-buy-form" novalidate>
      <?= csrfField() ?>

      <?php if ($error): ?>
        <div class="form-flash form-flash-error"><?= e($error) ?></div>
      <?php endif; ?>
      <?php if ($existingMessage): ?>
        <div class="form-flash"><?= e($existingMessage) ?></div>
      <?php endif; ?>

      <label class="field">
        <span>Name (optional)</span>
        <input type="text" name="name" maxlength="120" value="<?= e($_POST['name'] ?? '') ?>">
      </label>

      <label class="field">
        <span>E-Mail-Adresse *</span>
        <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
      </label>

      <label class="check">
        <input type="checkbox" name="consent_privacy" value="1" required>
        Ich akzeptiere die <a href="/datenschutz.php">Datenschutzerklärung</a>.
      </label>

      <?php if ($soldOut): ?>
        <button class="btn btn-disabled" disabled>AUSVERKAUFT</button>
        <a class="text-link" href="/newsletter#newsletter">Auf Warteliste setzen →</a>
      <?php else: ?>
        <button class="btn btn-primary" type="submit">GRATIS TICKET SICHERN →</button>
      <?php endif; ?>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
