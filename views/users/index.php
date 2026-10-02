<h1>Пользователи</h1>

<p class="page-actions"><a href="/users/create" class="btn btn-primary">+ Добавить пользователя</a></p>

<?php if (empty($users)): ?>
    <p class="empty">Пользователей пока нет.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Email</th>
                <th>Дата создания</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                    <td class="actions">
                        <a href="/users/<?= $user['id'] ?>">Просмотр</a>
                        <a href="/users/<?= $user['id'] ?>/edit">Редактировать</a>
                        <a href="/users/<?= $user['id'] ?>/delete" class="delete" onclick="return confirm('Удалить пользователя?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
