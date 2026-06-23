-- =============================================
-- REDLINE RIVALS — Lot de cartes supplémentaires
-- 15 voitures équilibrées par rareté et par type.
-- Import : mysql -u root redline_rivals < database/cards_extra.sql
-- (images laissées vides -> icône de type par défaut en jeu)
-- =============================================

INSERT INTO `cards` (`name`, `description`, `rarity`, `type`, `speed`, `power`, `handling`, `armor`) VALUES
-- ── Légendaires (S) ──
('Bugatti Chiron',         'Le monstre quad-turbo qui repousse les limites du bitume.',      'legendary', 'hypercar', 98, 96, 80, 62),
('Koenigsegg Jesko',       'Une fusée suédoise qui tutoie les 500 km/h.',                    'legendary', 'hypercar', 99, 94, 84, 56),
('Pagani Huayra',          'Œuvre d''art italienne propulsée par un V12 AMG bi-turbo.',       'legendary', 'hypercar', 95, 91, 88, 70),
-- ── Épiques (A) ──
('Nissan GT-R R35',        'Godzilla : la transmission intégrale ne pardonne rien.',         'epic',      'tuner',    90, 86, 88, 60),
('Dodge Challenger Hellcat','707 chevaux de pure fureur américaine.',                        'epic',      'muscle',   84, 96, 62, 82),
('Lamborghini Huracán',    'Le taureau italien hurlant à plein régime.',                     'epic',      'hypercar', 92, 89, 83, 56),
('Toyota Supra MK4',       'Légende du tuning au 2JZ inarrêtable.',                          'epic',      'tuner',    88, 83, 85, 58),
-- ── Rares (B) ──
('Subaru WRX STI',         'La symphonie boxer taillée pour le rallye.',                     'rare',      'sport',    78, 72, 84, 58),
('Ford Mustang GT',        'Le pony car qui gronde dans chaque virage.',                     'rare',      'muscle',   80, 83, 62, 70),
('Mitsubishi Lancer Evo',  'Reine des spéciales, vive comme l''éclair.',                     'rare',      'tuner',    77, 70, 87, 54),
('Chevrolet Camaro SS',    'Le V8 small-block dans toute sa splendeur.',                     'rare',      'muscle',   79, 80, 64, 70),
-- ── Communes (C) ──
('Volkswagen Golf GTI',    'La hot-hatch polyvalente par excellence.',                       'common',    'sport',    69, 61, 75, 56),
('Honda Civic Type R',     'Traction avant affûtée, prête à en découdre.',                   'common',    'sport',    73, 65, 79, 52),
('Ford F-150 Raptor',      'Le pick-up tout-terrain qui écrase tout sur son passage.',       'common',    'truck',    59, 81, 46, 93),
('Ford Bronco',            'Baroudeur né pour dévorer les sentiers tout-terrain.',                      'common',    'truck',    53, 71, 49, 89);
