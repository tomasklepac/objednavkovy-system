<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Správa uživatelů</title>
    <style>
        /* Styl tabulky */
        table {
            border-collapse: collapse; /* odstraní dvojité čáry mezi buňkami */
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }

        /* Barvy pro stav uživatele */
        .active {
            color: green;
            font-weight: bold;
        }
        .inactive {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
<h1>Správa uživatelů</h1>

<!-- Pokud existují uživatelé -->
<?php if (!empty($users)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Role</th>
            <th>Stav</th>
            <th>Akce</th>
        </tr>

        <!-- Smyčka přes všechny uživatele -->
        <?php foreach ($users as $user): ?>
            <tr>
                <!-- ID -->
                <td><?= htmlspecialchars($user['id']) ?></td>

                <!-- Email -->
                <td><?= htmlspecialchars($user['email']) ?></td>

                <!-- Role (řetězec z GROUP_CONCAT) -->
                <td><?= htmlspecialchars($user['roles']) ?></td>

                <!-- Stav účtu -->
                <td class="<?= $user['is_active'] ? 'active' : 'inactive' ?>">
                    <?= $user['is_active'] ? 'Aktivní' : 'Blokován / Čeká' ?>
                </td>

                <!-- Akce -->
                <td>
                    <?php if ($user['is_active']): ?>
                        <!-- Aktivní → můžeme blokovat -->
                        <a href="index.php?action=block_user&id=<?= $user['id'] ?>">Blokovat</a>
                    <?php else: ?>
                        <!-- Neaktivní → můžeme schválit -->
                        <a href="index.php?action=approve_user&id=<?= $user['id'] ?>">Schválit</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <!-- Pokud nejsou žádní uživatelé -->
    <p>Žádní uživatelé k zobrazení.</p>
<?php endif; ?>

<p><a href="index.php">Zpět na hlavní stránku</a></p>
</body>
</html>
