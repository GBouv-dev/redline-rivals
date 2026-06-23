-- =============================================
-- REDLINE RIVALS — Finition des cartes (axe distinct de la rareté)
-- classic | semiholo | holo | fullart
-- =============================================

ALTER TABLE `cards`
    ADD COLUMN `finish` ENUM('classic', 'semiholo', 'holo', 'fullart') NOT NULL DEFAULT 'classic' AFTER `image`;

-- Attribution par défaut selon la rareté (modifiable ensuite dans l'admin)
UPDATE `cards` SET `finish` = 'fullart'  WHERE `rarity` = 'legendary';
UPDATE `cards` SET `finish` = 'holo'     WHERE `rarity` = 'epic';
UPDATE `cards` SET `finish` = 'semiholo' WHERE `rarity` = 'rare';
UPDATE `cards` SET `finish` = 'classic'  WHERE `rarity` = 'common';
