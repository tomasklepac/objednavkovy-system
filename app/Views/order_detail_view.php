<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Detail objednávky</title>
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
        .back-link {
            margin-top: 15px;
            display: inline-block;
        }
    </style>
</head>
<body>

<h1>Detail objednávky #<?= htmlspecialchars($_GET['id'] ?? '') ?></h1>

<?php if (!empty($items)): ?>
    <table>
        <tr>
            <th>Produkt</th>
            <th>Množství</th>
            <th>Cena/ks</th>
            <th>Celkem</th>
        </tr>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= (int)$item['quantity'] ?></td>
                <td><?= number_format($item['unit_price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                <td><?= number_format(($item['unit_price_cents'] * $item['quantity']) / 100, 2, ',', ' ') ?> Kč</td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Tato objednávka neobsahuje žádné položky.</p>
<?php endif; ?>

<p class="back-link"><a href="index.php?action=orders">← Zpět na seznam objednávek</a></p>

</body>
</html>
