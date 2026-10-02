<h1><?= htmlspecialchars($user['name']) ?></h1>

<div class="card">
    <table class="detail-table">
        <tr>
            <th>ID</th>
            <td><?= $user['id'] ?></td>
        </tr>
        <tr>
            <th>Имя</th>
            <td><?= htmlspecialchars($user['name']) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= htmlspecialchars($user['email']) ?></td>
        </tr>
        <tr>
            <th>Дата создания</th>
            <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
        </tr>
    </table>
</div>

<div class="actions">
    <a href="/users/<?= $user['id'] ?>/edit" class="btn">Редактировать</a>
    <a href="/users/<?= $user['id'] ?>/delete" class="btn btn-danger" onclick="return confirm('Удалить пользователя?')">Удалить</a>
    <a href="/users" class="btn">Назад к списку</a>
</div>

<h2>Посты пользователя</h2>

<?php if (empty($posts)): ?>
    <p class="empty">У этого пользователя пока нет постов.</p>
<?php else: ?>
    <ul class="list">
        <?php foreach ($posts as $post): ?>
            <li>
                <strong><?= htmlspecialchars($post['title']) ?></strong>
                <p><?= htmlspecialchars(mb_substr($post['body'], 0, 100)) ?>...</p>
                <a href="/posts/<?= $post['id'] ?>">Подробнее →</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
