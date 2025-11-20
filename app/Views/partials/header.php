<!-- HTML document structure with Bootstrap framework -->
<!doctype html>
<html lang="cs">
<head>
    <!-- Character encoding for proper text rendering -->
    <meta charset="utf-8">
    <!-- Viewport settings for responsive design on mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Page title that appears in browser tab -->
    <title><?= htmlspecialchars($title ?? 'Objednávkový systém') ?></title>

    <!-- Bootstrap CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom application styles (minimal overrides) -->
    <link rel="stylesheet" href="css/app.css">
    
    <!-- Hamburger Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const menuClose = document.getElementById('menuClose');
            const sidebarMenu = document.getElementById('sidebarMenu');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            if (!menuToggle) return;
            
            // Open menu
            menuToggle.addEventListener('click', function() {
                sidebarMenu.classList.add('show');
                sidebarOverlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
            
            // Close menu
            const closeMenu = function() {
                sidebarMenu.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = 'auto';
            };
            
            menuClose.addEventListener('click', closeMenu);
            sidebarOverlay.addEventListener('click', closeMenu);
            
            // Close menu when clicking a link
            const sidebarItems = document.querySelectorAll('.sidebar-item');
            sidebarItems.forEach(item => {
                item.addEventListener('click', closeMenu);
            });
        });
    </script>
</head>
<body>
<!-- Navigation bar -->
<nav class="navbar navbar-light bg-white border-bottom border-1">
    <div class="container-fluid">
        <!-- Logo/Brand -->
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-shopping-cart"></i> E-Shop
        </a>
        
        <!-- Right side: user info and hamburger menu -->
        <div class="d-flex align-items-center gap-3">
            <!-- User info and logout -->
            <?php if (!empty($_SESSION['user_id'])): ?>
                <div class="d-none d-md-flex align-items-center gap-2">
                    <span class="text-muted">
                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                    </span>
                    <a href="index.php?action=logout" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-sign-out-alt"></i> Odhlásit
                    </a>
                </div>
            <?php else: ?>
                <a href="index.php?action=login" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-sign-in-alt"></i> Přihlášení
                </a>
            <?php endif; ?>
            
            <!-- Hamburger menu button -->
            <?php if (!empty($_SESSION['user_id'])): ?>
                <button class="btn btn-outline-secondary" id="menuToggle" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Sidebar menu (hidden by default) -->
<?php if (!empty($_SESSION['user_id'])): ?>
<div id="sidebarMenu" class="sidebar-menu">
    <div class="sidebar-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Menu</h5>
        <button class="btn btn-sm btn-close" id="menuClose"></button>
    </div>
    
    <div class="sidebar-body">
        <?php
        // Normalize roles to lowercase
        $roles = array_map('strtolower', $_SESSION['roles'] ?? []);
        ?>
        
        <!-- Admin section -->
        <?php if (in_array('admin', $roles)): ?>
            <div class="sidebar-section">
                <h6 class="sidebar-title"><i class="fas fa-shield-alt"></i> Administrátor</h6>
                <a href="index.php?action=users" class="sidebar-item">Správa uživatelů</a>
                <a href="index.php?action=orders" class="sidebar-item">Všechny objednávky</a>
            </div>
        <?php endif; ?>
        
        <!-- Supplier section -->
        <?php if (in_array('dodavatel', $roles) || in_array('supplier', $roles)): ?>
            <div class="sidebar-section">
                <h6 class="sidebar-title"><i class="fas fa-industry"></i> Dodavatel</h6>
                <a href="index.php?action=my_products" class="sidebar-item">Moje produkty</a>
                <a href="index.php?action=supplier_orders" class="sidebar-item">Objednávky mých produktů</a>
            </div>
        <?php endif; ?>
        
        <!-- Customer section -->
        <?php if (in_array('zákazník', $roles) || in_array('zakaznik', $roles) || in_array('customer', $roles)): ?>
            <div class="sidebar-section">
                <h6 class="sidebar-title"><i class="fas fa-shopping-cart"></i> Zákazník</h6>
                <a href="index.php?action=view_cart" class="sidebar-item">Můj košík</a>
                <a href="index.php?action=orders" class="sidebar-item">Moje objednávky</a>
            </div>
        <?php endif; ?>
        
        <!-- Mobile logout -->
        <div class="sidebar-section d-md-none border-top pt-3">
            <a href="index.php?action=logout" class="sidebar-item text-danger">
                <i class="fas fa-sign-out-alt"></i> Odhlásit se
            </a>
        </div>
    </div>
</div>

<!-- Sidebar overlay (for closing when clicking outside) -->
<div id="sidebarOverlay" class="sidebar-overlay"></div>
<?php endif; ?>

<!-- Main container for page content -->
<main class="container py-4">
