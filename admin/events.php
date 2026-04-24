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
        $stmt = db()->prepare('DELETE FROM events WHERE id = :id');
        $stmt->execute(['id' => (int) $_POST['id']]);
    } else {
        $payload = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'slug' => trim((string) ($_POST['slug'] ?? '')),
            'event_date' => $_POST['event_date'] ?? null,
            'event_time' => $_POST['event_time'] ?? null,
            'city' => trim((string) ($_POST['city'] ?? '')),
            'location_name' => trim((string) ($_POST['location_name'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
            'description_short' => trim((string) ($_POST['description_short'] ?? '')),
            'description_long' => trim((string) ($_POST['description_long'] ?? '')),
            'image_path' => trim((string) ($_POST['image_path'] ?? '')),
            'status' => $_POST['status'] ?? 'draft',
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];

        if ($action === 'update') {
            $payload['id'] = (int) $_POST['id'];
            $sql = 'UPDATE events SET title=:title, slug=:slug, event_date=:event_date, event_time=:event_time, city=:city,
                    location_name=:location_name, address=:address, description_short=:description_short,
                    description_long=:description_long, image_path=:image_path, status=:status,
                    is_featured=:is_featured, updated_at=NOW() WHERE id=:id';
        } else {
            $sql = 'INSERT INTO events (title, slug, event_date, event_time, city, location_name, address, description_short,
                    description_long, image_path, status, is_featured, created_at, updated_at)
                    VALUES (:title,:slug,:event_date,:event_time,:city,:location_name,:address,:description_short,
                    :description_long,:image_path,:status,:is_featured,NOW(),NOW())';
        }

        $stmt = db()->prepare($sql);
        $stmt->execute($payload);
    }

    header('Location: /admin/events.php');
    exit;
}

$editEvent = null;
if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM events WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $editEvent = $stmt->fetch();
}
$events = db()->query('SELECT * FROM events ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="de"><head><meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Admin Events</title><link rel="stylesheet" href="/assets/css/style.css" /></head>
<body><div class="admin-layout"><aside class="admin-nav"><h2>S-Art Admin</h2><a href="/admin/index.php">Dashboard</a><a href="/admin/events.php">Events</a><a href="/admin/artworks.php">Kunstwerke</a><a href="/admin/locations.php">Standorte</a><a href="/admin/subscribers.php">Subscriber</a></aside><main class="admin-main">
<h1 class="section-title">Events verwalten</h1>
<form method="post" class="grid" style="grid-template-columns:repeat(2,minmax(0,1fr));">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
<input type="hidden" name="action" value="<?= $editEvent ? 'update' : 'create' ?>" />
<?php if ($editEvent): ?><input type="hidden" name="id" value="<?= e((string) $editEvent['id']) ?>" /><?php endif; ?>
<label>Titel<input name="title" required value="<?= e($editEvent['title'] ?? '') ?>" /></label>
<label>Slug<input name="slug" required value="<?= e($editEvent['slug'] ?? '') ?>" /></label>
<label>Datum<input type="date" name="event_date" value="<?= e($editEvent['event_date'] ?? '') ?>" /></label>
<label>Zeit<input type="time" name="event_time" value="<?= e($editEvent['event_time'] ?? '') ?>" /></label>
<label>Stadt<input name="city" value="<?= e($editEvent['city'] ?? '') ?>" /></label>
<label>Location<input name="location_name" value="<?= e($editEvent['location_name'] ?? '') ?>" /></label>
<label>Adresse<input name="address" value="<?= e($editEvent['address'] ?? '') ?>" /></label>
<label>Status<select name="status"><?php foreach (['upcoming','past','draft'] as $s): ?><option value="<?= $s ?>" <?= (($editEvent['status'] ?? 'draft') === $s ? 'selected' : '') ?>><?= $s ?></option><?php endforeach; ?></select></label>
<label style="grid-column:1 / -1;">Kurztext<textarea name="description_short"><?= e($editEvent['description_short'] ?? '') ?></textarea></label>
<label style="grid-column:1 / -1;">Langtext<textarea name="description_long"><?= e($editEvent['description_long'] ?? '') ?></textarea></label>
<label>Bildpfad<input name="image_path" value="<?= e($editEvent['image_path'] ?? '') ?>" /></label>
<label><input type="checkbox" name="is_featured" value="1" <?= !empty($editEvent['is_featured']) ? 'checked' : '' ?> /> Featured</label>
<button type="submit">Speichern</button>
</form>

<div class="table-wrap"><table><thead><tr><th>ID</th><th>Titel</th><th>Datum</th><th>Status</th><th>Aktion</th></tr></thead><tbody>
<?php foreach ($events as $event): ?>
<tr><td><?= e((string) $event['id']) ?></td><td><?= e($event['title']) ?></td><td><?= e($event['event_date']) ?></td><td><?= e($event['status']) ?></td><td><a class="btn" href="?edit=<?= e((string) $event['id']) ?>">Bearbeiten</a>
<form method="post" style="display:inline" onsubmit="return confirm('Löschen?');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" /><input type="hidden" name="action" value="delete" /><input type="hidden" name="id" value="<?= e((string) $event['id']) ?>" /><button>Löschen</button></form></td></tr>
<?php endforeach; ?>
</tbody></table></div>
</main></div></body></html>
