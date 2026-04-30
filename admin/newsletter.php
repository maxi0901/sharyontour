<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    if (($_POST['action'] ?? '') === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare('DELETE FROM newsletter_subscribers WHERE id=:id')->execute(['id' => $id]);
        }
    }
    header('Location: /admin/newsletter.php');
    exit;
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="s-art-newsletter-' . date('Ymd-His') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['id', 'email', 'location_optional', 'created_at']);
    foreach (fetchAll('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC') as $r) {
        fputcsv($out, [$r['id'], $r['email'], $r['location_optional'], $r['created_at']]);
    }
    fclose($out);
    exit;
}

$rows = fetchAll('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC');

$pageTitle = 'Newsletter · Admin';
$adminPage = 'newsletter';
require __DIR__ . '/_header.php';
?>

<div class="admin-page-head">
  <h1>Newsletter <span class="muted"><?= count($rows) ?> Einträge</span></h1>
  <a class="btn btn-ghost btn-sm" href="/admin/newsletter.php?export=csv">CSV exportieren</a>
</div>

<table class="admin-table">
  <thead>
    <tr><th>Datum</th><th>E-Mail</th><th>PLZ / Stadt</th><th></th></tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= e(substr($r['created_at'], 0, 16)) ?></td>
        <td><?= e($r['email']) ?></td>
        <td><?= e($r['location_optional'] ?? '') ?></td>
        <td class="admin-row-actions">
          <form method="post" style="display:inline" onsubmit="return confirm('Eintrag löschen?')">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <button class="text-link danger" type="submit">Löschen</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?>
      <tr><td colspan="4" class="muted">Noch keine Anmeldungen.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php require __DIR__ . '/_footer.php'; ?>
