<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Upravit produkt</title>
</head>
<body>

<h1>Upravit produkt</h1>

<form method="post">
    <!-- Název produktu -->
    <label for="name">Název:</label><br>
    <input type="text" id="name" name="name"
           value="<?= htmlspecialchars($product['name']) ?>"
           required><br><br>

    <!-- Popis produktu -->
    <label for="description">Popis:</label><br>
    <textarea id="description" name="description" rows="4" cols="40"><?= htmlspecialchars($product['description']) ?></textarea><br><br>

    <!-- Cena produktu v Kč -->
    <label for="price">Cena (Kč):</label><br>
    <input type="number" id="price" name="price" step="0.01"
           value="<?= number_format($product['price_cents'] / 100, 2, '.', '') ?>"
           required><br><br>

    <!-- Počet kusů skladem -->
    <label for="stock">Skladem (ks):</label><br>
    <input type="number" id="stock" name="stock" min="0"
           value="<?= (int)$product['stock'] ?>"
           required><br><br>

    <button type="submit">💾 Uložit změny</button>
</form>

<p><a href="index.php?action=products">← Zpět na produkty</a></p>

</body>
</html>
