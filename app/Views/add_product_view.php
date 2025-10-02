<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přidat produkt</title>
</head>
<body>

<h1>Přidat produkt</h1>

<form method="post">
    <!-- Název produktu -->
    <label for="name">Název:</label><br>
    <input type="text" id="name" name="name" required><br><br>

    <!-- Popis produktu -->
    <label for="description">Popis:</label><br>
    <textarea id="description" name="description" rows="4" cols="40"></textarea><br><br>

    <!-- Cena produktu v Kč -->
    <label for="price">Cena (Kč):</label><br>
    <input type="number" id="price" name="price" step="0.01" required><br><br>

    <button type="submit">Uložit produkt</button>
</form>

<p><a href="index.php">← Zpět na produkty</a></p>

</body>
</html>
