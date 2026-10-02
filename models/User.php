<?php

/**
 * Модель пользователя.
 * Таблица: users
 *
 * Примеры использования:
 *   $users = User::findAll();
 *   $user = User::findById(1);
 *   User::create(['name' => 'Иван', 'email' => 'ivan@example.com']);
 *   User::update(1, ['name' => 'Иван Иванович']);
 *   User::delete(1);
 */
class User extends Model
{
    protected static $table = 'users';

    /**
     * Переопределяем getTable для явного указания таблицы
     */
    protected static function getTable()
    {
        return 'users';
    }

    /**
     * Найти пользователя по email
     */
    public static function findByEmail($email)
    {
        $stmt = static::db()->prepare("SELECT * FROM " . static::getTable() . " WHERE email = ?");
        $stmt->execute([$email]);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Получить посты пользователя
     */
    public static function getPosts($userId)
    {
        return static::query(
            "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }

    /**
     * Удалить пользователя (без удаления постов)
     */
    public static function deleteWithoutCascade($id)
    {
        $table = static::getTable();
        $stmt = static::db()->prepare("DELETE FROM {$table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
