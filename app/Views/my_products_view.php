<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Moje produkty</title>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        .out-of-stock {
            color: red;
            font-weight: bold;
        }
        a {
            text-decoration: none;
            color: #0077cc;
        }
        a:hover {
            text-decoration: underline;
        }
        .add-product {
            display: inline-block;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<h1>Moje produkty</h1>

<p class="add-product"><a href="index.php?action=add_product">➕ Přidat nový produkt</a></p>

<?php if (!empty($products)): ?>
    <table>
        <tr>
            <th>Název</th>
            <th>Popis</th>
            <th>Cena</th>
            <th>Skladem</th>
            <th>Akce</th>
        </tr>

        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['description']) ?></td>
                <td><?= number_format($product['price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                <td>
                    <?php if ((int)$product['stock'] > 0): ?>
                        <?= (int)$product['stock'] ?> ks
                    <?php else: ?>
                        <span class="out-of-stock">Vyprodáno</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="index.php?action=edit_product&id=<?= (int)$product['id'] ?>">✏ Upravit</a> |
                    <a href="index.php?action=delete_product&id=<?= (int)$product['id'] ?>"
                       onclick="return confirm('Opravdu smazat tento produkt?');">🗑 Smazat</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Nemáš zatím žádné produkty.</p>
<?php endif; ?>

<p><a href="index.php">🏠 Zpět na dashboard</a></p>

</body>
</html>
