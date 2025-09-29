<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přidat produkt</title>
</head>
<body>
<h1>Přidat produkt</h1>

<form method="post">
    <label>Název:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Popis:</label><br>
    <textarea name="description" rows="4" cols="40"></textarea><br><br>

    <label>Cena (Kč):</label><br>
    <input type="number" name="price" step="0.01" required><br><br>

    <button type="submit">Uložit produkt</button>
</form>

<p><a href="index.php">Zpět na produkty</a></p>
</body>
</html>
