-- =============================================
-- REDLINE RIVALS — Finition propre à la copie possédée
-- La finition se tire au sort à l'obtention (probabilité), pas via la rareté.
-- =============================================

ALTER TABLE `user_cards`
    ADD COLUMN `finish` ENUM('classic', 'semiholo', 'holo', 'fullart') NOT NULL DEFAULT 'classic' AFTER `quantity`;

-- Le catalogue redevient "base classique" : les finitions ne sont plus fixées par la rareté.
UPDATE `cards` SET `finish` = 'classic';
