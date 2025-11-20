<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Welcome section -->
<div class="mb-5">
    <h1 class="display-5 mb-2"><i class="fas fa-wave-hand"></i> Vítej, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
    <p class="text-muted">
        <i class="fas fa-envelope"></i> <?= htmlspecialchars($_SESSION['user_email']) ?> |
        <i class="fas fa-user-tag"></i> <?= implode(', ', $_SESSION['roles']) ?>
    </p>
</div>

<?php
// Normalize roles to lowercase for reliable comparison
$roles = array_map('strtolower', $_SESSION['roles'] ?? []);
?>

<!-- Admin section - visible only to users with admin role -->
<?php if (in_array('admin', $roles)): ?>
    <div class="mb-5">
        <h2 class="h4 mb-3"><i class="fas fa-shield-alt"></i> Administrátor</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <a href="index.php?action=users" class="btn btn-primary w-100 py-3">
                    <i class="fas fa-users"></i> Správa uživatelů
                </a>
            </div>
            <div class="col-md-6">
                <a href="index.php?action=orders" class="btn btn-primary w-100 py-3">
                    <i class="fas fa-boxes"></i> Všechny objednávky
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Supplier section - visible only to users with supplier role -->
<?php if (in_array('dodavatel', $roles) || in_array('supplier', $roles)): ?>
    <div class="mb-5">
        <h2 class="h4 mb-3"><i class="fas fa-industry"></i> Dodavatel</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <a href="index.php?action=my_products" class="btn btn-success w-100 py-3">
                    <i class="fas fa-box"></i> Moje produkty
                </a>
            </div>
            <div class="col-md-6">
                <a href="index.php?action=supplier_orders" class="btn btn-success w-100 py-3">
                    <i class="fas fa-receipt"></i> Objednávky mých produktů
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Customer section - visible only to users with customer role -->
<?php if (in_array('zákazník', $roles) || in_array('zakaznik', $roles) || in_array('customer', $roles)): ?>
    <div class="mb-5">
        <h2 class="h4 mb-3"><i class="fas fa-shopping-cart"></i> Zákazník</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <a href="index.php?action=view_cart" class="btn btn-info w-100 py-3">
                    <i class="fas fa-cart-plus"></i> Můj košík
                </a>
            </div>
            <div class="col-md-6">
                <a href="index.php?action=orders" class="btn btn-info w-100 py-3">
                    <i class="fas fa-list"></i> Moje objednávky
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>
