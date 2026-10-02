<h1>Создать пост</h1>

<form method="POST" action="/posts" class="form">
    <div class="form-group">
        <label for="title">Заголовок</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($controller->input('title') ?? '') ?>" placeholder="Введите заголовок">
        <?php if (isset($errors['title'])): ?>
            <span class="error"><?= implode(', ', $errors['title']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="body">Текст</label>
        <textarea id="body" name="body" rows="8" placeholder="Введите текст поста"><?= htmlspecialchars($controller->input('body') ?? '') ?></textarea>
        <?php if (isset($errors['body'])): ?>
            <span class="error"><?= implode(', ', $errors['body']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="user_id">Автор</label>
        <select id="user_id" name="user_id">
            <option value="">— Не указан —</option>
            <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>" <?= ($controller->input('user_id') ?? '') == $u['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['user_id'])): ?>
            <span class="error"><?= implode(', ', $errors['user_id']) ?></span>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Создать</button>
    <a href="/posts" class="btn">Отмена</a>
</form>
