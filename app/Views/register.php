<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Registrace</title>
</head>
<body>
<h1>Registrace</h1>

<form method="post">
    <label>Jméno:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Heslo:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Heslo znovu:</label><br>
    <input type="password" name="password_confirm" required><br><br>

    <label>Role:</label><br>
    <select name="role" required>
        <option value="customer">Zákazník</option>
        <option value="supplier">Dodavatel</option>
    </select><br><br>

    <button type="submit">Registrovat</button>
</form>

<?php if (!empty($error)): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>


<p><a href="index.php">Zpět na přihlášení</a></p>
</body>
</html>

