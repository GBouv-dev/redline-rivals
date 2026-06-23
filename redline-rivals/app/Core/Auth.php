<?php

class Auth
{
    // Vérifie si un utilisateur est connecté
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    // Vérifie si l'utilisateur est admin
    public static function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    // Retourne l'ID de l'utilisateur connecté
    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    // Retourne l'utilisateur connecté
    public static function user(): ?array
    {
        if (!self::check()) return null;
        return $_SESSION['user'] ?? null;
    }

    // Connecte un utilisateur (à appeler après vérification BDD)
    public static function login(array $user): void
    {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
        $_SESSION['user']      = $user;
        session_regenerate_id(true);
    }

    // Déconnecte l'utilisateur
    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    // Hash un mot de passe
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Vérifie un mot de passe contre son hash
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
