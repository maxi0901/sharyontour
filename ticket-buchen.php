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
$hasPostalCol = hasColumn('tickets', 'postal_code');

if (!$opening) {
    $error = 'Aktuell ist kein Opening-Event verfügbar.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $opening) {
    $csrf = $_POST['csrf_token'] ?? null;
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $name = trim((string) ($_POST['name'] ?? ''));
    $postal = trim((string) ($_POST['postal_code'] ?? ''));
    $consent = isset($_POST['consent_privacy']) && $_POST['consent_privacy'] === '1';

    if (!verifyCsrf(is_string($csrf) ? $csrf : null)) {
        $error = 'Sicherheits-Token ungültig. Bitte Seite neu laden.';
    } elseif ($name === '' || mb_strlen($name) < 2) {
        $error = 'Bitte deinen Namen angeben.';
    } elseif ($postal === '' || !preg_match('/^[0-9A-Za-zÄÖÜäöüß\- ]{3,12}$/u', $postal)) {
        $error = 'Bitte eine gültige Postleitzahl angeben.';
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
                    if ($hasPostalCol) {
                        $insert = $pdo->prepare(
                            'INSERT INTO tickets (event_id, email, name, postal_code, ticket_id, status, ip_address, user_agent)
                             VALUES (:e, :em, :n, :pc, :tid, "active", :ip, :ua)'
                        );
                        $insert->execute([
                            'e' => $eventId,
                            'em' => $email,
                            'n' => $name,
                            'pc' => $postal,
                            'tid' => $ticketId,
                            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                        ]);
                    } else {
                        $insert = $pdo->prepare(
                            'INSERT INTO tickets (event_id, email, name, ticket_id, status, ip_address, user_agent)
                             VALUES (:e, :em, :n, :tid, "active", :ip, :ua)'
                        );
                        $insert->execute([
                            'e' => $eventId,
                            'em' => $email,
                            'n' => $name,
                            'tid' => $ticketId,
                            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                        ]);
                    }
                    $pdo->commit();

                    $created = findTicketByEmailAndEvent($eventId, $email);
                    sendTicketMail($pdo, $email, $created['ticket_id'], $name, $opening);
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

<section class="ticket-checkout">
  <div class="container">
    <div class="checkout-steps">
      <div class="checkout-step is-active"><span>1</span> Daten</div>
      <div class="checkout-step-line"></div>
      <div class="checkout-step"><span>2</span> Bestätigung</div>
      <div class="checkout-step-line"></div>
      <div class="checkout-step"><span>3</span> Ticket</div>
    </div>

    <div class="checkout-single">
      <p class="kicker">CONTAINER OPENING · KASSEL</p>
      <h1>GRATIS<br><span class="text-pink">TICKET</span> SICHERN</h1>
      <p class="subline">Trag deine Daten ein und erhalte dein digitales Ticket sofort per E-Mail.</p>

      <form method="post" class="ticket-buy-form ticket-buy-form-stacked" id="ticketBuyForm" novalidate>
        <?= csrfField() ?>

        <?php if ($error): ?>
          <div class="form-flash form-flash-error"><?= e($error) ?></div>
        <?php endif; ?>
        <?php if ($existingMessage): ?>
          <div class="form-flash"><?= e($existingMessage) ?></div>
        <?php endif; ?>

        <div class="checkout-section">
          <p class="checkout-section-kicker">DEINE DATEN</p>

          <label class="field">
            <span>Name *</span>
            <input type="text" name="name" maxlength="120" required minlength="2"
                   placeholder="Vor- und Nachname"
                   value="<?= e($_POST['name'] ?? '') ?>">
          </label>

          <div class="field-row">
            <label class="field">
              <span>PLZ *</span>
              <input type="text" name="postal_code" required maxlength="12" minlength="3"
                     pattern="[0-9A-Za-zÄÖÜäöüß\- ]{3,12}"
                     placeholder="z. B. 34117"
                     value="<?= e($_POST['postal_code'] ?? '') ?>">
            </label>

            <label class="field field-grow">
              <span>E-Mail *</span>
              <input type="email" name="email" required
                     placeholder="deine@email.de"
                     value="<?= e($_POST['email'] ?? '') ?>">
            </label>
          </div>

          <label class="check check-privacy">
            <input type="checkbox" name="consent_privacy" value="1" required aria-required="true">
            <span>Ich akzeptiere die <a href="/datenschutz.php" class="privacy-pill">Datenschutzerklärung</a> <strong class="required-mark">*</strong></span>
          </label>
        </div>

        <div class="checkout-section checkout-section-summary">
          <p class="checkout-section-kicker">DEINE BESTELLUNG</p>
          <div class="summary-event">
            <h3>Container Opening</h3>
            <p class="muted"><?= $opening ? formatDateLong($opening['event_date']) : '22. August 2026' ?></p>
            <p class="muted">Kassel · Standort wird rechtzeitig bekanntgegeben</p>
          </div>
          <ul class="summary-list">
            <li><span>Gratis Ticket · Container Opening</span><strong>0,00 €</strong></li>
            <li><span>Service-Gebühr</span><strong>0,00 €</strong></li>
            <li><span>MwSt.</span><strong>0,00 €</strong></li>
          </ul>
          <div class="summary-total">
            <span>Gesamt</span>
            <strong>0,00 €</strong>
          </div>

          <div class="summary-stock <?= $soldOut ? 'is-sold' : ($lowStock ? 'is-low' : '') ?>">
            <?php if ($soldOut): ?>
              <strong>AUSVERKAUFT</strong>
            <?php else: ?>
              <strong>Noch <?= $remaining ?> von <?= $maxTickets ?> verfügbar</strong>
              <?php if ($lowStock): ?>
                <span class="warning">Letzte Tickets — sichere dir deinen Platz.</span>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <ul class="summary-features">
            <li>Sofortige Ticket-Erstellung</li>
            <li>QR-Code für schnellen Einlass</li>
            <li>Apple / Google Wallet · PDF · Kalender (.ics)</li>
            <li>Versand per E-Mail</li>
          </ul>
        </div>

        <div class="checkout-submit">
          <?php if ($soldOut): ?>
            <button class="btn btn-disabled btn-block" disabled>AUSVERKAUFT</button>
            <a class="text-link" href="/index.php#newsletter">Auf Warteliste setzen →</a>
          <?php else: ?>
            <button class="btn btn-primary btn-block" type="submit" id="ticketSubmitBtn">
              <span class="btn-label">JETZT GRATIS BUCHEN →</span>
            </button>
            <p class="muted form-hint">Pflichtfelder · Limit: 1 Ticket pro E-Mail · 100% kostenlos · Bestätigung per E-Mail</p>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>
</section>

<div class="checkout-loader" id="checkoutLoader" hidden aria-hidden="true">
  <div class="checkout-loader-box" role="dialog" aria-modal="true" aria-labelledby="checkoutLoaderTitle">
    <div class="checkout-loader-spinner" aria-hidden="true">
      <div class="checkout-loader-ring"></div>
      <div class="checkout-loader-pulse"></div>
    </div>
    <p class="checkout-loader-step" id="checkoutLoaderTitle">Daten werden geprüft…</p>
    <div class="checkout-loader-progress"><span></span></div>
    <p class="muted checkout-loader-note">Einen Moment — wir erstellen dein Ticket.</p>
  </div>
</div>

<script>
(function () {
  var form = document.getElementById('ticketBuyForm');
  var loader = document.getElementById('checkoutLoader');
  var btn = document.getElementById('ticketSubmitBtn');
  if (!form || !loader) return;

  var steps = [
    'Daten werden geprüft…',
    'Verfügbarkeit wird bestätigt…',
    'Ticket wird ausgestellt…',
    'Bestätigung wird versendet…'
  ];

  form.addEventListener('submit', function () {
    if (!form.checkValidity()) return;
    if (btn) btn.disabled = true;
    loader.hidden = false;
    loader.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    var stepEl = loader.querySelector('.checkout-loader-step');
    var i = 0;
    var tick = function () {
      i = (i + 1) % steps.length;
      stepEl.textContent = steps[i];
    };
    setInterval(tick, 1100);
  });
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
