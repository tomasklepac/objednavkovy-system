<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Správa objednávek</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 6px; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>

<h1>Objednávky</h1>

<?php if (!empty($orders)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Zákazník</th>
            <th>Stav</th>
            <th>Celkem</th>
            <th>Datum</th>
            <?php if (in_array('admin', $_SESSION['roles'], true)): ?>
                <th>Akce</th>
            <?php endif; ?>
        </tr>

        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= htmlspecialchars($order['customer_name'] ?? $_SESSION['user_name']) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
                <td><?= number_format($order['total_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                <td><?= htmlspecialchars($order['created_at']) ?></td>

                <?php if (in_array('admin', $_SESSION['roles'], true)): ?>
                    <td>
                        <a href="index.php?action=update_order&id=<?= $order['id'] ?>&status=confirmed">✅ Potvrdit</a> |
                        <a href="index.php?action=update_order&id=<?= $order['id'] ?>&status=canceled">❌ Zrušit</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Žádné objednávky k zobrazení.</p>
<?php endif; ?>

<p><a href="index.php">← Zpět na hlavní stránku</a></p>
</body>
</html>
