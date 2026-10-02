<?php

/**
 * Базовая модель.
 * Автоматически определяет имя таблицы (snake_case от имени класса).
 * Подключается к БД из конфигурации.
 */
abstract class Model
{
    /**
     * Имя таблицы (определяется автоматически)
     */
    protected static $table = null;

    /**
     * PDO подключение к БД
     */
    private static $pdo = null;

    /**
     * Получить PDO подключение
     */
    protected static function db()
    {
        if (self::$pdo === null) {
            $dbname = Config::get('db.name', 'mvc_blank');
            $user = Config::get('db.user', 'mvc_blank');
            $pass = Config::get('db.password', 'mvc_blank');
            $charset = Config::get('db.charset', 'utf8mb4');
            $socket = Config::get('db.socket', '');
            $host = Config::get('db.host', '127.0.0.1');
            $port = Config::get('db.port', 3306);

            // Если указан сокет — используем его (OpenServer)
            if ($socket) {
                $dsn = "mysql:unix_socket={$socket};dbname={$dbname};charset={$charset}";
            } else {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
            }

            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$pdo;
    }

    /**
     * Получить имя таблицы
     */
    protected static function getTable()
    {
        if (self::$table === null) {
            // Берём имя класса, убираем префикс и преобразуем в snake_case
            $class = basename(str_replace('\\', '/', get_called_class()));
            $class = preg_replace('/Model$/', '', $class);
            self::$table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class)) . 's';
        }
        return self::$table;
    }

    /**
     * Получить все записи
     *
     * @param string $orderBy Поле для сортировки
     * @param int $limit Лимит записей
     * @return array
     */
    public static function findAll($orderBy = 'id DESC', $limit = null)
    {
        $table = static::getTable();
        $sql = "SELECT * FROM {$table} ORDER BY {$orderBy}";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = static::db()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Получить запись по ID
     *
     * @param int $id
     * @return array|null
     */
    public static function findById($id)
    {
        $table = static::getTable();
        $stmt = static::db()->prepare("SELECT * FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Создать запись
     *
     * @param array $data Данные для вставки
     * @return int ID последней вставленной записи
     */
    public static function create($data)
    {
        $table = static::getTable();
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = static::db()->prepare($sql);

        // Привязываем параметры с явным указанием типа для null
        foreach ($data as $key => $value) {
            if ($value === null) {
                $stmt->bindValue($key, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();

        return (int) static::db()->lastInsertId();
    }

    /**
     * Обновить запись
     *
     * @param int $id ID записи
     * @param array $data Данные для обновления
     * @return bool
     */
    public static function update($id, $data)
    {
        $table = static::getTable();
        
        // Формируем SET: name = :name, email = :email
        $setParts = [];
        foreach (array_keys($data) as $key) {
            $setParts[] = "{$key} = :{$key}";
        }
        $set = implode(', ', $setParts);

        // Добавляем :id для условия
        $data['id'] = $id;
        $set .= ' WHERE id = :id';

        $sql = "UPDATE {$table} SET {$set}";
        $stmt = static::db()->prepare($sql);

        // Привязываем параметры с явным указанием типа для null
        foreach ($data as $key => $value) {
            if ($value === null) {
                $stmt->bindValue($key, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        return $stmt->execute();
    }

    /**
     * Удалить запись
     *
     * @param int $id
     * @return bool
     */
    public static function delete($id)
    {
        $table = static::getTable();
        $stmt = static::db()->prepare("DELETE FROM {$table} WHERE id = ?");

        return $stmt->execute([$id]);
    }

    /**
     * Получить количество записей
     *
     * @param string $where WHERE условие (без слова WHERE)
     * @param array $params Параметры
     * @return int
     */
    public static function count($where = '', $params = [])
    {
        $table = static::getTable();
        $sql = "SELECT COUNT(*) as total FROM {$table}";

        if ($where) {
            $sql .= " WHERE {$where}";
        }

        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetch()['total'];
    }

    /**
     * Выполнить произвольный запрос
     *
     * @param string $sql SQL запрос
     * @param array $params Параметры
     * @return array
     */
    public static function query($sql, $params = [])
    {
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
