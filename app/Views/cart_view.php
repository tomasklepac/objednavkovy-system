<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Tvůj košík</title>
    <style>
        table {
            border-collapse: collapse;
            width: 60%;
        }
        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        .quantity-controls a {
            display: inline-block;
            padding: 2px 6px;
            margin: 0 3px;
            border: 1px solid #333;
            border-radius: 3px;
            text-decoration: none;
            font-weight: bold;
            color: black;
            background-color: #e0e0e0;
        }
        .quantity-controls a:hover {
            background-color: #d0d0d0;
        }
    </style>
</head>
<body>

<h1>Tvůj košík</h1>

<?php if (!empty($_SESSION['cart'])): ?>
    <table>
        <tr>
            <th>Název</th>
            <th>Cena (Kč)</th>
            <th>Množství</th>
            <th>Celkem</th>
            <th>Akce</th>
        </tr>

        <?php
        $total = 0;
        foreach ($_SESSION['cart'] as $id => $item):
            $subtotal = ($item['price_cents'] / 100) * $item['quantity'];
            $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= number_format($item['price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                <td>
                    <div class="quantity-controls">
                        <a href="index.php?action=decrease_from_cart&id=<?= (int)$id ?>">➖</a>
                        <?= (int)$item['quantity'] ?>
                        <a href="index.php?action=increase_from_cart&id=<?= (int)$id ?>">➕</a>
                    </div>
                </td>
                <td><?= number_format($subtotal, 2, ',', ' ') ?> Kč</td>
                <td>
                    <a href="index.php?action=remove_from_cart&id=<?= (int)$id ?>"
                       onclick="return confirm('Opravdu smazat tento produkt z košíku?');">🗑 Smazat</a>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="3"><strong>Celkem</strong></td>
            <td colspan="2"><strong><?= number_format($total, 2, ',', ' ') ?> Kč</strong></td>
        </tr>
    </table>

    <p>
        <a href="index.php?action=confirm_order">✅ Pokračovat k potvrzení objednávky</a>
    </p>
<?php else: ?>
    <p>Tvůj košík je prázdný.</p>
<?php endif; ?>

<p><a href="index.php">← Zpět k produktům</a></p>

</body>
</html>
