<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';

$ticketId = isset($_GET['id']) ? (string) $_GET['id'] : '';
$type     = isset($_GET['t'])  ? (string) $_GET['t']  : 'ticket';

$targets = [
    'ticket' => '/ticket.php',
    'pdf'    => '/ticket-pdf.php',
    'ics'    => '/ticket-ics.php',
    'wallet' => '/ticket-wallet.php',
];

if (!isset($targets[$type])) {
    $type = 'ticket';
}

$fallback = appUrl('/');

if ($ticketId === '' || !preg_match('/^[a-f0-9\-]{16,64}$/i', $ticketId)) {
    header('Location: ' . $fallback, true, 302);
    exit;
}

try {
    $ticket = fetchOne('SELECT id, ticket_id, email, ticket_opened_at FROM tickets WHERE ticket_id = :tid LIMIT 1', ['tid' => $ticketId]);
    if ($ticket) {
        if ($type === 'ticket' && empty($ticket['ticket_opened_at'])) {
            $pdo->prepare('UPDATE tickets SET ticket_opened_at = NOW(), last_click_at = NOW(), click_count = click_count + 1 WHERE id = :id')
                ->execute(['id' => (int) $ticket['id']]);
        } else {
            $pdo->prepare('UPDATE tickets SET last_click_at = NOW(), click_count = click_count + 1 WHERE id = :id')
                ->execute(['id' => (int) $ticket['id']]);
        }
        logTicketEvent($ticket['ticket_id'], $ticket['email'], 'link_clicked_' . $type, null);
    }
} catch (Throwable $e) {
    error_log('track-click error: ' . $e->getMessage());
}

$target = appUrl($targets[$type] . '?id=' . urlencode($ticketId));
header('Location: ' . $target, true, 302);
exit;
