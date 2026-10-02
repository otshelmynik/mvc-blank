<?php

/**
 * Основной файл конфигураций.
 * Значения из .env переопределяют эти.
 */

return [
    // Приложение
    'app' => [
        'name' => 'MVC Application',
        'debug' => true,
        'url' => 'http://localhost:8080',
        'timezone' => 'Europe/Moscow',
    ],

    // База данных (все значения из .env)
    'db' => [
        'host' => '',
        'port' => '',
        'name' => '',
        'user' => '',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
];
