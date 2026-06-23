<?php

class User extends Model
{
    protected static string $table = 'users';

    // Crée un utilisateur avec mot de passe hashé
    public static function register(string $username, string $email, string $password): string
    {
        return static::create([
            'username'   => $username,
            'email'      => $email,
            'password'   => Auth::hashPassword($password),
            'role'       => 'user',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // Tente de connecter un utilisateur
    public static function attempt(string $email, string $password): ?array
    {
        $user = static::findBy(['email' => $email]);
        if ($user && Auth::verifyPassword($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    // Classement complet : tous les joueurs triés par victoires puis coins
    public static function ranking(): array
    {
        return Database::query("
            SELECT u.id, u.username, u.role, u.coins,
                   (SELECT COUNT(*) FROM battles b WHERE b.winner_id = u.id) AS wins,
                   (SELECT COALESCE(SUM(uc.quantity), 0) FROM user_cards uc WHERE uc.user_id = u.id) AS cards
            FROM users u
            ORDER BY wins DESC, u.coins DESC, u.username ASC
        ");
    }
}
