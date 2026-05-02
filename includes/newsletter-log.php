<?php

declare(strict_types=1);

/**
 * Append-only logger for newsletter events. Writes one JSON line per
 * event into /logs/newsletter.log so admins can grep or tail the file
 * directly. Falls back to error_log() if the file is not writable, so
 * events are never silently lost.
 */
function newsletterLogPath(): string
{
    return __DIR__ . '/../logs/newsletter.log';
}

function logNewsletterEvent(string $event, array $context = []): void
{
    $path = newsletterLogPath();
    $dir = dirname($path);

    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $entry = [
        'time'  => date('c'),
        'event' => $event,
        'ip'    => $_SERVER['REMOTE_ADDR'] ?? null,
        'ua'    => $_SERVER['HTTP_USER_AGENT'] ?? null,
    ];
    foreach ($context as $key => $value) {
        if (is_scalar($value) || $value === null) {
            $entry[$key] = $value;
        } else {
            $entry[$key] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    $line = json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($line === false) {
        $line = json_encode(['time' => date('c'), 'event' => $event, 'ctx_error' => 'encode_failed']);
    }

    $written = @file_put_contents($path, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    if ($written === false) {
        error_log('Newsletter log write failed for event ' . $event . ': ' . $line);
    }
}
