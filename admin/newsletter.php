<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    if ($action === 'delete' && $id) {
        $pdo->prepare('DELETE FROM newsletter_subscribers WHERE id=:id')->execute(['id' => $id]);
    } elseif ($action === 'unsubscribe' && $id && hasColumn('newsletter_subscribers', 'status')) {
        $pdo->prepare("UPDATE newsletter_subscribers SET status='unsubscribed', unsubscribed_at=NOW() WHERE id=:id")
            ->execute(['id' => $id]);
    }
    header('Location: /admin/newsletter.php');
    exit;
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="s-art-newsletter-' . date('Ymd-His') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['id', 'email', 'status', 'location_optional', 'created_at', 'confirmed_at']);
    foreach (fetchAll('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC') as $r) {
        fputcsv($out, [
            $r['id'],
            $r['email'],
            $r['status'] ?? '',
            $r['location_optional'] ?? '',
            $r['created_at'] ?? '',
            $r['confirmed_at'] ?? '',
        ]);
    }
    fclose($out);
    exit;
}

$hasStatus = hasColumn('newsletter_subscribers', 'status');

$confirmed = $hasStatus
    ? (int) (fetchOne("SELECT COUNT(*) AS c FROM newsletter_subscribers WHERE status='confirmed'")['c'] ?? 0)
    : (int) (fetchOne('SELECT COUNT(*) AS c FROM newsletter_subscribers')['c'] ?? 0);
$pending = $hasStatus
    ? (int) (fetchOne("SELECT COUNT(*) AS c FROM newsletter_subscribers WHERE status='pending'")['c'] ?? 0)
    : 0;
$unsubscribed = $hasStatus
    ? (int) (fetchOne("SELECT COUNT(*) AS c FROM newsletter_subscribers WHERE status='unsubscribed'")['c'] ?? 0)
    : 0;

$rows = fetchAll('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC');

$pageTitle = 'Newsletter · Admin';
$adminPage = 'newsletter';
require __DIR__ . '/_header.php';
?>

<div class="admin-page-head">
  <h1>Newsletter <span class="muted"><?= count($rows) ?> Einträge</span></h1>
  <div class="admin-row-actions">
    <a class="btn btn-primary btn-sm" href="/admin/newsletter-campaigns.php">→ Kampagnen verwalten</a>
    <a class="btn btn-ghost btn-sm" href="/admin/newsletter.php?export=csv">CSV exportieren</a>
  </div>
</div>

<div class="admin-grid">
  <div class="admin-card">
    <p class="muted" style="font-size:.7rem;letter-spacing:1.6px;text-transform:uppercase;">Bestätigt</p>
    <h2><?= $confirmed ?></h2>
  </div>
  <div class="admin-card">
    <p class="muted" style="font-size:.7rem;letter-spacing:1.6px;text-transform:uppercase;">Offen / Pending</p>
    <h2><?= $pending ?></h2>
  </div>
  <div class="admin-card">
    <p class="muted" style="font-size:.7rem;letter-spacing:1.6px;text-transform:uppercase;">Abgemeldet</p>
    <h2><?= $unsubscribed ?></h2>
  </div>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
  <thead>
    <tr><th>Datum</th><th>E-Mail</th><th>Status</th><th>PLZ / Stadt</th><th></th></tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r):
      $status = $r['status'] ?? ($hasStatus ? 'pending' : 'confirmed');
    ?>
      <tr>
        <td><?= e(substr((string) ($r['created_at'] ?? ''), 0, 16)) ?></td>
        <td><?= e($r['email']) ?></td>
        <td><span class="status-pill status-<?= e($status) ?>"><?= e($status) ?></span></td>
        <td><?= e((string) ($r['location_optional'] ?? '')) ?></td>
        <td class="admin-row-actions">
          <?php if ($hasStatus && $status !== 'unsubscribed'): ?>
            <form method="post" style="display:inline" onsubmit="return confirm('Diesen Abonnenten abmelden?')">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="unsubscribe">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <button class="text-link" type="submit">Abmelden</button>
            </form>
          <?php endif; ?>
          <form method="post" style="display:inline" onsubmit="return confirm('Eintrag endgültig löschen?')">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <button class="text-link danger" type="submit">Löschen</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?>
      <tr><td colspan="5" class="muted">Noch keine Anmeldungen.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
