<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Objednávky mých produktů</title>
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
        a {
            text-decoration: none;
            color: #0077cc;
        }
        a:hover {
            text-decoration: underline;
        }
        .back-link {
            margin-top: 15px;
            display: inline-block;
        }
    </style>
</head>
<body>

<h1>Objednávky mých produktů</h1>

<?php if (!empty($orders)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Zákazník</th>
            <th>Stav</th>
            <th>Datum</th>
            <th>Detail</th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= (int)$order['id'] ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
                <td><?= htmlspecialchars($order['created_at']) ?></td>
                <td>
                    <a href="index.php?action=supplier_order_detail&id=<?= (int)$order['id'] ?>">
                        🔍 Detail (moje položky)
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Nemáš žádné objednávky obsahující tvoje produkty.</p>
<?php endif; ?>

<p class="back-link"><a href="index.php">← Zpět na hlavní stránku</a></p>

</body>
</html>
