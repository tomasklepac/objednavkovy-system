<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přidat produkt</title>
</head>
<body>
<h1>Přidat produkt</h1>

<!-- Formulář pro přidání nového produktu -->
<form method="post">
    <!-- Název produktu -->
    <label>Název:</label><br>
    <input type="text" name="name" required><br><br>

    <!-- Popis produktu -->
    <label>Popis:</label><br>
    <textarea name="description" rows="4" cols="40"></textarea><br><br>

    <!-- Cena produktu v Kč -->
    <label>Cena (Kč):</label><br>
    <!-- step="0.01" umožňuje desetinná čísla (např. 199.99) -->
    <input type="number" name="price" step="0.01" required><br><br>

    <!-- Tlačítko pro odeslání -->
    <button type="submit">Uložit produkt</button>
</form>

<!-- Odkaz zpět na seznam produktů -->
<p><a href="index.php">Zpět na produkty</a></p>
</body>
</html>
