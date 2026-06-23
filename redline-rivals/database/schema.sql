-- =============================================
-- REDLINE RIVALS - Schéma BDD
-- =============================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- ---------------------------------------------
-- USERS
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username`   VARCHAR(50)  NOT NULL UNIQUE,
    `email`      VARCHAR(150) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `role`       ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    `coins`      INT UNSIGNED NOT NULL DEFAULT 500,
    `avatar`     VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- CARDS
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `cards` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `rarity`      ENUM('common', 'rare', 'epic', 'legendary') NOT NULL DEFAULT 'common',
    `type`        ENUM('sport', 'muscle', 'tuner', 'hypercar', 'truck') NOT NULL DEFAULT 'sport',
    `speed`       TINYINT UNSIGNED NOT NULL DEFAULT 50 COMMENT 'Stat vitesse 1-100',
    `power`       TINYINT UNSIGNED NOT NULL DEFAULT 50 COMMENT 'Stat puissance 1-100',
    `handling`    TINYINT UNSIGNED NOT NULL DEFAULT 50 COMMENT 'Stat maniabilité 1-100',
    `armor`       TINYINT UNSIGNED NOT NULL DEFAULT 50 COMMENT 'Stat blindage 1-100',
    `image`       VARCHAR(255) DEFAULT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- BOOSTERS
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `boosters` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `price`       INT UNSIGNED NOT NULL DEFAULT 100 COMMENT 'Prix en coins',
    `card_count`  TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Nb cartes par ouverture',
    `image`       VARCHAR(255) DEFAULT NULL,
    `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- BOOSTER_CARDS (pool de cartes par booster + taux de drop)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `booster_cards` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `booster_id` INT UNSIGNED NOT NULL,
    `card_id`    INT UNSIGNED NOT NULL,
    `drop_rate`  DECIMAL(5,2) NOT NULL DEFAULT 10.00 COMMENT 'Taux de drop en %',
    FOREIGN KEY (`booster_id`) REFERENCES `boosters`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`card_id`)    REFERENCES `cards`(`id`)    ON DELETE CASCADE,
    UNIQUE KEY `unique_booster_card` (`booster_id`, `card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- USER_CARDS (collection du joueur)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `user_cards` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`     INT UNSIGNED NOT NULL,
    `card_id`     INT UNSIGNED NOT NULL,
    `quantity`    INT UNSIGNED NOT NULL DEFAULT 1,
    `obtained_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`card_id`) REFERENCES `cards`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_card` (`user_id`, `card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- USER_BOOSTERS (boosters achetés non ouverts)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `user_boosters` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`      INT UNSIGNED NOT NULL,
    `booster_id`   INT UNSIGNED NOT NULL,
    `quantity`     INT UNSIGNED NOT NULL DEFAULT 1,
    `purchased_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`)    ON DELETE CASCADE,
    FOREIGN KEY (`booster_id`) REFERENCES `boosters`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_booster` (`user_id`, `booster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- DONNÉES DE TEST
-- ---------------------------------------------

-- Admin par défaut (mot de passe: admin123)
INSERT INTO `users` (`username`, `email`, `password`, `role`, `coins`) VALUES
('admin', 'admin@redline.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 9999);

-- Quelques cartes de base
INSERT INTO `cards` (`name`, `description`, `rarity`, `type`, `speed`, `power`, `handling`, `armor`) VALUES
('Phantom GT',     'Une hypercar fantôme aux lignes acérées.',          'legendary', 'hypercar', 95, 90, 85, 40),
('Neon Viper',     'Un tuner modifié néon qui brûle l\'asphalte.',      'epic',      'tuner',    88, 75, 92, 35),
('Iron Stallion',  'Un muscle car blindé, inarrêtable.',                'epic',      'muscle',   78, 95, 60, 88),
('Street Runner',  'Une voiture de sport agile et rapide.',             'rare',      'sport',    82, 70, 88, 45),
('Cyber Drift',    'Un tuner spécialisé dans le drift cyberpunk.',      'rare',      'tuner',    75, 68, 95, 38),
('City Cruiser',   'Une voiture de ville fiable et polyvalente.',       'common',    'sport',    60, 55, 65, 55),
('Road Beast',     'Un camion modifié pour la course underground.',     'common',    'truck',    55, 85, 40, 95),
('Volt Sprint',    'Une voiture électrique silencieuse et rapide.',     'rare',      'sport',    85, 65, 80, 42);

-- Un booster de base
INSERT INTO `boosters` (`name`, `description`, `price`, `card_count`) VALUES
('Pack Standard',  'Un pack de 5 cartes aléatoires.',           100, 5),
('Pack Premium',   'Un pack de 5 cartes, plus de rares garanties.', 250, 5),
('Pack Légendaire','Un pack de 3 cartes avec légendaire garantie.', 500, 3);

-- Taux de drop Pack Standard
INSERT INTO `booster_cards` (`booster_id`, `card_id`, `drop_rate`) VALUES
(1, 1, 2.00),   -- Phantom GT - 2%
(1, 2, 5.00),   -- Neon Viper - 5%
(1, 3, 5.00),   -- Iron Stallion - 5%
(1, 4, 15.00),  -- Street Runner - 15%
(1, 5, 15.00),  -- Cyber Drift - 15%
(1, 6, 20.00),  -- City Cruiser - 20%
(1, 7, 20.00),  -- Road Beast - 20%
(1, 8, 18.00);  -- Volt Sprint - 18%

-- Taux de drop Pack Premium
INSERT INTO `booster_cards` (`booster_id`, `card_id`, `drop_rate`) VALUES
(2, 1, 5.00),
(2, 2, 10.00),
(2, 3, 10.00),
(2, 4, 20.00),
(2, 5, 20.00),
(2, 6, 15.00),
(2, 7, 10.00),
(2, 8, 10.00);

-- Taux de drop Pack Légendaire
INSERT INTO `booster_cards` (`booster_id`, `card_id`, `drop_rate`) VALUES
(3, 1, 33.34),
(3, 2, 16.66),
(3, 3, 16.66),
(3, 4, 11.11),
(3, 5, 11.11),
(3, 8, 11.12);

-- ---------------------------------------------
-- DECKS
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `decks` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT UNSIGNED NOT NULL,
    `name`       VARCHAR(100) NOT NULL DEFAULT 'Mon deck',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- DECK_CARDS (cartes dans un deck)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `deck_cards` (
    `id`      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `deck_id` INT UNSIGNED NOT NULL,
    `card_id` INT UNSIGNED NOT NULL,
    FOREIGN KEY (`deck_id`) REFERENCES `decks`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`card_id`) REFERENCES `cards`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_deck_card` (`deck_id`, `card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- BATTLES (combats tour par tour)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `battles` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `player1_id`   INT UNSIGNED NOT NULL,
    `player2_id`   INT UNSIGNED DEFAULT NULL,
    `deck1_id`     INT UNSIGNED NOT NULL,
    `deck2_id`     INT UNSIGNED DEFAULT NULL,
    `status`       ENUM('waiting', 'active', 'finished') NOT NULL DEFAULT 'waiting',
    `winner_id`    INT UNSIGNED DEFAULT NULL,
    `game_state`   JSON NOT NULL COMMENT 'État du jeu : mains, tour, rounds joués',
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `finished_at`  DATETIME DEFAULT NULL,
    FOREIGN KEY (`player1_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`player2_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`deck1_id`)   REFERENCES `decks`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`deck2_id`)   REFERENCES `decks`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`winner_id`)  REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- MARKET_LISTINGS (vente à prix fixe)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `market_listings` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `seller_id`  INT UNSIGNED NOT NULL,
    `buyer_id`   INT UNSIGNED DEFAULT NULL,
    `card_id`    INT UNSIGNED NOT NULL,
    `price`      INT UNSIGNED NOT NULL,
    `quantity`   INT UNSIGNED NOT NULL DEFAULT 1,
    `status`     ENUM('active', 'sold', 'cancelled') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `sold_at`    DATETIME DEFAULT NULL,
    FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`buyer_id`)  REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`card_id`)   REFERENCES `cards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- AUCTIONS (enchères)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `auctions` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `seller_id`       INT UNSIGNED NOT NULL,
    `card_id`         INT UNSIGNED NOT NULL,
    `start_price`     INT UNSIGNED NOT NULL COMMENT 'Mise de départ',
    `current_bid`     INT UNSIGNED NOT NULL COMMENT 'Enchère actuelle',
    `current_bidder_id` INT UNSIGNED DEFAULT NULL,
    `min_increment`   INT UNSIGNED NOT NULL DEFAULT 10 COMMENT 'Surenchère minimale',
    `status`          ENUM('active', 'finished', 'cancelled') NOT NULL DEFAULT 'active',
    `ends_at`         DATETIME NOT NULL COMMENT 'Date de fin de l\'enchère',
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`seller_id`)        REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`card_id`)          REFERENCES `cards`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`current_bidder_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
