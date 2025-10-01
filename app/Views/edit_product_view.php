<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Upravit produkt</title>
</head>
<body>
<h1>Upravit produkt</h1>

<!-- Formulář pro úpravu produktu -->
<form method="post">
    <!-- Název produktu -->
    <label>Název:</label><br>
    <input type="text" name="name"
           value="<?= htmlspecialchars($product['name']) ?>"
           required><br><br>

    <!-- Popis produktu -->
    <label>Popis:</label><br>
    <textarea name="description" rows="4" cols="40"><?= htmlspecialchars($product['description']) ?></textarea><br><br>

    <!-- Cena produktu v Kč -->
    <label>Cena (Kč):</label><br>
    <input type="number" name="price" step="0.01"
           value="<?= number_format($product['price_cents'] / 100, 2, '.', '') ?>"
           required><br><br>

    <!-- Tlačítko pro odeslání -->
    <button type="submit">Uložit změny</button>
</form>

<!-- Odkaz zpět -->
<p><a href="index.php">Zpět na produkty</a></p>
</body>
</html>
