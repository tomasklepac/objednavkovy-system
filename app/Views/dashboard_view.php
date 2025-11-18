<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Welcome heading with user's name -->
<h1 class="h3 mb-3">Vítej, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
<!-- Display user's email address -->
<p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user_email']) ?></p>
<!-- Display all assigned roles for the user -->
<p><strong>Role:</strong> <?= implode(', ', $_SESSION['roles']) ?></p>
<hr class="my-4">

<?php
// Normalize roles to lowercase for reliable comparison
$roles = array_map('strtolower', $_SESSION['roles'] ?? []);
?>

<!-- Admin section - visible only to users with admin role -->
<?php if (in_array('admin', $roles)): ?>
    <h2 class="h5">Administrátor</h2>
    <ul class="list-unstyled mb-3">
        <!-- Link to user management panel -->
        <li><a href="index.php?action=users" class="btn btn-outline-secondary">Správa uživatelů</a></li>
        <!-- Link to view all orders in the system -->
        <li><a href="index.php?action=orders" class="btn btn-outline-secondary">Všechny objednávky</a></li>
    </ul>
<?php endif; ?>

<!-- Supplier section - visible only to users with supplier role -->
<?php if (in_array('dodavatel', $roles) || in_array('supplier', $roles)): ?>
    <h2 class="h5">Dodavatel</h2>
    <ul class="list-unstyled mb-3">
        <!-- Link to manage supplier's products -->
        <li><a href="index.php?action=my_products" class="btn btn-outline-secondary">Moje produkty</a></li>
        <!-- Link to view orders containing supplier's products -->
        <li><a href="index.php?action=supplier_orders" class="btn btn-outline-secondary">Objednávky mých produktů</a></li>
    </ul>
<?php endif; ?>

<!-- Customer section - visible only to users with customer role -->
<?php if (in_array('zákazník', $roles) || in_array('zakaznik', $roles) || in_array('customer', $roles)): ?>
    <h2 class="h5">Zákazník</h2>
    <ul class="list-unstyled mb-3">
        <!-- Link to view shopping cart -->
        <li><a href="index.php?action=view_cart" class="btn btn-outline-secondary">Můj košík</a></li>
        <!-- Link to view customer's orders -->
        <li><a href="index.php?action=orders" class="btn btn-outline-secondary">Moje objednávky</a></li>
    </ul>
<?php endif; ?>

<!-- Logout button -->
<p><a href="index.php?action=logout" class="btn btn-outline-danger btn-sm">Odhlásit se</a></p>
