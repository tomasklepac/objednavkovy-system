<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        h1, h2 {
            font-family: Arial, sans-serif;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        li {
            margin: 5px 0;
        }
        a {
            text-decoration: none;
            color: #0077cc;
        }
        a:hover {
            text-decoration: underline;
        }
        hr {
            margin: 20px 0;
        }
    </style>
</head>
<body>

<h1>Vítej, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
<p>Email: <?= htmlspecialchars($_SESSION['user_email']) ?></p>
<p>Role: <?= implode(', ', $_SESSION['roles']) ?></p>
<hr>

<?php if (in_array('admin', $_SESSION['roles'])): ?>
    <h2>Administrátor</h2>
    <ul>
        <li><a href="index.php?action=users">Správa uživatelů</a></li>
        <li><a href="index.php?action=products">Všechny produkty</a></li>
        <li><a href="index.php?action=orders">Všechny objednávky</a></li>
    </ul>
<?php endif; ?>

<?php if (in_array('supplier', $_SESSION['roles'])): ?>
    <h2>Dodavatel</h2>
    <ul>
        <li><a href="index.php?action=my_products">Moje produkty</a></li>
        <!-- Později lze doplnit: objednávky jejich produktů -->
    </ul>
<?php endif; ?>

<?php if (in_array('customer', $_SESSION['roles'])): ?>
    <h2>Zákazník</h2>
    <ul>
        <li><a href="index.php?action=products">Prohlížet produkty</a></li>
        <li><a href="index.php?action=view_cart">Můj košík</a></li>
        <li><a href="index.php?action=orders">Moje objednávky</a></li>
    </ul>
<?php endif; ?>

<p><a href="index.php?action=logout">Odhlásit se</a></p>

</body>
</html>
