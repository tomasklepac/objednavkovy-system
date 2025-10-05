<?php
// ============================================================
// Hlavní router aplikace
// ------------------------------------------------------------
// Spuštění session, nastavení chyb, připojení k DB
// Rozděluje logiku do menších routerů podle akce (?action=...)
// ============================================================

session_start();

// CSRF token - pokud neexistuje, vygeneruj
if (empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        // fallback pokud random_bytes není dostupné
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}

// Debug nastavení – vypisování chyb
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ------------------------------------------------------------
// Načtení konfigurace a controllerů
// ------------------------------------------------------------
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/Controllers/user_controller.php';
require_once __DIR__ . '/../app/Controllers/product_controller.php';
require_once __DIR__ . '/../app/Controllers/order_controller.php';

// Připojení k databázi
$pdo = Database::getInstance();

// Vytvoření instancí controllerů
$userController    = new user_controller($pdo);
$productController = new product_controller($pdo);
$orderController   = new order_controller($pdo);

// ------------------------------------------------------------
// Zjištění akce (z URL parametru ?action=...)
// ------------------------------------------------------------
$action = $_GET['action'] ?? null;

// ------------------------------------------------------------
// Směrování podle akce
// ------------------------------------------------------------
switch ($action) {

    // 🟩 Autentizace: login, logout, registrace
    case 'register':
    case 'login':
    case 'logout':
        require __DIR__ . '/routers/auth_router.php';
        break;

    // 🟦 Admin: správa uživatelů
    case 'users':
    case 'approve_user':
    case 'block_user':
        require __DIR__ . '/routers/admin_router.php';
        break;

    // 🟨 Produkty: CRUD a vlastní produkty dodavatele
    case 'add_product':
    case 'edit_product':
    case 'delete_product':
    case 'my_products':
        require __DIR__ . '/routers/product_router.php';
        break;

    // 🟧 Košík: přidávání, odebírání, změny množství
    case 'add_to_cart':
    case 'view_cart':
    case 'remove_from_cart':
    case 'increase_from_cart':
    case 'decrease_from_cart':
        require __DIR__ . '/routers/cart_router.php';
        break;

    // 🟥 Objednávky: tvorba, výpis, změna stavu
    case 'confirm_order':
    case 'orders':
    case 'update_order':
    case 'order_detail':
    case 'confirm_admin_order':
    case 'supplier_orders':
    case 'supplier_order_detail':
        require __DIR__ . '/routers/order_router.php';
        break;

    // 🟪 Výchozí přehled (dashboard, produkty)
    default:
        require __DIR__ . '/routers/dashboard_router.php';
        break;
}
