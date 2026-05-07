-- 0001_add_event_video.sql
-- Adds optional video_path column to events for video-capable event cards.
-- Apply once per environment:  mysql ... < database/migrations/0001_add_event_video.sql

ALTER TABLE events
  ADD COLUMN video_path VARCHAR(500) NULL AFTER image_path;
