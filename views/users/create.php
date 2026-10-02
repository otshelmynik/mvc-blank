<h1>Создать пользователя</h1>

<form method="POST" action="/users" class="form">
    <div class="form-group">
        <label for="name">Имя</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($controller->input('name') ?? '') ?>" placeholder="Введите имя">
        <?php if (isset($errors['name'])): ?>
            <span class="error"><?= implode(', ', $errors['name']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($controller->input('email') ?? '') ?>" placeholder="example@mail.com">
        <?php if (isset($errors['email'])): ?>
            <span class="error"><?= implode(', ', $errors['email']) ?></span>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Создать</button>
    <a href="/users" class="btn">Отмена</a>
</form>
