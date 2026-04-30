-- Seed data for S-ART / Shary on Tour

INSERT INTO events
  (title, slug, event_date, event_time, city, location_name, address, description_short, description_long, image_path, status, is_featured, is_main_event, has_tickets, max_tickets, hide_address)
VALUES
  ('Vernissage Essen', 'vernissage-essen', '2026-04-18', '19:00:00', 'Essen', NULL, NULL,
   'Vernissage mit Live-Painting und exklusiven Werken.',
   'Die offizielle Vernissage in Essen – cinematic Street-Art, Pop-Art und limitierte Werke. Bilder ansehen in der Galerie.',
   '/assets/img/event-vernissage-essen.jpg', 'past', 0, 0, 0, 0, 0),

  ('Maifest Gutshof', 'maifest-gutshof', '2026-05-01', '14:00:00', 'Gutshof', NULL, NULL,
   'Open-Air Maifest mit S-ART Showcase.',
   'S-ART zu Gast beim Maifest am Gutshof. Live-Walls, Drinks und Pop-Art-Vibes.',
   '/assets/img/event-maifest-gutshof.jpg', 'upcoming', 1, 0, 0, 0, 0),

  ('Weinfest im Messinghof', 'weinfest-messinghof', '2026-06-06', '17:00:00', 'Messinghof', NULL, NULL,
   'Weinfest mit Pop-Art Installationen.',
   'Cinematic Street-Art Energy beim Weinfest im historischen Messinghof.',
   '/assets/img/event-weinfest-messinghof.jpg', 'upcoming', 1, 0, 0, 0, 0),

  ('Container Opening Kassel', 'container-opening-kassel', '2026-08-22', '18:00:00', 'Kassel', NULL, NULL,
   'Das große Container Opening – Gratis-Tickets sichern.',
   'Das Haupt-Event 2026: das offizielle Container Opening in Kassel. Pop-Art goes viral – bekannt aus „Die Geissens“. Sichere dir dein gratis Ticket – limitiert auf 600 Stück.',
   '/assets/img/event-container-opening-kassel.jpg', 'upcoming', 1, 1, 1, 600, 1);
