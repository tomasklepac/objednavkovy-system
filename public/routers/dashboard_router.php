<?php
// -------------------------------------------------
// Router: dashboard / homepage
// -------------------------------------------------

require_once __DIR__ . '/../../app/Controllers/product_controller.php';
require_once __DIR__ . '/../../app/Controllers/order_controller.php';
require_once __DIR__ . '/../../app/Controllers/user_controller.php';

$productController = new product_controller();
$orderController   = new order_controller();
$userController    = new user_controller();

// If user is logged in
if (!empty($_SESSION['user_id'])) {
    // Load products (displayed to all logged in users)
    $products = $productController->index();

    // Call dashboard view (contains links by role)
    require __DIR__ . '/../../app/Views/dashboard_view.php';

    // Below dashboard immediately display product list
    require __DIR__ . '/../../app/Views/products_view.php';
    
    // Close HTML document with footer
    require __DIR__ . '/../../app/Views/partials/footer.php';
    exit;
}

// If not logged in → redirect to login
require __DIR__ . '/../../app/Views/login_view.php';
exit;
