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

// Pokud je uživatel přihlášený
if (!empty($_SESSION['user_id'])) {
    // Načteme produkty (zobrazí se všem přihlášeným)
    $products = $productController->index();

    // Zavoláme view dashboard (obsahuje odkazy podle role)
    require __DIR__ . '/../../app/Views/dashboard_view.php';

    // Pod dashboardem rovnou zobrazíme seznam produktů
    require __DIR__ . '/../../app/Views/products_view.php';
    exit;
}

// Pokud není přihlášený → přesměrujeme na login
require __DIR__ . '/../../app/Views/login_view.php';
exit;
