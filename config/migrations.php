<?php

declare(strict_types=1);

function runTrackingMigrations(PDO $pdo): void
{
    static $alreadyRan = false;
    if ($alreadyRan) {
        return;
    }
    $alreadyRan = true;

    try {
        if (!hasColumn('tickets', 'click_count')) {
            $columns = [
                'mail_sent_at'     => 'ALTER TABLE tickets ADD COLUMN mail_sent_at DATETIME NULL',
                'mail_opened_at'   => 'ALTER TABLE tickets ADD COLUMN mail_opened_at DATETIME NULL',
                'ticket_opened_at' => 'ALTER TABLE tickets ADD COLUMN ticket_opened_at DATETIME NULL',
                'last_click_at'    => 'ALTER TABLE tickets ADD COLUMN last_click_at DATETIME NULL',
                'click_count'      => 'ALTER TABLE tickets ADD COLUMN click_count INT UNSIGNED NOT NULL DEFAULT 0',
            ];

            foreach ($columns as $name => $sql) {
                if (!hasColumn('tickets', $name)) {
                    $pdo->exec($sql);
                }
            }
        }
    } catch (Throwable $e) {
        error_log('Tracking migration failed: ' . $e->getMessage());
    }

    try {
        if (hasColumn('events', 'is_opening') && hasColumn('events', 'slug')) {
            $stmt = $pdo->prepare(
                "UPDATE events SET is_opening = 1
                 WHERE slug = 'container-opening-kassel' AND (is_opening IS NULL OR is_opening = 0)"
            );
            $stmt->execute();
        }
    } catch (Throwable $e) {
        error_log('Container opening migration failed: ' . $e->getMessage());
    }

    runNewsletterMigrations($pdo);
}

function runNewsletterMigrations(PDO $pdo): void
{
    try {
        $columns = [
            'status'             => "ALTER TABLE newsletter_subscribers ADD COLUMN status ENUM('pending','confirmed','unsubscribed') DEFAULT 'pending' AFTER consent_privacy",
            'confirm_token'      => 'ALTER TABLE newsletter_subscribers ADD COLUMN confirm_token VARCHAR(128) NULL',
            'unsubscribe_token'  => 'ALTER TABLE newsletter_subscribers ADD COLUMN unsubscribe_token VARCHAR(128) NULL',
            'confirmed_at'       => 'ALTER TABLE newsletter_subscribers ADD COLUMN confirmed_at DATETIME NULL',
            'unsubscribed_at'    => 'ALTER TABLE newsletter_subscribers ADD COLUMN unsubscribed_at DATETIME NULL',
        ];

        foreach ($columns as $col => $sql) {
            if (!hasColumn('newsletter_subscribers', $col)) {
                $pdo->exec($sql);
            }
        }

        if (hasColumn('newsletter_subscribers', 'status')) {
            $pdo->exec("UPDATE newsletter_subscribers SET status = 'confirmed', confirmed_at = COALESCE(confirmed_at, created_at) WHERE status IS NULL OR status = ''");
        }

        if (hasColumn('newsletter_subscribers', 'unsubscribe_token')) {
            $rows = $pdo->query("SELECT id FROM newsletter_subscribers WHERE unsubscribe_token IS NULL OR unsubscribe_token = ''")->fetchAll();
            $stmt = $pdo->prepare('UPDATE newsletter_subscribers SET unsubscribe_token = :t WHERE id = :id');
            foreach ($rows as $r) {
                $stmt->execute(['t' => bin2hex(random_bytes(32)), 'id' => (int) $r['id']]);
            }
        }
    } catch (Throwable $e) {
        error_log('Newsletter subscribers migration failed: ' . $e->getMessage());
    }

    try {
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS newsletter_campaigns (
              id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              subject VARCHAR(255) NOT NULL,
              body_html MEDIUMTEXT NOT NULL,
              body_text MEDIUMTEXT NULL,
              status ENUM('draft','sending','sent','failed') DEFAULT 'draft',
              recipients_total INT DEFAULT 0,
              sent_count INT DEFAULT 0,
              failed_count INT DEFAULT 0,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
              sent_at DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS newsletter_send_log (
              id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              campaign_id INT UNSIGNED NOT NULL,
              subscriber_id INT UNSIGNED NOT NULL,
              email VARCHAR(190) NOT NULL,
              status ENUM('sent','failed') NOT NULL,
              error_message TEXT NULL,
              sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
              INDEX idx_campaign (campaign_id),
              INDEX idx_subscriber (subscriber_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    } catch (Throwable $e) {
        error_log('Newsletter campaigns migration failed: ' . $e->getMessage());
    }
}
