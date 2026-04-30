<?php
declare(strict_types=1);
require __DIR__ . '/config/bootstrap.php';

$ticketId = trim((string) ($_GET['id'] ?? ''));
$ticket = $ticketId !== '' ? getTicketByTicketId($ticketId) : null;

if (!$ticket) {
    http_response_code(404);
    exit('Ticket nicht gefunden.');
}

$start = $ticket['event_date'] . ' ' . ($ticket['event_time'] ?: '18:00:00');
$dtStart = (new DateTime($start, new DateTimeZone('Europe/Berlin')));
$dtEnd = (clone $dtStart)->modify('+4 hours');

$uid = $ticket['ticket_id'] . '@s-art.work';
$summary = 'S-ART · ' . $ticket['event_title'];
$location = $ticket['city'];
$description = "Dein Gratis-Ticket: " . appUrl('/ticket.php?id=' . urlencode($ticket['ticket_id'])) . "\\nTicket-ID: " . $ticket['ticket_id'];

$ics = "BEGIN:VCALENDAR\r\n"
     . "VERSION:2.0\r\n"
     . "PRODID:-//S-ART//Shary on Tour//DE\r\n"
     . "CALSCALE:GREGORIAN\r\n"
     . "METHOD:PUBLISH\r\n"
     . "BEGIN:VEVENT\r\n"
     . "UID:" . $uid . "\r\n"
     . "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n"
     . "DTSTART:" . $dtStart->setTimezone(new DateTimeZone('UTC'))->format('Ymd\THis\Z') . "\r\n"
     . "DTEND:" . $dtEnd->setTimezone(new DateTimeZone('UTC'))->format('Ymd\THis\Z') . "\r\n"
     . "SUMMARY:" . str_replace(["\r","\n"], '', $summary) . "\r\n"
     . "LOCATION:" . str_replace(["\r","\n"], '', $location) . "\r\n"
     . "DESCRIPTION:" . str_replace(["\r","\n"], '', $description) . "\r\n"
     . "END:VEVENT\r\n"
     . "END:VCALENDAR\r\n";

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="s-art-' . preg_replace('/[^a-z0-9]+/i', '-', $ticket['event_title']) . '.ics"');
echo $ics;
