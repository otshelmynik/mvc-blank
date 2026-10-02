<h1>Посты</h1>

<p class="page-actions"><a href="/posts/create" class="btn btn-primary">+ Добавить пост</a></p>

<?php if (empty($posts)): ?>
    <p class="empty">Постов пока нет.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Заголовок</th>
                <th>Автор</th>
                <th>Дата создания</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?= $post['id'] ?></td>
                    <td><?= htmlspecialchars($post['title']) ?></td>
                    <td>
                        <?php
                        $authorName = Post::getAuthorName($post['id']);
                        echo htmlspecialchars($authorName);
                        ?>
                    </td>
                    <td><?= date('d.m.Y', strtotime($post['created_at'])) ?></td>
                    <td class="actions">
                        <a href="/posts/<?= $post['id'] ?>">Просмотр</a>
                        <a href="/posts/<?= $post['id'] ?>/edit">Редактировать</a>
                        <a href="/posts/<?= $post['id'] ?>/delete" class="delete" onclick="return confirm('Удалить пост?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
