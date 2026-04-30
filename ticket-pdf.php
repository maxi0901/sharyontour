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
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=10&data=' . urlencode($verifyUrl);
?><!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>S-ART Ticket · <?= e($ticket['event_title']) ?></title>
  <style>
    @page { size: A4; margin: 24mm; }
    body { font-family: 'Helvetica', sans-serif; background: #0a0a0c; color: #f7f8ff; margin: 0; padding: 24px; }
    .pdf-ticket { max-width: 540px; margin: 0 auto; border: 2px solid #e2231a; border-radius: 18px; padding: 28px; background: linear-gradient(160deg, #0a0a0c 0%, #1a0509 100%); }
    .pdf-logo { font-family: 'Times', serif; font-size: 56px; font-weight: 900; color: #e2231a; letter-spacing: 4px; }
    h1 { color: #fff; font-size: 28px; margin: 12px 0 8px; }
    .meta-row { display: flex; gap: 24px; margin-top: 16px; }
    .meta-label { color: #aab1c3; font-size: 11px; letter-spacing: 2px; }
    .qr { text-align: center; margin: 20px 0; }
    .qr img { width: 220px; height: 220px; border: 6px solid #fff; border-radius: 12px; }
    .ticket-id { font-family: monospace; font-size: 11px; color: #aab1c3; word-break: break-all; }
    .print-btn { display: inline-block; margin: 12px 0; padding: 10px 16px; background: #e2231a; color: #fff; border-radius: 8px; text-decoration: none; }
    @media print { .print-btn { display: none; } body { background: #fff; color: #000; } .pdf-ticket { background: #fff; color: #000; } .pdf-logo { color: #e2231a; } h1 { color: #000; } }
  </style>
</head>
<body>
  <a class="print-btn" href="javascript:window.print()">Drucken / Als PDF speichern</a>

  <div class="pdf-ticket">
    <div class="pdf-logo">S-ART</div>
    <p style="color:#e2231a;letter-spacing:3px;font-size:11px;margin:0 0 8px">GRATIS TICKET</p>
    <h1><?= e($ticket['event_title']) ?></h1>

    <div class="meta-row">
      <div>
        <div class="meta-label">DATUM</div>
        <strong><?= formatDate($ticket['event_date']) ?></strong>
      </div>
      <div>
        <div class="meta-label">ORT</div>
        <strong><?= e($ticket['city']) ?></strong>
      </div>
    </div>

    <div class="meta-row">
      <div>
        <div class="meta-label">TICKET FÜR</div>
        <strong><?= e($ticket['name'] ?: $ticket['email']) ?></strong>
      </div>
    </div>

    <div class="qr">
      <img src="<?= e($qrUrl) ?>" alt="QR Code">
    </div>

    <p class="ticket-id">ID: <?= e($ticket['ticket_id']) ?></p>
    <p style="color:#aab1c3;font-size:12px">Der genaue Standort wird rechtzeitig bekanntgegeben.</p>
  </div>

  <script>window.addEventListener('load', () => setTimeout(() => window.print(), 400));</script>
</body>
</html>
