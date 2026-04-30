-- S-ART / Shary on Tour - Seed Data

DELETE FROM events;

INSERT INTO events
  (title, slug, event_date, event_time, city, location_name, address, description_short, description_long, image_path, status, is_opening, max_tickets, google_maps_url)
VALUES
  ('Vernissage Essen', 'vernissage-essen', '2026-04-18', '19:00:00', 'Essen', 'S-Art Studio', NULL,
   'Die Vernissage in Essen war der Auftakt der diesjährigen Tour.',
   'Bilder, Reaktionen und Eindrücke aus der Vernissage in Essen findest du in der Galerie.',
   '/assets/img/events/vernissage-essen.jpg', 'past', 0, 0, NULL),

  ('Maifest Gutshof', 'maifest-gutshof', '2026-05-01', '12:00:00', 'Gutshof', 'Gutshof', NULL,
   'Pop-Art trifft Maifest – Live-Painting & Atmosphäre.',
   'S-Art beim Maifest Gutshof: Live-Painting, Pop-Art Stände und Tour-Atmosphäre.',
   '/assets/img/events/maifest-gutshof.jpg', 'upcoming', 0, 0,
   'https://www.google.com/maps/search/?api=1&query=Gutshof+Maifest'),

  ('Weinfest im Messinghof', 'weinfest-messinghof', '2026-06-06', '15:00:00', 'Messinghof', 'Messinghof', NULL,
   'Wein, Pop-Art und Street-Vibes im Messinghof.',
   'Beim Weinfest im Messinghof zeigt Shary neue Werke und ist live vor Ort.',
   '/assets/img/events/weinfest-messinghof.jpg', 'upcoming', 0, 0,
   'https://www.google.com/maps/search/?api=1&query=Messinghof+Weinfest'),

  ('Container Opening Kassel', 'container-opening-kassel', '2026-08-22', '18:00:00', 'Kassel', NULL, NULL,
   'Das Highlight der Tour – Container Opening in Kassel.',
   'Das große Container Opening in Kassel: Pop-Art Show, Live-Acts & exklusives Erlebnis. Der genaue Standort wird rechtzeitig bekanntgegeben.',
   '/assets/img/events/container-opening-kassel.jpg', 'upcoming', 1, 600, NULL);
