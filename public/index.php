<?php

/**
 * Точка входа в приложение.
 * Загружает конфиги, регистрирует автозагрузчик, обрабатывает маршрут.
 */

// Автозагрузка классов — регистрируем ПЕРВЫМ делом
spl_autoload_register(function ($class) {
    // Преобразуем namespace в путь
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    // Проверяем models/ (перед app/, чтобы User/Post загружались правильно)
    $file = dirname(__DIR__) . '/models/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }

    // Проверяем app/
    $file = dirname(__DIR__) . '/app/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }

    // Проверяем controllers/
    $file = dirname(__DIR__) . '/controllers/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }
});

// Инициализируем конфигурации
Config::init();

// Запускаем сессию
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Устанавливаем часовую зону
date_default_timezone_set(Config::get('app.timezone', 'UTC'));

// Включаем режим отладки
if (Config::get('app.debug', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
}

// Инициализируем конфигурации
Config::init();

// Запускаем сессию
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Загружаем маршруты и диспетчим
Router::load();
Router::dispatch();
