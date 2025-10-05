<?php require __DIR__ . '/partials/header.php'; ?>

<h1 class="h3 text-center mb-3">Přihlášení</h1>

<?php if (!empty($error)) : ?>
    <div class="alert alert-danger text-center">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="post" action="index.php?action=login" class="card p-4 mx-auto" style="max-width: 400px;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" id="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Heslo:</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Přihlásit se</button>
</form>

<p class="text-center mt-3">
    Nemáš účet? <a href="index.php?action=register">Zaregistruj se</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>
