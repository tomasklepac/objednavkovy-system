<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Registrace</title>
</head>
<body>
<h1>Registrace</h1>

<!-- Formulář pro registraci -->
<form method="post">
    <!-- Jméno -->
    <label>Jméno:</label><br>
    <input type="text" name="name" required><br><br>

    <!-- Email -->
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <!-- Heslo -->
    <label>Heslo:</label><br>
    <input type="password" name="password" required><br><br>

    <!-- Heslo znovu pro kontrolu -->
    <label>Heslo znovu:</label><br>
    <input type="password" name="password_confirm" required><br><br>

    <!-- Role -->
    <label>Role:</label><br>
    <select name="role" required>
        <option value="customer">Zákazník</option>
        <option value="supplier">Dodavatel</option>
    </select><br><br>

    <!-- Tlačítko -->
    <button type="submit">Registrovat</button>
</form>

<!-- Chybová hláška (pokud existuje) -->
<?php if (!empty($error)): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- Odkaz zpět -->
<p><a href="index.php">Zpět na přihlášení</a></p>
</body>
</html>
