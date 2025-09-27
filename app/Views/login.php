<!doctype html>
<html lang="cs">
<meta charset="utf-8">
<title>Přihlášení</title>

<h1>Přihlášení</h1>

<?php if (!empty($error)): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="">
    <label>Email:<br>
        <input type="email" name="email" required>
    </label><br><br>

    <label>Heslo:<br>
        <input type="password" name="password" required>
    </label><br><br>

    <button type="submit" name="action" value="login">Přihlásit</button>
</form>
<?php
