<?php require __DIR__ . '/partials/header.php'; ?>

<h1 class="h3 mb-3">Vítej, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
<p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user_email']) ?></p>
<p><strong>Role:</strong> <?= implode(', ', $_SESSION['roles']) ?></p>
<hr class="my-4">

<?php if (in_array('admin', $_SESSION['roles'])): ?>
    <h2 class="h5">Administrátor</h2>
    <ul class="list-unstyled mb-3">
        <li><a href="index.php?action=users" class="btn btn-outline-secondary">Správa uživatelů</a></li>
        <li><a href="index.php?action=orders" class="btn btn-outline-secondary">Všechny objednávky</a></li>
    </ul>
<?php endif; ?>

<?php if (in_array('supplier', $_SESSION['roles'])): ?>
    <h2 class="h5">Dodavatel</h2>
    <ul class="list-unstyled mb-3">
        <li><a href="index.php?action=my_products" class="btn btn-outline-secondary">Moje produkty</a></li>
        <!-- Později lze doplnit: objednávky jejich produktů -->
    </ul>
<?php endif; ?>

<?php if (in_array('customer', $_SESSION['roles'])): ?>
    <h2 class="h5">Zákazník</h2>
    <ul class="list-unstyled mb-3">
        <li><a href="index.php?action=view_cart" class="btn btn-outline-secondary">Můj košík</a></li>
        <li><a href="index.php?action=orders" class="btn btn-outline-secondary">Moje objednávky</a></li>
    </ul>
<?php endif; ?>

<p><a href="index.php?action=logout" class="btn btn-outline-danger btn-sm">Odhlásit se</a></p>
