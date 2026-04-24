INSERT INTO events (title, slug, event_date, event_time, city, location_name, address, description_short, description_long, image_path, status, is_featured) VALUES
('Container Opening Night Berlin', 'container-opening-berlin', '2026-06-10', '19:00:00', 'Berlin', 'RAW Gelände', 'Revaler Str. 99', 'Launch Event mit Live-Art und DJ.', 'Großes Opening der Sommer-Tour mit Künstler-Talks und Performance.', '/assets/img/event-berlin.jpg', 'upcoming', 1),
('Street Colors Hamburg', 'street-colors-hamburg', '2026-07-04', '18:00:00', 'Hamburg', 'Oberhafenquartier', 'Stockmeyerstr. 43', 'Urban-Art Abend im Hafen.', 'Container-Installation, Lichtshow und Talks.', '/assets/img/event-hamburg.jpg', 'upcoming', 0),
('Night Shapes Köln', 'night-shapes-koeln', '2025-10-02', '20:00:00', 'Köln', 'Ehrenfeld Yard', 'Venloer Str. 300', 'Abendshow mit Projection Mapping.', 'Vergangenes Event der Herbsttour.', '/assets/img/event-koeln.jpg', 'past', 0),
('S-ART x City Walls München', 's-art-city-walls-muenchen', '2025-08-21', '17:00:00', 'München', 'Kreativquartier', 'Dachauer Str. 112d', 'Open-Air Ausstellung.', 'Vergangenes Event mit Workshop und Talk.', '/assets/img/event-muenchen.jpg', 'past', 0);

INSERT INTO artworks (title, slug, description, image_path, collection_name, year, is_visible, sort_order) VALUES
('Neon Pulse', 'neon-pulse', 'Kräftige Farbflächen und dynamische Linien.', '/assets/img/art-neon-pulse.jpg', 'City Beats', '2026', 1, 1),
('Urban Bloom', 'urban-bloom', 'Florale Formen treffen auf Graffiti-Strukturen.', '/assets/img/art-urban-bloom.jpg', 'City Beats', '2026', 1, 2),
('Midnight Echo', 'midnight-echo', 'Dunkle Basis mit poppigen Lichtakzenten.', '/assets/img/art-midnight-echo.jpg', 'After Dark', '2025', 1, 3),
('Electric Faces', 'electric-faces', 'Porträt-Serie mit Street-Art Charakter.', '/assets/img/art-electric-faces.jpg', 'After Dark', '2025', 1, 4);

INSERT INTO tour_locations (title, city, address, date_from, date_to, status, google_maps_url, description) VALUES
('Container Hub Ost', 'Berlin', 'Revaler Str. 99, Berlin', '2026-05-30', NULL, 'current', 'https://maps.google.com', 'Aktueller Hauptstandort der Tour.'),
('Hafen Art Spot', 'Hamburg', 'Stockmeyerstr. 43, Hamburg', '2026-07-01', '2026-07-15', 'upcoming', 'https://maps.google.com', 'Nächster Stopp im Norden.'),
('Factory Yard Süd', 'München', 'Dachauer Str. 112d, München', '2026-08-05', '2026-08-20', 'upcoming', 'https://maps.google.com', 'Sommerfinale mit Night Session.');
