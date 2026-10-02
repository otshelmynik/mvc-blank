<?php

/**
 * Файл маршрутизации.
 * Формат: 'URI' => ['controller' => 'Name', 'action' => 'methodName']
 *
 * Поддерживаются параметризованные роуты: users/{id}
 */

return [
    // Главная страница
    '/' => [
        'controller' => 'HomeController',
        'action' => 'index',
    ],

    // Пользователи
    'users' => [
        'controller' => 'UserController',
        'action' => 'index',
    ],
    'users/create' => [
        'controller' => 'UserController',
        'action' => 'create',
    ],
    'users/{id}' => [
        'controller' => 'UserController',
        'action' => 'show',
    ],
    'users/{id}/edit' => [
        'controller' => 'UserController',
        'action' => 'edit',
    ],
    'users/{id}/delete' => [
        'controller' => 'UserController',
        'action' => 'destroy',
    ],

    // Посты
    'posts' => [
        'controller' => 'PostController',
        'action' => 'index',
    ],
    'posts/create' => [
        'controller' => 'PostController',
        'action' => 'create',
    ],
    'posts/{id}' => [
        'controller' => 'PostController',
        'action' => 'show',
    ],
    'posts/{id}/edit' => [
        'controller' => 'PostController',
        'action' => 'edit',
    ],
    'posts/{id}/delete' => [
        'controller' => 'PostController',
        'action' => 'destroy',
    ],
];
