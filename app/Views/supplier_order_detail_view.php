<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Detail objednávky – moje položky</title>
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
        .sum {
            text-align: right;
            font-weight: bold;
        }
        .back-link {
            margin-top: 15px;
            display: inline-block;
        }
    </style>
</head>
<body>

<h1>Objednávka #<?= htmlspecialchars($_GET['id'] ?? '') ?> – moje položky</h1>

<?php if (!empty($customer)): ?>
    <p><strong>Zákazník:</strong> <?= htmlspecialchars($customer['name']) ?> (<?= htmlspecialchars($customer['email']) ?>)</p>
<?php endif; ?>

<?php if (!empty($items)): ?>
    <table>
        <tr>
            <th>Produkt</th>
            <th>Množství</th>
            <th>Cena/ks</th>
            <th>Mezisoučet</th>
        </tr>
        <?php
        $subtotalCents = 0;
        foreach ($items as $it):
            $line = $it['unit_price_cents'] * $it['quantity'];
            $subtotalCents += $line;
            ?>
            <tr>
                <td><?= htmlspecialchars($it['name']) ?></td>
                <td><?= (int)$it['quantity'] ?></td>
                <td><?= number_format($it['unit_price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                <td><?= number_format($line / 100, 2, ',', ' ') ?> Kč</td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" class="sum">Součet mých položek:</td>
            <td class="sum"><?= number_format($subtotalCents / 100, 2, ',', ' ') ?> Kč</td>
        </tr>
    </table>
<?php else: ?>
    <p>Tahle objednávka neobsahuje žádné tvoje položky.</p>
<?php endif; ?>

<p class="back-link"><a href="index.php?action=supplier_orders">← Zpět na objednávky mých produktů</a></p>

</body>
</html>
