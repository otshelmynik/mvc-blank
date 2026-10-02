# MVC Application — PHP Framework

Лёгкий PHP MVC-фреймворк без сторонних библиотек. Docker-готовый, с простым роутингом, валидацией и CRUD-операциями.

---

## 📋 Содержание

- [Требования](#-требования)
- [Быстрый старт](#-быстрый-старт)
- [Структура проекта](#-структура-проекта)
- [Локальная разработка (OpenServer)](#-локальная-разработка-openserver)
- [Разработка в Docker](#-разработка-в-docker)
- [База данных](#-база-данных)
- [Роутинг](#-роутинг)
- [Создание нового модуля](#-создание-нового-модуля)
- [Валидация](#-валидация)
- [Деплой на продакшн](#-деплой-на-продакшн)
- [Конфигурация](#-конфигурация)
- [Поддержка](#-поддержка)

---

## 🖥 Требования

### Локальная разработка (OpenServer)
- OpenServer 5+
- PHP 8.0+
- MySQL 5.7+ / 8.0

### Docker
- Docker 20.10+
- Docker Compose 2.0+

### Продакшн
- Linux (Ubuntu 22.04+)
- Apache 2.4 / Nginx 1.24+
- PHP 8.2+ с расширениями: `pdo`, `pdo_mysql`, `mysqli`
- MySQL 8.0+

---

## 🚀 Быстрый старт

### Вариант 1: OpenServer

1. Клонируйте проект в директорию OpenServer:
   ```
   D:/OpenServer/domains/mvc-blank
   ```

2. Импортируйте базу данных:
   ```sql
   -- В phpMyAdmin или через консоль:
   mysql -u root -p < migrations/001_initial_schema.sql
   ```

3. Настройте `.env`:
   ```env
   DB.HOST=127.0.0.1
   DB.PORT=3316
   DB.NAME=mvc_blank
   DB.USER=root
   DB.PASSWORD=root
   DB.SOCKET=
   ```

4. Откройте http://mvc-blank.loc

### Вариант 2: Docker

1. Клонируйте проект:
   ```bash
   git clone <repository-url> mvc-blank
   cd mvc-blank
   ```

2. Настройте `.env` для Docker:
   ```env
   DB.HOST=db
   DB.PORT=3306
   DB.NAME=mvc_blank
   DB.USER=mvc_user
   DB.PASSWORD=mvc_password
   DB.SOCKET=
   ```

3. Запустите контейнеры:
   ```bash
   docker-compose up -d --build
   ```

4. Откройте http://localhost:8080

---

## 📁 Структура проекта

```
mvc-blank/
├── app/                    # Ядро фреймворка
│   ├── Config.php          # Работа с конфигурацией
│   ├── Router.php          # Маршрутизатор
│   ├── Controller.php      # Базовый контроллер
│   ├── Model.php           # Базовая модель (PDO)
│   └── Validator.php       # Валидатор
├── controllers/            # Контроллеры
│   ├── HomeController.php
│   ├── UserController.php
│   └── PostController.php
├── models/                 # Модели данных
│   ├── User.php
│   └── Post.php
├── views/                  # Представления (шаблоны)
│   ├── layouts/
│   │   └── main.php
│   ├── home/
│   │   └── index.php
│   ├── users/
│   │   ├── index.php
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── show.php
│   └── posts/
│       ├── index.php
│       ├── create.php
│       ├── edit.php
│       └── show.php
├── public/                 # DocumentRoot
│   ├── index.php           # Точка входа
│   ├── .htaccess           # Правила Apache
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
├── migrations/             # SQL-миграции
│   └── 001_initial_schema.sql
├── .env                    # Переменные окружения
├── configs.php             # Базовая конфигурация
├── routes.php              # Роуты
├── init.sql                # Справка по инициализации
├── Dockerfile              # Образ приложения
├── docker-compose.yml      # Оркестрация
├── apache.conf             # Конфиг Apache в Docker
└── .dockerignore           # Исключения для Docker
```

---

## 💻 Локальная разработка (OpenServer)

### Настройка

1. **Создайте сайт в OpenServer:**
   - Добавьте домен `mvc-blank.loc`
   - DocumentRoot: `D:/OpenServer/domains/mvc-blank/public`

2. **Настройте `.env`:**
   ```env
   DB.HOST=127.0.0.1
   DB.PORT=3316
   DB.NAME=mvc_blank
   DB.USER=root
   DB.PASSWORD=root
   DB.SOCKET=
   ```

3. **Создайте базу данных:**
   ```bash
   mysql -u root -e "CREATE DATABASE mvc_blank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root mvc_blank < migrations/001_initial_schema.sql
   ```

### Режим отладки

В `.env`:
```env
APP.DEBUG=true    # Включить отладку
APP.DEBUG=false   # Выключить для продакшена
```

---

## 🐳 Разработка в Docker

### Команды

```bash
# Запуск
docker-compose up -d --build

# Просмотр логов
docker-compose logs -f app
docker-compose logs -f db

# Остановка
docker-compose down

# Остановка с удалением volumes (⚠️ удалит БД)
docker-compose down -v

# Перезапуск PHP
docker-compose restart app

# Выполнение команд в контейнере
docker-compose exec app php artisan ...
docker-compose exec db mysql -umvc_user -pmvc_password mvc_blank
```

### Доступы

| Сервис    | URL                    | Порт |
|-----------|------------------------|------|
| Приложение | http://localhost:8080  | 8080 |
| MySQL     | localhost:3316         | 3316 |

### Синхронизация кода

Код автоматически синхронизируется через volume:
```yaml
volumes:
  - .:/var/www/html
```

Изменения на хосте сразу применяются в контейнере.

---

## 🗄 База данных

### Схема

**users:**
- `id` INT PRIMARY KEY AUTO_INCREMENT
- `name` VARCHAR(100) NOT NULL
- `email` VARCHAR(150) UNIQUE NOT NULL
- `created_at` DATETIME NOT NULL

**posts:**
- `id` INT PRIMARY KEY AUTO_INCREMENT
- `title` VARCHAR(200) NOT NULL
- `body` TEXT NOT NULL
- `user_id` INT NULL (опционально, без foreign key)
- `created_at` DATETIME NOT NULL

### Миграции

Все миграции находятся в `migrations/`:

```bash
# Применение миграции
docker-compose exec db mysql -umvc_user -pmvc_password mvc_blank < migrations/001_initial_schema.sql

# Для OpenServer:
mysql -u root -p mvc_blank < migrations/001_initial_schema.sql
```

### Создание новой миграции

1. Создайте файл `migrations/002_new_table.sql`
2. Добавьте SQL-код
3. Примените миграцию (см. выше)

Пример:
```sql
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    text TEXT NOT NULL,
    created_at DATETIME NOT NULL
);
```

### Важные особенности

- `user_id` в posts — **опциональный** (INT NULL)
- Удаление пользователя **не удаляет** его посты
- Для запрета удаления пользователя с постами используется проверка в контроллере

---

## 🛣 Роутинг

### Файл конфигурации

`routes.php` — определяет все маршруты:

```php
Router::get('home', 'HomeController', 'index');
Router::get('posts', 'PostController', 'index');
Router::get('posts/create', 'PostController', 'create');
Router::post('posts', 'PostController', 'store');
Router::get('posts/{id}', 'PostController', 'show');
Router::get('posts/{id}/edit', 'PostController', 'edit');
Router::post('posts/{id}/edit', 'PostController', 'update');
Router::get('posts/{id}/delete', 'PostController', 'destroy');
```

### Правила определения методов

| URI Pattern     | Метод контроллера |
|-----------------|-------------------|
| `/resource`     | `index()`         |
| `/resource/create` | `create()`     |
| `POST /resource` | `store()`        |
| `/resource/{id}` | `show($id)`      |
| `/resource/{id}/edit` (GET) | `edit($id)` |
| `/resource/{id}/edit` (POST) | `update($id)` |
| `/resource/{id}/delete` | `destroy($id)` |

### Добавление нового роута

В `routes.php`:
```php
Router::get('comments', 'CommentController', 'index');
Router::post('comments', 'CommentController', 'store');
```

---

## 🔧 Создание нового модуля

### Шаг 1: Модель

Создайте `models/Comment.php`:

```php
class Comment extends Model
{
    protected static $table = 'comments';

    protected static function getTable()
    {
        return 'comments';
    }
}
```

### Шаг 2: Контроллер

Создайте `controllers/CommentController.php`:

```php
class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::findAll('created_at DESC');
        $this->render('comments/index', [
            'title' => 'Комментарии',
            'comments' => $comments,
        ]);
    }

    public function create()
    {
        $this->render('comments/create', [
            'title' => 'Добавить комментарий',
        ]);
    }

    public function store()
    {
        $errors = $this->validate($_POST, [
            'text' => 'required|min:5|max:1000',
        ]);

        if (!empty($errors)) {
            $this->render('comments/create', ['errors' => $errors]);
            return;
        }

        Comment::create([
            'text' => $this->input('text'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->setFlash('success', 'Комментарий создан!');
        $this->redirect('/comments');
    }
}
```

### Шаг 3: Представления

Создайте `views/comments/`:
- `index.php` — список
- `create.php` — форма создания
- `edit.php` — форма редактирования
- `show.php` — детали

### Шаг 4: Роуты

Добавьте в `routes.php`:
```php
Router::get('comments', 'CommentController', 'index');
Router::get('comments/create', 'CommentController', 'create');
Router::post('comments', 'CommentController', 'store');
```

---

## ✅ Валидация

### Доступные правила

| Правило       | Описание                          |
|---------------|-----------------------------------|
| `required`    | Поле обязательно                  |
| `email`       | Валидный email                    |
| `min:N`       | Минимальная длина N               |
| `max:N`       | Максимальная длина N              |
| `numeric`     | Числовое значение                 |
| `url`         | Валидный URL                      |
| `alpha`       | Только буквы                      |
| `alpha_numeric` | Буквы и цифры                  |

### Использование

```php
$errors = $this->validate($_POST, [
    'name'  => 'required|min:2|max:100',
    'email' => 'required|email',
    'age'   => 'numeric|min:18|max:99',
]);

if (!empty($errors)) {
    // Обработка ошибок
}
```

### Доступ к ошибкам

```php
<?php if (isset($errors['email'])): ?>
    <span class="error"><?= implode(', ', $errors['email']) ?></span>
<?php endif; ?>
```

---

## 🚀 Деплой на продакшн

### Подготовка

1. **Скопируйте проект на сервер:**
   ```bash
   scp -r mvc-blank/ user@server:/var/www/mvc-blank
   ```

2. **Настройте `.env` для продакшена:**
   ```env
   DB.HOST=127.0.0.1
   DB.PORT=3306
   DB.NAME=mvc_production
   DB.USER=mvc_user
   DB.PASSWORD=strong_password_here
   DB.SOCKET=

   APP.DEBUG=false
   APP.URL=https://yourdomain.com
   ```

### Деплой на Apache

1. **Установите зависимости:**
   ```bash
   sudo apt update
   sudo apt install apache2 php libapache2-mod-php php-mysql mysql-server
   ```

2. **Настройте Apache:**

   `/etc/apache2/sites-available/mvc-blank.conf`:
   ```apache
   <VirtualHost *:80>
       ServerName yourdomain.com
       DocumentRoot /var/www/mvc-blank/public

       <Directory /var/www/mvc-blank/public>
           AllowOverride All
           Require all granted
       </Directory>

       ErrorLog ${APACHE_LOG_DIR}/error.log
       CustomLog ${APACHE_LOG_DIR}/access.log combined
   </VirtualHost>
   ```

3. **Включите сайт:**
   ```bash
   sudo a2ensite mvc-blank
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

4. **Импортируйте базу данных:**
   ```bash
   mysql -u root -p < migrations/001_initial_schema.sql
   ```

5. **Настройте права:**
   ```bash
   sudo chown -R www-data:www-data /var/www/mvc-blank
   sudo chmod -R 755 /var/www/mvc-blank
   ```

### Деплой с Docker (Production)

1. **Docker Compose для продакшена:**

   `docker-compose.prod.yml`:
   ```yaml
   version: '3.8'

   services:
     app:
       build:
         context: .
         dockerfile: Dockerfile
       ports:
         - "80:80"
       environment:
         DB_HOST: db
         DB_PORT: 3306
         DB_NAME: mvc_production
         DB_USER: mvc_user
         DB_PASSWORD: ${DB_PASSWORD}
       depends_on:
         - db
       restart: always

     db:
       image: mysql:8.0
       environment:
         MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
         MYSQL_DATABASE: mvc_production
         MYSQL_USER: mvc_user
         MYSQL_PASSWORD: ${DB_PASSWORD}
       volumes:
         - mysql_data:/var/lib/mysql
         - ./migrations:/docker-entrypoint-initdb.d
       restart: always

   volumes:
     mysql_data:
   ```

2. **Запуск:**
   ```bash
   export DB_PASSWORD=strong_password
   export MYSQL_ROOT_PASSWORD=strong_root_password
   docker-compose -f docker-compose.prod.yml up -d
   ```

### HTTPS (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
```

### CI/CD (GitHub Actions)

`.github/workflows/deploy.yml`:
```yaml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Deploy to server
        uses: appleboy/ssh-action@v1
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd /var/www/mvc-blank
            git pull origin main
            docker-compose up -d --build
```

---

## ⚙️ Конфигурация

### Файлы конфигурации

| Файл         | Назначение                          |
|--------------|-------------------------------------|
| `.env`       | Переменные окружения (приоритет выше) |
| `configs.php`| Базовая конфигурация (пустые значения для DB) |

### Доступ к конфигу

```php
// Получить значение
$host = Config::get('db.host');
$url = Config::get('app.url');

// Получить все
$config = Config::get();
```

### Переменные окружения

```env
# Database
DB.HOST=db
DB.PORT=3306
DB.NAME=mvc_blank
DB.USER=mvc_user
DB.PASSWORD=mvc_password
DB.SOCKET=

# Application
APP.NAME=MVC Application
APP.DEBUG=true
APP.URL=http://localhost:8080
```

---

## 🛠 Поддержка

### Частые проблемы

#### 1. Ошибка подключения к БД

**Проблема:** `Connection refused`

**Решение:**
- Проверьте, запущен ли MySQL
- Проверьте доступы в `.env`
- Для Docker: `docker-compose ps`

#### 2. 404 на все роуты

**Проблема:** `.htaccess` не работает

**Решение:**
```bash
# Для Apache
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### 3. Ошибка `user_id cannot be null`

**Проблема:** В БД поле не nullable

**Решение:**
```sql
ALTER TABLE posts MODIFY COLUMN user_id INT NULL;
```

#### 4. Права доступа в Linux

```bash
sudo chown -R www-data:www-data /var/www/mvc-blank
sudo chmod -R 755 /var/www/mvc-blank
```

### Логирование

```php
// В Model.php включите ERRMODE_EXCEPTION
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION

// В .env включите отладку
APP.DEBUG=true
```

### Бэкап базы данных

```bash
# Бэкап
mysqldump -u root -p mvc_blank > backup_$(date +%Y%m%d).sql

# Восстановление
mysql -u root -p mvc_blank < backup_20240101.sql
```

---

## 📝 Лицензия

MIT

---

## 👤 Автор

Разработано для быстрого старта PHP MVC-проектов.
