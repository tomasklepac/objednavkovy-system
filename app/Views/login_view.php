<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přihlášení</title>
    <style>
        form {
            width: 300px;
            margin: 20px auto;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: #f9f9f9;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 6px;
            margin-top: 4px;
            box-sizing: border-box;
        }
        button {
            margin-top: 15px;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            background-color: #0077cc;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #005fa3;
        }
        p {
            text-align: center;
        }
    </style>
</head>
<body>

<h1 style="text-align: center;">Přihlášení</h1>

<?php if (!empty($error)) : ?>
    <p style="color: red; text-align: center;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<form method="post" action="index.php?action=login">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Heslo:</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Přihlásit se</button>
</form>

<p>Nemáš účet? <a href="index.php?action=register">Zaregistruj se</a></p>

</body>
</html>
