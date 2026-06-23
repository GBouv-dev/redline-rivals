-- Finitions cumulées : liste des finitions obtenues par carte ; `finish` = finition affichée
ALTER TABLE `user_cards`
    ADD COLUMN `finishes_owned` SET('classic','semiholo','holo','fullart') NOT NULL DEFAULT 'classic' AFTER `finish`;
UPDATE `user_cards` SET `finishes_owned` = `finish`;
