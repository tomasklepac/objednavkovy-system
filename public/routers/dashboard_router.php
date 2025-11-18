<?php
// -------------------------------------------------
// Router: dashboard / homepage
// -------------------------------------------------

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../app/Controllers/product_controller.php';
require_once __DIR__ . '/../../app/Controllers/order_controller.php';
require_once __DIR__ . '/../../app/Controllers/user_controller.php';

$pdo = Database::getInstance();

$productController = new product_controller($pdo);
$orderController   = new order_controller($pdo);
$userController    = new user_controller($pdo);

// If user is logged in
if (!empty($_SESSION['user_id'])) {
    // Load products (displayed to all logged in users)
    $products = $productController->index();

    // Call dashboard view (contains links by role)
    require __DIR__ . '/../../app/Views/dashboard_view.php';

    // Below dashboard immediately display product list
    require __DIR__ . '/../../app/Views/products_view.php';
    exit;
}

// If not logged in → redirect to login
require __DIR__ . '/../../app/Views/login_view.php';
exit;
