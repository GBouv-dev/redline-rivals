-- Récompenses de quêtes réclamées (la progression est calculée à la volée)
CREATE TABLE IF NOT EXISTS `user_quests` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT UNSIGNED NOT NULL,
    `quest_key`  VARCHAR(50) NOT NULL,
    `claimed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_user_quest` (`user_id`, `quest_key`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
