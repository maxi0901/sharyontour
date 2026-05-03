<?php

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$raw = file_get_contents('php://input') ?: '{}';
$payload = json_decode($raw, true) ?: [];

$type = (string) ($payload['type'] ?? '');
$dir  = (string) ($payload['direction'] ?? '');
$path = (string) ($payload['page'] ?? '');

$allowedTypes = ['carousel_arrow'];
$allowedDirs  = ['prev', 'next', ''];

if (!in_array($type, $allowedTypes, true) || !in_array($dir, $allowedDirs, true)) {
    http_response_code(204);
    exit;
}

try {
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? substr((string) $_SERVER['HTTP_USER_AGENT'], 0, 500) : null;

    $stmt = $pdo->prepare(
        'INSERT INTO click_logs (event_type, direction, page_path, ip_address, user_agent)
         VALUES (:type, :dir, :path, :ip, :ua)'
    );
    $stmt->execute([
        'type' => $type,
        'dir'  => $dir !== '' ? $dir : null,
        'path' => $path !== '' ? substr($path, 0, 255) : null,
        'ip'   => $ip,
        'ua'   => $ua,
    ]);
} catch (Throwable $e) {
    error_log('track-click insert failed: ' . $e->getMessage());
}

http_response_code(204);
