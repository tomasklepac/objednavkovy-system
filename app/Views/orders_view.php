<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Správa objednávek</title>
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
        .status-finished {
            color: green;
            font-weight: bold;
        }
        .status-canceled {
            color: red;
            font-weight: bold;
        }
        .back-link {
            margin-top: 15px;
            display: inline-block;
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

<h1>Objednávky</h1>

<?php if (!empty($orders)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Stav</th>
            <th>Celkem</th>
            <th>Datum</th>
            <th>Detail</th>
            <?php if (in_array('admin', $_SESSION['roles'], true)): ?>
                <th>Zákazník</th>
                <th>Akce</th>
            <?php endif; ?>
        </tr>

        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
                <td><?= number_format($order['total_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                <td><?= htmlspecialchars($order['created_at']) ?></td>
                <td>
                    <a href="index.php?action=order_detail&id=<?= (int)$order['id'] ?>">🔍 Detail</a>
                </td>

                <?php if (in_array('admin', $_SESSION['roles'], true)): ?>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td>
                        <?php if ($order['status'] === 'pending'): ?>
                            <a href="index.php?action=confirm_admin_order&id=<?= (int)$order['id'] ?>">✅ Potvrdit</a> |
                            <a href="index.php?action=update_order&id=<?= (int)$order['id'] ?>&status=canceled">❌ Zrušit</a>
                        <?php elseif ($order['status'] === 'confirmed'): ?>
                            <a href="index.php?action=update_order&id=<?= (int)$order['id'] ?>&status=shipped">📦 Odeslat</a>
                        <?php elseif ($order['status'] === 'shipped'): ?>
                            <a href="index.php?action=update_order&id=<?= (int)$order['id'] ?>&status=delivered">📬 Doručeno</a>
                        <?php elseif ($order['status'] === 'delivered'): ?>
                            <span class="status-finished">✔ Ukončeno</span>
                        <?php elseif ($order['status'] === 'canceled'): ?>
                            <span class="status-canceled">❌ Zrušeno</span>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Žádné objednávky k zobrazení.</p>
<?php endif; ?>

<p class="back-link"><a href="index.php">← Zpět na hlavní stránku</a></p>

</body>
</html>
