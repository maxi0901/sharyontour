<?php
declare(strict_types=1);
require __DIR__ . '/config/bootstrap.php';

$ticketId = trim((string) ($_GET['id'] ?? ''));
$ticket = $ticketId !== '' ? getTicketByTicketId($ticketId) : null;

if (!$ticket) {
    http_response_code(404);
    exit('Ticket nicht gefunden.');
}

$verifyUrl = appUrl('/ticket.php?id=' . urlencode($ticket['ticket_id']));

$pass = [
    'formatVersion' => 1,
    'passTypeIdentifier' => 'pass.work.s-art.event',
    'serialNumber' => $ticket['ticket_id'],
    'teamIdentifier' => 'SARTTEAM',
    'organizationName' => 'S-ART · Shary on Tour',
    'description' => 'Gratis Ticket · ' . $ticket['event_title'],
    'logoText' => 'S-ART',
    'foregroundColor' => 'rgb(255, 255, 255)',
    'backgroundColor' => 'rgb(10, 10, 12)',
    'labelColor' => 'rgb(226, 35, 26)',
    'eventTicket' => [
        'primaryFields' => [
            ['key' => 'event', 'label' => 'EVENT', 'value' => $ticket['event_title']],
        ],
        'secondaryFields' => [
            ['key' => 'date', 'label' => 'DATUM', 'value' => formatDate($ticket['event_date'])],
            ['key' => 'loc', 'label' => 'ORT', 'value' => $ticket['city']],
        ],
        'auxiliaryFields' => [
            ['key' => 'name', 'label' => 'NAME', 'value' => $ticket['name'] ?: $ticket['email']],
        ],
        'backFields' => [
            ['key' => 'note', 'label' => 'Hinweis', 'value' => 'Der genaue Standort wird rechtzeitig bekanntgegeben.'],
            ['key' => 'url', 'label' => 'Online-Ticket', 'value' => $verifyUrl],
        ],
    ],
    'barcode' => [
        'message' => $verifyUrl,
        'format' => 'PKBarcodeFormatQR',
        'messageEncoding' => 'iso-8859-1',
    ],
];

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="s-art-ticket-' . substr($ticket['ticket_id'], 0, 8) . '.pkpass.json"');
echo json_encode($pass, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
