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
 * Контроллер постов.
 * Полный CRUD: index, create, store, show, edit, update, delete.
 *
 * Примеры роутов:
 *   GET  /posts          — список постов
 *   GET  /posts/create   — форма создания
 *   POST /posts          — создать пост
 *   GET  /posts/{id}     — детали поста
 *   GET  /posts/{id}/edit — форма редактирования
 *   POST /posts/{id}/edit — обновить пост
 *   GET  /posts/{id}/delete — удалить пост
 */
class PostController extends Controller
{
    /**
     * Список постов
     */
    public function index()
    {
        $posts = Post::findAll('created_at DESC');

        $this->render('posts/index', [
            'title' => 'Посты',
            'posts' => $posts,
        ]);
    }

    /**
     * Форма создания
     */
    public function create()
    {
        $errors = [];

        // Получаем всех пользователей для выбора автора
        $users = User::findAll('name ASC');

        $this->render('posts/create', [
            'title' => 'Создать пост',
            'users' => $users,
            'errors' => $errors,
        ]);
    }

    /**
     * Создание поста
     */
    public function store()
    {
        // Валидация данных
        $errors = $this->validate($_POST, [
            'title'   => 'required|min:3|max:200',
            'body'    => 'required|min:10',
        ]);

        if (!empty($errors)) {
            $users = User::findAll('name ASC');
            $this->render('posts/create', [
                'title' => 'Создать пост',
                'users' => $users,
                'errors' => $errors,
            ]);
            return;
        }

        // Создаём пост
        $data = [
            'title'    => $this->input('title'),
            'body'     => $this->input('body'),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
        // user_id необязателен
        $userId = $this->input('user_id');
        if ($userId !== null && $userId !== '' && $userId !== false) {
            $data['user_id'] = (int) $userId;
        } else {
            $data['user_id'] = null;
        }

        Post::create($data);

        $this->setFlash('success', 'Пост успешно создан!');
        $this->redirect(Router::url('posts'));
    }

    /**
     * Детали поста
     */
    public function show($id)
    {
        $post = Post::findById((int) $id);

        if (!$post) {
            http_response_code(404);
            echo "404 — Пост не найден";
            exit;
        }

        // Получаем автора (если есть)
        $author = Post::getAuthor($id);
        $author = $author ? $author[0] : null;

        $this->render('posts/show', [
            'title' => htmlspecialchars($post['title']),
            'post' => $post,
            'author' => $author,
        ]);
    }

    /**
     * Форма редактирования
     */
    public function edit($id)
    {
        $post = Post::findById((int) $id);

        if (!$post) {
            http_response_code(404);
            echo "404 — Пост не найден";
            exit;
        }

        $users = User::findAll('name ASC');
        $errors = [];

        $this->render('posts/edit', [
            'title' => 'Редактировать пост',
            'post' => $post,
            'users' => $users,
            'errors' => $errors,
        ]);
    }

    /**
     * Обновление поста
     */
    public function update($id)
    {
        $post = Post::findById((int) $id);

        if (!$post) {
            http_response_code(404);
            echo "404 — Пост не найден";
            exit;
        }

        // Валидация данных
        $errors = $this->validate($_POST, [
            'title'   => 'required|min:3|max:200',
            'body'    => 'required|min:10',
        ]);

        if (!empty($errors)) {
            $users = User::findAll('name ASC');
            $this->render('posts/edit', [
                'title' => 'Редактировать пост',
                'post' => $post,
                'users' => $users,
                'errors' => $errors,
            ]);
            return;
        }

        // Обновляем пост
        $data = [
            'title'   => $this->input('title'),
            'body'    => $this->input('body'),
        ];
        
        // user_id необязателен
        $userId = $this->input('user_id');
        if ($userId !== null && $userId !== '' && $userId !== false) {
            $data['user_id'] = (int) $userId;
        } else {
            $data['user_id'] = null;
        }

        Post::update($id, $data);

        $this->setFlash('success', 'Пост успешно обновлён!');
        $this->redirect(Router::url('posts', ['id' => $id]));
    }

    /**
     * Удаление поста
     */
    public function destroy($id)
    {
        Post::delete((int) $id);

        $this->setFlash('success', 'Пост успешно удалён!');
        $this->redirect(Router::url('posts'));
    }
}
