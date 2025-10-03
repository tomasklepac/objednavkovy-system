<?php require __DIR__ . '/partials/header.php'; ?>

<h1 class="h3 text-center mb-3">Registrace</h1>

<?php if (!empty($error)) : ?>
    <div class="alert alert-danger text-center">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="post" action="index.php?action=register" class="card p-4 mx-auto" style="max-width: 450px;">
    <div class="mb-3">
        <label for="name" class="form-label">Jméno:</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" id="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Heslo:</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="password_confirm" class="form-label">Potvrzení hesla:</label>
        <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="role" class="form-label">Role:</label>
        <select id="role" name="role" class="form-select" required>
            <option value="customer">Zákazník</option>
            <option value="supplier">Dodavatel</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary w-100">Registrovat</button>
</form>

<p class="text-center mt-3">
    Máš už účet? <a href="index.php?action=login">Přihlas se</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>
