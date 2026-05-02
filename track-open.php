<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';

$ticketId = isset($_GET['id']) ? (string) $_GET['id'] : '';

if ($ticketId !== '' && preg_match('/^[a-f0-9\-]{16,64}$/i', $ticketId)) {
    try {
        $ticket = fetchOne('SELECT id, ticket_id, email, mail_opened_at FROM tickets WHERE ticket_id = :tid LIMIT 1', ['tid' => $ticketId]);
        if ($ticket && empty($ticket['mail_opened_at'])) {
            $pdo->prepare('UPDATE tickets SET mail_opened_at = NOW() WHERE id = :id')
                ->execute(['id' => (int) $ticket['id']]);
            logTicketEvent($ticket['ticket_id'], $ticket['email'], 'mail_opened', null);
        }
    } catch (Throwable $e) {
        error_log('track-open error: ' . $e->getMessage());
    }
}

header('Content-Type: image/gif');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Content-Length: 43');

echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
exit;
