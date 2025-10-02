<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Registrace</title>
    <style>
        form {
            width: 320px;
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
        input, select {
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

<h1 style="text-align: center;">Registrace</h1>

<?php if (!empty($error)) : ?>
    <p style="color: red; text-align: center;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<form method="post" action="index.php?action=register">
    <label for="name">Jméno:</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Heslo:</label>
    <input type="password" id="password" name="password" required>

    <label for="password_confirm">Potvrzení hesla:</label>
    <input type="password" id="password_confirm" name="password_confirm" required>

    <label for="role">Role:</label>
    <select id="role" name="role" required>
        <option value="customer">Zákazník</option>
        <option value="supplier">Dodavatel</option>
    </select>

    <button type="submit">Registrovat</button>
</form>

<p>Máš už účet? <a href="index.php">Přihlas se</a></p>

</body>
</html>
