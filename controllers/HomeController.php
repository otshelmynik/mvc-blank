<?php

/**
 * Контроллер главной страницы.
 *
 * Пример использования:
 *   Роут: '/'
 *   Метод: index()
 */
class HomeController extends Controller
{
    /**
     * Главная страница
     */
    public function index()
    {
        // Получаем количество пользователей и постов
        $userCount = User::count();
        $postCount = Post::count();

        // Получаем последние 5 постов
        $recentPosts = Post::findAll('created_at DESC', 5);

        // Передаём данные в шаблон
        $this->render('home/index', [
            'title' => 'Главная',
            'userCount' => $userCount,
            'postCount' => $postCount,
            'recentPosts' => $recentPosts,
        ]);
    }
}
