<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_admin_bootstrap();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        die('Invalid CSRF token');
    }

    $action = $_POST['action'] ?? 'create';
    if ($action === 'delete') {
        db()->prepare('DELETE FROM tour_locations WHERE id = :id')->execute(['id' => (int) $_POST['id']]);
    } else {
        $payload = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'city' => trim((string) ($_POST['city'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
            'date_from' => $_POST['date_from'] ?? null,
            'date_to' => $_POST['date_to'] ?? null,
            'status' => $_POST['status'] ?? 'draft',
            'google_maps_url' => trim((string) ($_POST['google_maps_url'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
        ];

        if ($action === 'update') {
            $payload['id'] = (int) $_POST['id'];
            $sql = 'UPDATE tour_locations SET title=:title, city=:city, address=:address, date_from=:date_from, date_to=:date_to,
                    status=:status, google_maps_url=:google_maps_url, description=:description, updated_at=NOW() WHERE id=:id';
        } else {
            $sql = 'INSERT INTO tour_locations (title, city, address, date_from, date_to, status, google_maps_url, description, created_at, updated_at)
                    VALUES (:title,:city,:address,:date_from,:date_to,:status,:google_maps_url,:description,NOW(),NOW())';
        }
        db()->prepare($sql)->execute($payload);
    }

    header('Location: /admin/locations.php');
    exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM tour_locations WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $editItem = $stmt->fetch();
}
$items = db()->query('SELECT * FROM tour_locations ORDER BY date_from DESC')->fetchAll();
?>
<!doctype html>
<html lang="de"><head><meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Admin Standorte</title><link rel="stylesheet" href="/assets/css/style.css" /></head>
<body><div class="admin-layout"><aside class="admin-nav"><h2>S-Art Admin</h2><a href="/admin/index.php">Dashboard</a><a href="/admin/events.php">Events</a><a href="/admin/artworks.php">Kunstwerke</a><a href="/admin/locations.php">Standorte</a><a href="/admin/subscribers.php">Subscriber</a></aside><main class="admin-main">
<h1 class="section-title">Standorte verwalten</h1>
<form method="post" class="grid" style="grid-template-columns:repeat(2,minmax(0,1fr));">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
<input type="hidden" name="action" value="<?= $editItem ? 'update' : 'create' ?>" />
<?php if ($editItem): ?><input type="hidden" name="id" value="<?= e((string) $editItem['id']) ?>" /><?php endif; ?>
<label>Titel<input name="title" value="<?= e($editItem['title'] ?? '') ?>" required /></label>
<label>Stadt<input name="city" value="<?= e($editItem['city'] ?? '') ?>" required /></label>
<label>Adresse<input name="address" value="<?= e($editItem['address'] ?? '') ?>" /></label>
<label>Status<select name="status"><?php foreach (['current','upcoming','past','draft'] as $s): ?><option value="<?= $s ?>" <?= (($editItem['status'] ?? 'draft') === $s ? 'selected' : '') ?>><?= $s ?></option><?php endforeach; ?></select></label>
<label>Von<input type="date" name="date_from" value="<?= e($editItem['date_from'] ?? '') ?>" /></label>
<label>Bis<input type="date" name="date_to" value="<?= e($editItem['date_to'] ?? '') ?>" /></label>
<label style="grid-column:1 / -1;">Google Maps URL<input name="google_maps_url" value="<?= e($editItem['google_maps_url'] ?? '') ?>" /></label>
<label style="grid-column:1 / -1;">Beschreibung<textarea name="description"><?= e($editItem['description'] ?? '') ?></textarea></label>
<button type="submit">Speichern</button>
</form>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Titel</th><th>Stadt</th><th>Status</th><th>Aktion</th></tr></thead><tbody>
<?php foreach ($items as $item): ?>
<tr><td><?= e((string) $item['id']) ?></td><td><?= e($item['title']) ?></td><td><?= e($item['city']) ?></td><td><?= e($item['status']) ?></td><td><a class="btn" href="?edit=<?= e((string) $item['id']) ?>">Bearbeiten</a>
<form method="post" style="display:inline" onsubmit="return confirm('Löschen?');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" /><input type="hidden" name="action" value="delete" /><input type="hidden" name="id" value="<?= e((string) $item['id']) ?>" /><button>Löschen</button></form></td></tr>
<?php endforeach; ?>
</tbody></table></div>
</main></div></body></html>
