<?php
/**
 * REST API Entry Point
 * 
 * Routes API requests to appropriate controller methods.
 * Base URL: http://localhost/objednavkovy-system/public/api.php
 * 
 * Examples:
 * - GET /api.php?action=products                 → All products
 * - GET /api.php?action=products&id=5            → Product with ID 5
 * - GET /api.php?action=orders                   → All orders (requires login)
 * - GET /api.php?action=orders&id=10             → Order with ID 10
 */

// Start session for user authentication
session_start();

// Load autoloader
require_once __DIR__ . '/../app/autoload.php';

// Use API controller
use App\Controllers\ApiController;

// Get action from query string
$action = $_GET['action'] ?? null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    // Route to appropriate API method
    switch ($action) {
        case 'products':
            if ($id) {
                // GET /api.php?action=products&id=5
                ApiController::getProductById($id);
            } else {
                // GET /api.php?action=products
                ApiController::getAllProducts();
            }
            break;
            
        case 'orders':
            if ($id) {
                // GET /api.php?action=orders&id=10
                ApiController::getOrderById($id);
            } else {
                // GET /api.php?action=orders
                ApiController::getAllOrders();
            }
            break;
            
        default:
            // No action specified or invalid action
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error' => 'Invalid action. Available actions: products, orders'
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            break;
    }
} catch (\Exception $e) {
    // General error handler
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
