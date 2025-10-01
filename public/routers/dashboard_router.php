<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../app/Controllers/product_controller.php';
require_once __DIR__ . '/../../app/Controllers/order_controller.php';
require_once __DIR__ . '/../../app/Controllers/user_controller.php';

$pdo = Database::getInstance();

$productController = new product_controller($pdo);
$orderController = new order_controller($pdo);
$userController = new user_controller($pdo);

// Pokud je uživatel přihlášen
if (!empty($_SESSION['user_id'])) {

    echo "<h1>Vítej, " . htmlspecialchars($_SESSION['user_name']) . "!</h1>";
    echo "Jsi přihlášen jako: " . htmlspecialchars($_SESSION['user_email']) . "<br>";

    if (!empty($_SESSION['roles'])) {
        echo "Role: " . implode(", ", $_SESSION['roles']) . "<br><br>";
    }

    echo '<a href="index.php?action=logout">Odhlásit se</a><br><br>';

    // ------------------------
    // ODKAZY DLE ROLE
    // ------------------------

    // Zákazník
    if (in_array('customer', $_SESSION['roles'], true)) {
        echo '<p><a href="index.php?action=view_cart">🛒 Zobrazit košík</a></p>';
        echo '<p><a href="index.php?action=orders">Moje objednávky</a></p>';
    }

    // Dodavatel
    if (in_array('supplier', $_SESSION['roles'], true)) {
        echo '<p><a href="index.php?action=my_products">Moje produkty</a></p>';
        echo '<p><a href="index.php?action=orders">Objednávky mých produktů</a></p>';
    }

    // Admin
    if (in_array('admin', $_SESSION['roles'], true)) {
        echo '<p><a href="index.php?action=users">Správa uživatelů</a></p>';
        echo '<p><a href="index.php?action=orders">Všechny objednávky</a></p>';
    }

    echo "<hr>";

    // Výpis všech produktů – pro každého přihlášeného
    $products = $productController->index();
    require __DIR__ . '/../../app/Views/products_view.php';
    exit;
}

// Pokud není přihlášený → login
require __DIR__ . '/../../app/Views/login_view.php';
exit;
