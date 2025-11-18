<?php
// ============================================================
// Main application router
// ------------------------------------------------------------
// Session start, error settings, DB connection
// Splits logic into smaller routers by action (?action=...)
// ============================================================

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// CSRF token - if doesn't exist, generate
if (empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        // fallback if random_bytes is not available
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}

// Debug settings – display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ------------------------------------------------------------
// Load configuration and controllers
// ------------------------------------------------------------
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/Controllers/user_controller.php';
require_once __DIR__ . '/../app/Controllers/product_controller.php';
require_once __DIR__ . '/../app/Controllers/order_controller.php';

// Database connection
$pdo = Database::getInstance();

// Create controller instances
$userController    = new user_controller($pdo);
$productController = new product_controller($pdo);
$orderController   = new order_controller($pdo);

// ------------------------------------------------------------
// Get action (from URL parameter ?action=...)
// ------------------------------------------------------------
$action = $_GET['action'] ?? null;

// ------------------------------------------------------------
// Routing by action
// ------------------------------------------------------------
switch ($action) {

    // Authentication: login, logout, registration
    case 'register':
    case 'login':
    case 'logout':
        require __DIR__ . '/routers/auth_router.php';
        break;

    // Admin: user management
    case 'users':
    case 'approve_user':
    case 'block_user':
        require __DIR__ . '/routers/admin_router.php';
        break;

    // Products: CRUD and supplier's own products
    case 'add_product':
    case 'edit_product':
    case 'delete_product':
    case 'my_products':
        require __DIR__ . '/routers/product_router.php';
        break;

    // Cart: adding, removing, changing quantities
    case 'add_to_cart':
    case 'view_cart':
    case 'remove_from_cart':
    case 'increase_from_cart':
    case 'decrease_from_cart':
        require __DIR__ . '/routers/cart_router.php';
        break;

    // Orders: creation, listing, status changes
    case 'confirm_order':
    case 'orders':
    case 'update_order':
    case 'order_detail':
    case 'confirm_admin_order':
    case 'supplier_orders':
    case 'supplier_order_detail':
        require __DIR__ . '/routers/order_router.php';
        break;

    // Default overview (dashboard, products)
    default:
        require __DIR__ . '/routers/dashboard_router.php';
        break;
}
