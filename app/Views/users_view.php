<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Správa uživatelů</title>
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
        .active {
            color: green;
            font-weight: bold;
        }
        .inactive {
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
        .back-link {
            margin-top: 15px;
            display: inline-block;
        }
    </style>
</head>
<body>

<h1>Správa uživatelů</h1>

<?php if (!empty($users)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Role</th>
            <th>Stav</th>
            <th>Akce</th>
        </tr>

        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['roles']) ?></td>
                <td class="<?= $user['is_active'] ? 'active' : 'inactive' ?>">
                    <?= $user['is_active'] ? 'Aktivní' : 'Blokován / Čeká' ?>
                </td>
                <td>
                    <?php if ($user['is_active']): ?>
                        <a href="index.php?action=block_user&id=<?= (int)$user['id'] ?>">🚫 Blokovat</a>
                    <?php else: ?>
                        <a href="index.php?action=approve_user&id=<?= (int)$user['id'] ?>">✅ Schválit</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Žádní uživatelé k zobrazení.</p>
<?php endif; ?>

<p class="back-link"><a href="index.php">← Zpět na hlavní stránku</a></p>

</body>
</html>
