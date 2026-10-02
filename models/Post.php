<?php

/**
 * Модель поста.
 * Таблица: posts
 * Связь: belongsTo User (user_id)
 *
 * Примеры использования:
 *   $posts = Post::findAll();
 *   $post = Post::findById(1);
 *   Post::create(['title' => 'Заголовок', 'body' => 'Текст', 'user_id' => 1]);
 *   Post::update(1, ['title' => 'Новый заголовок']);
 *   Post::delete(1);
 */
class Post extends Model
{
    protected static $table = 'posts';

    /**
     * Переопределяем getTable для явного указания таблицы
     */
    protected static function getTable()
    {
        return 'posts';
    }

    /**
     * Получить автора поста
     */
    public static function getAuthor($postId)
    {
        return static::query(
            "SELECT u.* FROM users u LEFT JOIN posts p ON u.id = p.user_id WHERE p.id = ?",
            [$postId]
        );
    }

    /**
     * Получить имя автора поста
     */
    public static function getAuthorName($postId)
    {
        $result = static::query(
            "SELECT u.name FROM users u LEFT JOIN posts p ON u.id = p.user_id WHERE p.id = ?",
            [$postId]
        );
        return $result[0]['name'] ?? 'Не указан';
    }

    /**
     * Получить посты пользователя
     */
    public static function getByUser($userId, $limit = null)
    {
        $sql = "SELECT * FROM " . static::getTable() . " WHERE user_id = ? ORDER BY created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = static::db()->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }
}
