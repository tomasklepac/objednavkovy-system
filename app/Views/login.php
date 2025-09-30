<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Přihlášení</title>
</head>
<body>
<h1>Přihlášení</h1>

<!-- Zobrazení chybové hlášky (pokud existuje) -->
<?php if (!empty($error)): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- Formulář pro přihlášení -->
<form method="post" action="">
    <!-- Pole pro email -->
    <label>Email:<br>
        <input type="email" name="email" required>
    </label><br><br>

    <!-- Pole pro heslo -->
    <label>Heslo:<br>
        <input type="password" name="password" required>
    </label><br><br>

    <!-- Tlačítko pro přihlášení -->
    <button type="submit" name="action" value="login">Přihlásit</button>
</form>
</body>
</html>
