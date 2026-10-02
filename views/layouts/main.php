<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'MVC Application') ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="/" class="logo"><?= htmlspecialchars(Config::get('app.name', 'MVC')) ?></a>
            <ul class="nav">
                <li><a href="/">Главная</a></li>
                <li><a href="/users">Пользователи</a></li>
                <li><a href="/posts">Посты</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <!-- Flash-сообщения -->
        <?php
        $success = $this->getFlash('success');
        if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Содержимое страницы -->
        <?= $content ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(Config::get('app.name', 'MVC Application')) ?></p>
        </div>
    </footer>

    <script src="/js/app.js"></script>
</body>
</html>
