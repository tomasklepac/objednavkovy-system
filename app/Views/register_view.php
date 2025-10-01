<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Registrace</title>
</head>
<body>
<h1>Registrace</h1>

<?php if (!empty($error)) : ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="post" action="index.php?action=register">
    <label for="name">Jméno:</label>
    <input type="text" name="name" required><br><br>

    <label for="email">Email:</label>
    <input type="email" name="email" required><br><br>

    <label for="password">Heslo:</label>
    <input type="password" name="password" required><br><br>

    <label for="password_confirm">Potvrzení hesla:</label>
    <input type="password" name="password_confirm" required><br><br>

    <label for="role">Role:</label>
    <select name="role" required>
        <option value="customer">Zákazník</option>
        <option value="supplier">Dodavatel</option>
    </select><br><br>

    <button type="submit">Registrovat</button>
</form>

<p>Máš už účet? <a href="index.php">Přihlas se</a></p>
</body>
</html>
