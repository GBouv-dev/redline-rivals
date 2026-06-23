<?php

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    // Récupère tous les enregistrements
    public static function all(): array
    {
        return Database::query("SELECT * FROM " . static::$table);
    }

    // Récupère un enregistrement par ID
    public static function find(int $id): ?array
    {
        return Database::queryOne(
            "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?",
            [$id]
        );
    }

    // Récupère avec conditions : User::where(['email' => 'test@test.com'])
    public static function where(array $conditions): array
    {
        $clauses = implode(' AND ', array_map(fn($k) => "{$k} = ?", array_keys($conditions)));
        return Database::query(
            "SELECT * FROM " . static::$table . " WHERE {$clauses}",
            array_values($conditions)
        );
    }

    // Récupère un seul résultat avec conditions
    public static function findBy(array $conditions): ?array
    {
        $clauses = implode(' AND ', array_map(fn($k) => "{$k} = ?", array_keys($conditions)));
        return Database::queryOne(
            "SELECT * FROM " . static::$table . " WHERE {$clauses} LIMIT 1",
            array_values($conditions)
        );
    }

    // Insère un nouvel enregistrement, retourne l'ID
    public static function create(array $data): string
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        Database::execute(
            "INSERT INTO " . static::$table . " ({$columns}) VALUES ({$placeholders})",
            array_values($data)
        );
        return Database::lastInsertId();
    }

    // Met à jour un enregistrement par ID
    public static function update(int $id, array $data): int
    {
        $set = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;
        return Database::execute(
            "UPDATE " . static::$table . " SET {$set} WHERE " . static::$primaryKey . " = ?",
            $values
        );
    }

    // Supprime un enregistrement par ID
    public static function delete(int $id): int
    {
        return Database::execute(
            "DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?",
            [$id]
        );
    }

    // Compte les enregistrements
    public static function count(array $conditions = []): int
    {
        if (empty($conditions)) {
            $result = Database::queryOne("SELECT COUNT(*) as total FROM " . static::$table);
        } else {
            $clauses = implode(' AND ', array_map(fn($k) => "{$k} = ?", array_keys($conditions)));
            $result = Database::queryOne(
                "SELECT COUNT(*) as total FROM " . static::$table . " WHERE {$clauses}",
                array_values($conditions)
            );
        }
        return (int) ($result['total'] ?? 0);
    }
}
