<h1><?= htmlspecialchars($post['title']) ?></h1>

<div class="card">
    <table class="detail-table">
        <tr>
            <th>ID</th>
            <td><?= $post['id'] ?></td>
        </tr>
        <tr>
            <th>Заголовок</th>
            <td><?= htmlspecialchars($post['title']) ?></td>
        </tr>
        <tr>
            <th>Автор</th>
            <td>
                <?php if ($author): ?>
                    <a href="/users/<?= $author['id'] ?>"><?= htmlspecialchars($author['name']) ?></a>
                <?php else: ?>
                    Не указан
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Дата создания</th>
            <td><?= date('d.m.Y H:i', strtotime($post['created_at'])) ?></td>
        </tr>
    </table>
</div>

<div class="card">
    <h3>Текст поста</h3>
    <div class="post-body">
        <?= nl2br(htmlspecialchars($post['body'])) ?>
    </div>
</div>

<div class="actions">
    <a href="/posts/<?= $post['id'] ?>/edit" class="btn">Редактировать</a>
    <a href="/posts/<?= $post['id'] ?>/delete" class="btn btn-danger" onclick="return confirm('Удалить пост?')">Удалить</a>
    <a href="/posts" class="btn">Назад к списку</a>
</div>
