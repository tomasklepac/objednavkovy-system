<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Produkty</title>
    <style>
        table {
            border-collapse: collapse;
            width: 90%;
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
        .add-product {
            display: inline-block;
            margin-bottom: 15px;
        }
        a {
            text-decoration: none;
            color: #0077cc;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Odkaz na přidání produktu (jen pro dodavatele nebo admina) -->
<?php if (!empty($_SESSION['roles']) && (in_array('supplier', $_SESSION['roles']) || in_array('admin', $_SESSION['roles']))): ?>
    <p class="add-product"><a href="index.php?action=add_product">➕ Přidat produkt</a></p>
<?php endif; ?>

<h1>Seznam produktů</h1>

<?php if (!empty($products)): ?>
    <table>
        <tr>
            <th>Název</th>
            <th>Popis</th>
            <th>Cena</th>
            <th>Skladem</th>
            <th>Dodavatel (ID)</th>
            <th>Akce</th>
        </tr>

        <?php foreach ($products as $product): ?>
            <?php
            $isOwner    = isset($_SESSION['user_id']) && ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
            $isAdmin    = !empty($_SESSION['roles']) && in_array('admin', $_SESSION['roles'], true);
            $isCustomer = !empty($_SESSION['roles']) && in_array('customer', $_SESSION['roles'], true);
            ?>
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
                <td><?= htmlspecialchars($product['supplier_id']) ?></td>
                <td>
                    <?php if ($isOwner || $isAdmin): ?>
                        <a href="index.php?action=edit_product&id=<?= (int)$product['id'] ?>">✏ Upravit</a> |
                        <a href="index.php?action=delete_product&id=<?= (int)$product['id'] ?>"
                           onclick="return confirm('Opravdu smazat tento produkt?');">🗑 Smazat</a> |
                    <?php endif; ?>

                    <?php if ($isCustomer): ?>
                        <?php if ((int)$product['stock'] > 0): ?>
                            <a href="index.php?action=add_to_cart&id=<?= (int)$product['id'] ?>">🛒 Přidat do košíku</a>
                        <?php else: ?>
                            <span style="color:grey;">Nelze přidat</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Žádné produkty nejsou dostupné.</p>
<?php endif; ?>

</body>
</html>
