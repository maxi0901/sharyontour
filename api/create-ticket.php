<?php
declare(strict_types=1);
require __DIR__ . '/../config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$name = trim((string) ($_POST['name'] ?? ''));
$postal = trim((string) ($_POST['postal_code'] ?? ''));
$eventId = (int) ($_POST['event_id'] ?? 0);

if ($name === '' || mb_strlen($name) < 2) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'Bitte deinen Namen angeben']);
    exit;
}
if ($postal === '' || !preg_match('/^[0-9A-Za-zÄÖÜäöüß\- ]{3,12}$/u', $postal)) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'Bitte eine gültige Postleitzahl angeben']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'Ungültige E-Mail']);
    exit;
}
$event = fetchOne('SELECT id, is_opening, max_tickets FROM events WHERE id=:id LIMIT 1', ['id' => $eventId]);
if (!$event) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Event nicht gefunden']);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$rateCount = (int) (fetchOne('SELECT COUNT(*) AS c FROM tickets WHERE ip_address=:ip AND created_at >= (NOW() - INTERVAL 1 MINUTE)', ['ip' => $ip])['c'] ?? 0);
if ($rateCount >= 3) {
    http_response_code(429);
    echo json_encode(['status' => 'error', 'message' => 'Zu viele Anfragen']);
    exit;
}

try {
    $pdo->beginTransaction();
    $existing = fetchOne('SELECT id FROM tickets WHERE event_id=:e AND email=:m LIMIT 1 FOR UPDATE', ['e' => $eventId, 'm' => $email]);
    if ($existing) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Du hast bereits ein Ticket']);
        exit;
    }

    $maxTickets = max(1, (int) ($event['max_tickets'] ?? 600));
    $count = (int) (fetchOne('SELECT COUNT(*) AS c FROM tickets WHERE event_id=:e AND status="active" FOR UPDATE', ['e' => $eventId])['c'] ?? 0);
    if ($count >= $maxTickets) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Event ausverkauft']);
        exit;
    }

    $ticketId = bin2hex(random_bytes(16));
    if (hasColumn('tickets', 'postal_code')) {
        $stmt = $pdo->prepare('INSERT INTO tickets (event_id, email, name, postal_code, ticket_id, status, ip_address, user_agent) VALUES (:e,:em,:n,:pc,:tid,"active",:ip,:ua)');
        $stmt->execute([
            'e' => $eventId,
            'em' => $email,
            'n' => $name,
            'pc' => $postal,
            'tid' => $ticketId,
            'ip' => $ip,
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO tickets (event_id, email, name, ticket_id, status, ip_address, user_agent) VALUES (:e,:em,:n,:tid,"active",:ip,:ua)');
        $stmt->execute([
            'e' => $eventId,
            'em' => $email,
            'n' => $name,
            'tid' => $ticketId,
            'ip' => $ip,
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);
    }
    $pdo->commit();

    try {
        require_once __DIR__ . '/../config/mail.php';
        $eventForMail = fetchOne('SELECT id, title, event_date, event_time FROM events WHERE id = :id LIMIT 1', ['id' => $eventId]);
        sendTicketMail($pdo, $email, $ticketId, $name, $eventForMail);
    } catch (Throwable $mailError) {
        error_log('sendTicketMail failed in api/create-ticket.php: ' . $mailError->getMessage());
    }

    echo json_encode(['status' => 'success', 'ticket_id' => $ticketId]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Technischer Fehler']);
}
