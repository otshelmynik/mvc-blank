<?php

/**
 * Класс для работы с конфигурациями.
 * Загружает configs.php и переопределяет значения из .env.
 * Доступ по dot-notation: Config::get('db.host')
 */
class Config
{
    private static $config = [];
    private static $env = [];

    /**
     * Загрузка конфигураций
     */
    public static function init()
    {
        // Загрузка основного конфига
        $configFile = dirname(__DIR__) . '/configs.php';
        if (file_exists($configFile)) {
            self::$config = require $configFile;
        }

        // Загрузка .env и переопределение
        self::loadEnv();
    }

    /**
     * Получение значения по dot-notation
     * Пример: Config::get('db.host')
     */
    public static function get($key = null, $default = null)
    {
        if ($key === null) {
            return array_replace_recursive(self::$config, self::$env);
        }

        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }

        // .env переопределяет configs.php
        $envValue = self::getEnvValue($keys);
        if ($envValue !== null) {
            return $envValue;
        }

        return $value;
    }

    /**
     * Получение значения из .env
     */
    private static function getEnvValue($keys)
    {
        $value = self::$env;
        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return null;
            }
        }
        return $value;
    }

    /**
     * Загрузка .env файла
     */
    private static function loadEnv()
    {
        $envFile = dirname(__DIR__) . '/.env';
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Пропускаем комментарии
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim(strtolower($key));
                $value = trim($value);

                // Удаляем кавычки если есть
                if ((strpos($value, '"') === 0 && strpos($value, '"') === strlen($value) - 1) ||
                    (strpos($value, "'") === 0 && strpos($value, "'") === strlen($value) - 1)) {
                    $value = substr($value, 1, -1);
                }

                // Преобразуем true/false
                if ($value === 'true') {
                    $value = true;
                } elseif ($value === 'false') {
                    $value = false;
                } elseif (is_numeric($value)) {
                    $value = $value + 0;
                }

                // Поддерживаем вложенные ключи: DB.HOST -> ['db']['host']
                $nestedKeys = explode('.', $key);
                $ref = &self::$env;
                foreach ($nestedKeys as $k) {
                    if (!isset($ref[$k]) || !is_array($ref[$k])) {
                        $ref[$k] = [];
                    }
                    $ref = &$ref[$k];
                }
                $ref = $value;
                unset($ref);
            }
        }
    }
}
