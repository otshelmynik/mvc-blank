-- Начальная схема БД
-- Создание базы данных
CREATE DATABASE IF NOT EXISTS mvc_blank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mvc_blank;

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL
);

-- Таблица постов (user_id опциональный, без foreign key)
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    user_id INT NULL,
    created_at DATETIME NOT NULL
);
