<!doctype html>
<html lang="cs">
<meta charset="utf-8">
<title>Produkty</title>

<h1>Seznam produktů</h1>

<?php if (!empty($products)): ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Název</th>
            <th>Popis</th>
            <th>Cena</th>
            <th>Dodavatel</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['description']) ?></td>
                <td><?= number_format($product['price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                <td><?= htmlspecialchars($product['supplier_id']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Žádné produkty nejsou dostupné.</p>
<?php endif; ?>
