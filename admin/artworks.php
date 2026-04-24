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
        db()->prepare('DELETE FROM artworks WHERE id = :id')->execute(['id' => (int) $_POST['id']]);
    } else {
        $payload = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'slug' => trim((string) ($_POST['slug'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'image_path' => trim((string) ($_POST['image_path'] ?? '')),
            'collection_name' => trim((string) ($_POST['collection_name'] ?? '')),
            'year' => $_POST['year'] !== '' ? (int) $_POST['year'] : null,
            'is_visible' => isset($_POST['is_visible']) ? 1 : 0,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ];

        if ($action === 'update') {
            $payload['id'] = (int) $_POST['id'];
            $sql = 'UPDATE artworks SET title=:title, slug=:slug, description=:description, image_path=:image_path,
                    collection_name=:collection_name, year=:year, is_visible=:is_visible, sort_order=:sort_order,
                    updated_at=NOW() WHERE id=:id';
        } else {
            $sql = 'INSERT INTO artworks (title, slug, description, image_path, collection_name, year, is_visible, sort_order, created_at, updated_at)
                    VALUES (:title,:slug,:description,:image_path,:collection_name,:year,:is_visible,:sort_order,NOW(),NOW())';
        }
        db()->prepare($sql)->execute($payload);
    }

    header('Location: /admin/artworks.php');
    exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM artworks WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $editItem = $stmt->fetch();
}
$items = db()->query('SELECT * FROM artworks ORDER BY sort_order ASC, created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="de"><head><meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Admin Kunstwerke</title><link rel="stylesheet" href="/assets/css/style.css" /></head>
<body><div class="admin-layout"><aside class="admin-nav"><h2>S-Art Admin</h2><a href="/admin/index.php">Dashboard</a><a href="/admin/events.php">Events</a><a href="/admin/artworks.php">Kunstwerke</a><a href="/admin/locations.php">Standorte</a><a href="/admin/subscribers.php">Subscriber</a></aside><main class="admin-main">
<h1 class="section-title">Kunstwerke verwalten</h1>
<form method="post" class="grid" style="grid-template-columns:repeat(2,minmax(0,1fr));">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
<input type="hidden" name="action" value="<?= $editItem ? 'update' : 'create' ?>" />
<?php if ($editItem): ?><input type="hidden" name="id" value="<?= e((string) $editItem['id']) ?>" /><?php endif; ?>
<label>Titel<input name="title" value="<?= e($editItem['title'] ?? '') ?>" required /></label>
<label>Slug<input name="slug" value="<?= e($editItem['slug'] ?? '') ?>" required /></label>
<label>Collection<input name="collection_name" value="<?= e($editItem['collection_name'] ?? '') ?>" /></label>
<label>Jahr<input type="number" name="year" value="<?= e((string) ($editItem['year'] ?? '')) ?>" /></label>
<label>Sortierung<input type="number" name="sort_order" value="<?= e((string) ($editItem['sort_order'] ?? '0')) ?>" /></label>
<label>Bildpfad<input name="image_path" value="<?= e($editItem['image_path'] ?? '') ?>" /></label>
<label style="grid-column:1 / -1;">Beschreibung<textarea name="description"><?= e($editItem['description'] ?? '') ?></textarea></label>
<label><input type="checkbox" name="is_visible" value="1" <?= !empty($editItem['is_visible']) ? 'checked' : '' ?> /> Sichtbar</label>
<button type="submit">Speichern</button>
</form>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Titel</th><th>Collection</th><th>Sichtbar</th><th>Aktion</th></tr></thead><tbody>
<?php foreach ($items as $item): ?>
<tr><td><?= e((string) $item['id']) ?></td><td><?= e($item['title']) ?></td><td><?= e($item['collection_name']) ?></td><td><?= e((string) $item['is_visible']) ?></td><td><a class="btn" href="?edit=<?= e((string) $item['id']) ?>">Bearbeiten</a>
<form method="post" style="display:inline" onsubmit="return confirm('Löschen?');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" /><input type="hidden" name="action" value="delete" /><input type="hidden" name="id" value="<?= e((string) $item['id']) ?>" /><button>Löschen</button></form></td></tr>
<?php endforeach; ?>
</tbody></table></div>
</main></div></body></html>
