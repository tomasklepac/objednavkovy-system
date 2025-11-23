<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Config\Database;

/**
 * API Controller
 * 
 * Handles REST API endpoints for products, orders, etc.
 * Returns JSON responses instead of HTML views.
 */
class ApiController {

    /**
     * Get all products (GET /api/products)
     * 
     * Supports filtering by:
     * - ?supplier_id=X - Filter by supplier
     * 
     * @return void (outputs JSON)
     */
    public static function getAllProducts(): void {
        try {
            $products = ProductModel::getAllProducts();
            
            // Apply filters if provided
            if (!empty($_GET['supplier_id'])) {
                $supplierId = (int)$_GET['supplier_id'];
                $products = array_filter($products, function($product) use ($supplierId) {
                    return (int)$product['supplier_id'] === $supplierId;
                });
            }
            
            // Reindex array after filtering
            $products = array_values($products);
            
            self::jsonResponse([
                'success' => true,
                'count' => count($products),
                'products' => $products
            ]);
        } catch (\Exception $e) {
            self::jsonError('Failed to fetch products: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get a single product by ID (GET /api/products/5)
     * 
     * @param int $id Product ID
     * @return void (outputs JSON)
     */
    public static function getProductById(int $id): void {
        try {
            $product = ProductModel::getById($id);
            
            if (!$product) {
                self::jsonError('Product not found', 404);
                return;
            }
            
            self::jsonResponse([
                'success' => true,
                'product' => $product
            ]);
        } catch (\Exception $e) {
            self::jsonError('Failed to fetch product: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get all orders for logged in user (GET /api/orders)
     * 
     * Returns different orders based on user role:
     * - Admin: All orders
     * - Supplier: Orders containing their products
     * - Customer: Their own orders
     * 
     * @return void (outputs JSON)
     */
    public static function getAllOrders(): void {
        try {
            // Check if user is logged in
            if (empty($_SESSION['user_id'])) {
                self::jsonError('Unauthorized: Please log in', 401);
                return;
            }
            
            $userId = (int)$_SESSION['user_id'];
            $roles = $_SESSION['roles'] ?? [];
            
            $orders = [];
            
            // Admin: Get all orders
            if (in_array('admin', $roles)) {
                $orders = OrderModel::getAllOrders();
            }
            // Supplier: Get orders containing their products
            elseif (in_array('supplier', $roles)) {
                $orders = OrderModel::getOrdersBySupplier($userId);
            }
            // Customer: Get their own orders
            else {
                $orders = OrderModel::getOrdersByCustomer($userId);
            }
            
            self::jsonResponse([
                'success' => true,
                'count' => count($orders),
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            self::jsonError('Failed to fetch orders: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get a single order with details (GET /api/orders/5)
     * 
     * @param int $id Order ID
     * @return void (outputs JSON)
     */
    public static function getOrderById(int $id): void {
        try {
            // Check if user is logged in
            if (empty($_SESSION['user_id'])) {
                self::jsonError('Unauthorized: Please log in', 401);
                return;
            }
            
            $orderDetails = OrderModel::getOrderDetails((int)$id);
            
            if (!$orderDetails) {
                self::jsonError('Order not found', 404);
                return;
            }
            
            self::jsonResponse([
                'success' => true,
                'order' => $orderDetails
            ]);
        } catch (\Exception $e) {
            self::jsonError('Failed to fetch order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Send JSON response
     * 
     * @param array $data Data to encode as JSON
     * @param int $statusCode HTTP status code (default 200)
     * @return void
     */
    private static function jsonResponse(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Send JSON error response
     * 
     * @param string $message Error message
     * @param int $statusCode HTTP status code (default 400)
     * @return void
     */
    private static function jsonError(string $message, int $statusCode = 400): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error' => $message
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
