<?php
declare(strict_types=1);
require __DIR__ . '/../config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (!hasColumn('newsletter_subscribers', 'status')) {
    echo json_encode([
        'confirmed'    => 0,
        'pending'      => 0,
        'unsubscribed' => 0,
        'has_status'   => false,
    ]);
    exit;
}

$confirmed = (int) (fetchOne(
    "SELECT COUNT(*) AS c FROM newsletter_subscribers WHERE status='confirmed'"
)['c'] ?? 0);
$pending = (int) (fetchOne(
    "SELECT COUNT(*) AS c FROM newsletter_subscribers WHERE status='pending'"
)['c'] ?? 0);
$unsubscribed = (int) (fetchOne(
    "SELECT COUNT(*) AS c FROM newsletter_subscribers WHERE status='unsubscribed'"
)['c'] ?? 0);

echo json_encode([
    'confirmed'    => $confirmed,
    'pending'      => $pending,
    'unsubscribed' => $unsubscribed,
    'has_status'   => true,
    'fetched_at'   => date('c'),
]);
