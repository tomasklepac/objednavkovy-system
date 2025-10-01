<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přihlášení</title>
</head>
<body>
<h1>Přihlášení</h1>

<?php if (!empty($error)) : ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="post" action="index.php?action=login">
    <label for="email">Email:</label>
    <input type="email" name="email" required><br><br>

    <label for="password">Heslo:</label>
    <input type="password" name="password" required><br><br>

    <button type="submit">Přihlásit se</button>
</form>

<p>Nemáš účet? <a href="index.php?action=register">Zaregistruj se</a></p>
</body>
</html>
