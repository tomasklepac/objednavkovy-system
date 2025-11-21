<?php
// -------------------------------------------------
// Router: dashboard / homepage
// -------------------------------------------------

use App\Controllers\ProductController;
use App\Controllers\OrderController;
use App\Controllers\UserController;
use App\Models\UserModel;

$productController = new ProductController();
$orderController   = new OrderController();
$userController    = new UserController();

// If user is logged in
if (!empty($_SESSION['user_id'])) {
    // If user is SuperAdmin, redirect to SuperAdmin panel
    if (UserModel::isSuperAdmin($_SESSION['user_id'])) {
        header("Location: index.php?action=super_admin");
        exit;
    }

    // Load products (displayed to all logged in users)
    $products = $productController->index();
    
    // Hide action buttons on dashboard (show only "Add to cart" for customers)
    $hideActions = true;

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
