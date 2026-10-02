<h1>Главная страница</h1>

<div class="stats">
    <div class="stat-card">
        <h2>Пользователи</h2>
        <p class="stat-number"><?= $userCount ?></p>
        <a href="/users">Смотреть всех →</a>
    </div>
    <div class="stat-card">
        <h2>Посты</h2>
        <p class="stat-number"><?= $postCount ?></p>
        <a href="/posts">Смотреть все →</a>
    </div>
</div>

<h2>Последние посты</h2>

<?php if (empty($recentPosts)): ?>
    <p class="empty">Постов пока нет.</p>
<?php else: ?>
    <ul class="list">
        <?php foreach ($recentPosts as $post): ?>
            <li>
                <strong><?= htmlspecialchars($post['title']) ?></strong>
                <p><?= htmlspecialchars(mb_substr($post['body'], 0, 100)) ?>...</p>
                <a href="/posts/<?= $post['id'] ?>">Подробнее →</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
