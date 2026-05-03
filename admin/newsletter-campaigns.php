<?php
require_once __DIR__ . '/auth.php';
requireAdminLogin();
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    if (($_POST['action'] ?? '') === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare('DELETE FROM newsletter_campaigns WHERE id=:id')->execute(['id' => $id]);
        }
    }
    header('Location: /admin/newsletter-campaigns.php');
    exit;
}

$campaigns = fetchAll('SELECT * FROM newsletter_campaigns ORDER BY created_at DESC');

$pageTitle = 'Newsletter Kampagnen · Admin';
$adminPage = 'newsletter-campaigns';
require __DIR__ . '/_header.php';
?>

<div class="admin-page-head">
  <h1>Newsletter-Kampagnen</h1>
  <a class="btn btn-primary btn-sm" href="/admin/newsletter-campaign-edit.php">+ Neue Kampagne</a>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
  <thead>
    <tr>
      <th>Erstellt</th>
      <th>Betreff</th>
      <th>Status</th>
      <th>Empfänger</th>
      <th>Versendet</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($campaigns as $c):
      $status = $c['status'] ?? 'draft';
    ?>
      <tr>
        <td><?= e(substr((string) ($c['created_at'] ?? ''), 0, 16)) ?></td>
        <td><?= e($c['subject']) ?></td>
        <td><span class="status-pill status-<?= e($status) ?>"><?= e($status) ?></span></td>
        <td><?= (int) $c['recipients_total'] ?></td>
        <td>
          <?= (int) $c['sent_count'] ?>
          <?php if ((int) $c['failed_count'] > 0): ?>
            <span class="muted">· <?= (int) $c['failed_count'] ?> failed</span>
          <?php endif; ?>
        </td>
        <td class="admin-row-actions">
          <a class="text-link" href="/admin/newsletter-campaign-edit.php?id=<?= (int) $c['id'] ?>">Öffnen</a>
          <?php if ($status !== 'sending'): ?>
            <form method="post" style="display:inline" onsubmit="return confirm('Kampagne und Versandprotokoll löschen?')">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
              <button class="text-link danger" type="submit">Löschen</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$campaigns): ?>
      <tr><td colspan="6" class="muted">Noch keine Kampagnen. Lege eine neue an, um den ersten Newsletter zu schreiben.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
