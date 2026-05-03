<?php
declare(strict_types=1);
require __DIR__ . '/../config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

$eventId = (int) ($_GET['event_id'] ?? 0);
if ($eventId <= 0) {
    http_response_code(400);
    echo json_encode(['message' => 'Ungültige event_id']);
    exit;
}
$event = fetchOne('SELECT id, max_tickets FROM events WHERE id=:id LIMIT 1', ['id' => $eventId]);
if (!$event) {
    http_response_code(404);
    echo json_encode(['message' => 'Event nicht gefunden']);
    exit;
}
$count = countTicketsForEvent($eventId);
$max = (int) ($event['max_tickets'] ?? 600);
if ($max <= 0) {
    $max = 600;
}
echo json_encode([
    'count'     => $count,
    'max'       => $max,
    'threshold' => TICKET_TRACKER_THRESHOLD,
    'label'     => ticketTrackerLabel($count, $max),
]);
