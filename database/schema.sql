CREATE TABLE IF NOT EXISTS events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  event_date DATE NOT NULL,
  event_time TIME NULL,
  city VARCHAR(120) NOT NULL,
  location_name VARCHAR(160) NOT NULL,
  address VARCHAR(255) NULL,
  description_short VARCHAR(255) NULL,
  description_long TEXT NULL,
  image_path VARCHAR(255) NULL,
  status ENUM('upcoming','past','draft') NOT NULL DEFAULT 'draft',
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  INDEX idx_events_status_date (status, event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS artworks (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  description TEXT NULL,
  image_path VARCHAR(255) NULL,
  collection_name VARCHAR(140) NULL,
  year SMALLINT NULL,
  is_visible TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  INDEX idx_artworks_visible_order (is_visible, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tour_locations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  city VARCHAR(120) NOT NULL,
  address VARCHAR(255) NULL,
  date_from DATE NULL,
  date_to DATE NULL,
  status ENUM('current','upcoming','past','draft') NOT NULL DEFAULT 'draft',
  google_maps_url VARCHAR(255) NULL,
  description TEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  INDEX idx_locations_status_date (status, date_from)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  first_name VARCHAR(80) NULL,
  source VARCHAR(80) NOT NULL,
  consent_privacy TINYINT(1) NOT NULL DEFAULT 0,
  double_opt_in_token VARCHAR(120) NULL,
  double_opt_in_confirmed_at DATETIME NULL,
  ticket_token VARCHAR(120) NOT NULL UNIQUE,
  ticket_sent_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  ip_address VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  INDEX idx_subscribers_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ticket_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  subscriber_id INT UNSIGNED NOT NULL,
  email VARCHAR(190) NOT NULL,
  ticket_token VARCHAR(120) NOT NULL,
  sent_at DATETIME NOT NULL,
  status VARCHAR(40) NOT NULL,
  error_message TEXT NULL,
  CONSTRAINT fk_ticket_logs_subscriber
    FOREIGN KEY (subscriber_id) REFERENCES newsletter_subscribers(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
