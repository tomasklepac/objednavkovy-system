<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Produkty</title>
</head>
<body>

<?php if (!empty($_SESSION['roles']) && (in_array('supplier', $_SESSION['roles']) || in_array('admin', $_SESSION['roles']))): ?>
    <p><a href="index.php?action=add_product">➕ Přidat produkt</a></p>
<?php endif; ?>

<h1>Seznam produktů</h1>

<?php if (!empty($products)): ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Název</th>
            <th>Popis</th>
            <th>Cena</th>
            <th>Dodavatel (ID)</th>
            <th>Akce</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['description']) ?></td>
                <td><?= number_format($product['price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                <td><?= htmlspecialchars($product['supplier_id']) ?></td>
                <td>
                    <?php
                    $isOwner = isset($_SESSION['user_id']) && ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
                    $isAdmin = !empty($_SESSION['roles']) && in_array('admin', $_SESSION['roles'], true);
                    ?>

                    <?php if ($isOwner || $isAdmin): ?>
                        <a href="index.php?action=edit_product&id=<?= (int)$product['id'] ?>">✏ Upravit</a> |
                        <a href="index.php?action=delete_product&id=<?= (int)$product['id'] ?>"
                           onclick="return confirm('Opravdu smazat tento produkt?');">🗑 Smazat</a>
                    <?php else: ?>
                        <em>Bez oprávnění</em>
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
