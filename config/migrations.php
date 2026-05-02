<?php

declare(strict_types=1);

function runTrackingMigrations(PDO $pdo): void
{
    static $alreadyRan = false;
    if ($alreadyRan) {
        return;
    }
    $alreadyRan = true;

    $columns = [
        'mail_sent_at'     => 'ALTER TABLE tickets ADD COLUMN mail_sent_at DATETIME NULL',
        'mail_opened_at'   => 'ALTER TABLE tickets ADD COLUMN mail_opened_at DATETIME NULL',
        'ticket_opened_at' => 'ALTER TABLE tickets ADD COLUMN ticket_opened_at DATETIME NULL',
        'last_click_at'    => 'ALTER TABLE tickets ADD COLUMN last_click_at DATETIME NULL',
        'click_count'      => 'ALTER TABLE tickets ADD COLUMN click_count INT UNSIGNED NOT NULL DEFAULT 0',
    ];

    try {
        foreach ($columns as $name => $sql) {
            if (!hasColumn('tickets', $name)) {
                $pdo->exec($sql);
            }
        }
    } catch (Throwable $e) {
        error_log('Tracking migration failed: ' . $e->getMessage());
    }
}
