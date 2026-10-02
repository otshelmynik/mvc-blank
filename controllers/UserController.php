<?php

// Явная загрузка моделей
$modelUserFile = dirname(__DIR__) . '/models/User.php';
$modelPostFile = dirname(__DIR__) . '/models/Post.php';
if (file_exists($modelUserFile) && !class_exists('User', false)) {
    require_once $modelUserFile;
}
if (file_exists($modelPostFile) && !class_exists('Post', false)) {
    require_once $modelPostFile;
}

/**
 * Контроллер пользователей.
 * Полный CRUD: index, create, store, show, edit, update, delete.
 *
 * Примеры роутов:
 *   GET  /users          — список пользователей
 *   GET  /users/create   — форма создания
 *   POST /users          — создать пользователя
 *   GET  /users/{id}     — детали пользователя
 *   GET  /users/{id}/edit — форма редактирования
 *   POST /users/{id}/edit — обновить пользователя
 *   GET  /users/{id}/delete — удалить пользователя
 */
class UserController extends Controller
{
    /**
     * Список пользователей
     */
    public function index()
    {
        $users = User::findAll('created_at DESC');

        $this->render('users/index', [
            'title' => 'Пользователи',
            'users' => $users,
        ]);
    }

    /**
     * Форма создания
     */
    public function create()
    {
        $errors = [];

        $this->render('users/create', [
            'title' => 'Создать пользователя',
            'errors' => $errors,
        ]);
    }

    /**
     * Создание пользователя
     */
    public function store()
    {
        // Валидация данных
        $errors = $this->validate($_POST, [
            'name'  => 'required|min:2|max:100',
            'email' => 'required|email',
        ]);

        if (!empty($errors)) {
            $this->render('users/create', [
                'title' => 'Создать пользователя',
                'errors' => $errors,
            ]);
            return;
        }

        // Создаём пользователя
        $userId = User::create([
            'name'     => $this->input('name'),
            'email'    => $this->input('email'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->setFlash('success', 'Пользователь успешно создан! (ID: ' . $userId . ')');
        $this->redirect(Router::url('users'));
    }

    /**
     * Детали пользователя
     */
    public function show($id)
    {
        $user = User::findById((int) $id);

        if (!$user) {
            http_response_code(404);
            echo "404 — Пользователь не найден";
            exit;
        }

        // Получаем посты пользователя
        $posts = User::getPosts($id);

        $this->render('users/show', [
            'title' => htmlspecialchars($user['name']),
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    /**
     * Форма редактирования
     */
    public function edit($id)
    {
        $user = User::findById((int) $id);

        if (!$user) {
            http_response_code(404);
            echo "404 — Пользователь не найден";
            exit;
        }

        $errors = [];

        $this->render('users/edit', [
            'title' => 'Редактировать пользователя',
            'user' => $user,
            'errors' => $errors,
        ]);
    }

    /**
     * Обновление пользователя
     */
    public function update($id)
    {
        $user = User::findById((int) $id);

        if (!$user) {
            http_response_code(404);
            echo "404 — Пользователь не найден";
            exit;
        }

        // Валидация данных
        $errors = $this->validate($_POST, [
            'name'  => 'required|min:2|max:100',
            'email' => 'required|email',
        ]);

        if (!empty($errors)) {
            $this->render('users/edit', [
                'title' => 'Редактировать пользователя',
                'user' => $user,
                'errors' => $errors,
            ]);
            return;
        }

        // Обновляем пользователя
        User::update($id, [
            'name'     => $this->input('name'),
            'email'    => $this->input('email'),
        ]);

        $this->setFlash('success', 'Пользователь успешно обновлён!');
        $this->redirect(Router::url('users', ['id' => $id]));
    }

    /**
     * Удаление пользователя
     */
    public function destroy($id)
    {
        // Проверка наличия постов у пользователя
        $postCount = Post::count('user_id = ?', [$id]);
        
        if ($postCount > 0) {
            $this->setFlash('error', 'Нельзя удалить пользователя с постами!');
            $this->redirect(Router::url('users'));
            return;
        }

        User::deleteWithoutCascade((int) $id);

        $this->setFlash('success', 'Пользователь успешно удалён!');
        $this->redirect(Router::url('users'));
    }
}
