<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Potvrzení objednávky</title>
    <style>
        form {
            width: 400px;
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
        input, textarea {
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
            cursor: pointer;
        }
        .confirm {
            background-color: #4CAF50;
            color: white;
        }
        .cancel {
            background-color: #f44336;
            color: white;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>

<h1>Potvrzení objednávky</h1>

<form method="post">
    <label for="street">Ulice a č.p.:</label>
    <input type="text" id="street" name="street" required>

    <label for="city">Město:</label>
    <input type="text" id="city" name="city" required>

    <label for="zip">PSČ:</label>
    <input type="text" id="zip" name="zip" pattern="\d{5}" title="Zadej 5 číslic" required>

    <label for="note">Poznámka (nepovinné):</label>
    <textarea id="note" name="note" rows="2"></textarea>

    <button type="submit" class="confirm">✅ Potvrdit objednávku</button>
    <a href="index.php?action=view_cart" class="cancel">❌ Zpět do košíku</a>
</form>

</body>
</html>
