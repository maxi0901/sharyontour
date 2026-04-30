-- Idempotent migration to align an existing DB with the new schema.
-- Run in MySQL/MariaDB with caution; only adds new columns/tables if missing.

-- events: new columns
ALTER TABLE events
  ADD COLUMN IF NOT EXISTS is_main_event TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS has_tickets TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS max_tickets INT UNSIGNED DEFAULT 0,
  ADD COLUMN IF NOT EXISTS hide_address TINYINT(1) DEFAULT 0;

-- newsletter: optional location
ALTER TABLE newsletter_subscribers
  ADD COLUMN IF NOT EXISTS location_optional VARCHAR(255) NULL;

-- galleries
CREATE TABLE IF NOT EXISTS event_galleries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id INT UNSIGNED NOT NULL,
  image_path VARCHAR(500) NOT NULL,
  caption VARCHAR(255) NULL,
  sort_order INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_gallery_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- tickets
CREATE TABLE IF NOT EXISTS tickets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id INT UNSIGNED NOT NULL,
  ticket_id CHAR(36) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL,
  name VARCHAR(255) NULL,
  status ENUM('active','disabled','used') DEFAULT 'active',
  ip_address VARCHAR(100) NULL,
  user_agent TEXT NULL,
  sent_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ticket_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_event_email (event_id, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- waitlist
CREATE TABLE IF NOT EXISTS ticket_waitlist (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id INT UNSIGNED NOT NULL,
  email VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_waitlist_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_waitlist (event_id, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
