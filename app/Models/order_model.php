<?php

namespace App\Models;

require_once __DIR__ . '/../../config/db.php';

/**
 * Model for orders.
 * Contains static methods for communicating with the `orders` table and related operations.
 */
class OrderModel {

    // ================================================================
    // ADMIN - ALL ORDERS
    // ================================================================

    /**
     * Returns all orders with customer information.
     *
     * @return array[] List of orders with customer names
     */
    public static function getAllOrders(): array {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT o.id, o.customer_id, u.name AS customer_name, 
                   o.status, o.total_cents, o.created_at
            FROM orders o
            JOIN users u ON o.customer_id = u.id
            ORDER BY o.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    // CUSTOMER - ORDERS BY CUSTOMER
    // ================================================================

    /**
     * Returns all orders for a specific customer.
     *
     * @param int $customerId Customer ID
     * @return array[] List of customer's orders
     */
    public static function getOrdersByCustomer(int $customerId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT id, status, total_cents, created_at
            FROM orders
            WHERE customer_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all items in a specific order.
     *
     * @param int $orderId Order ID
     * @return array[] List of order items with product names
     */
    public static function getOrderItems(int $orderId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT oi.product_id, oi.quantity, oi.unit_price_cents, p.name
            FROM order_item oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    // ORDER STATUS MANAGEMENT
    // ================================================================

    /**
     * Updates the status of an order.
     *
     * @param int $orderId Order ID
     * @param string $status New status (pending, confirmed, shipped, delivered, cancelled)
     * @return void
     */
    public static function updateStatus(int $orderId, string $status): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
    }

    /**
     * Confirms an order and deducts items from stock (transaction-based).
     *
     * @param int $orderId Order ID
     * @return void
     * @throws Exception If stock is insufficient or transaction fails
     */
    public static function confirmOrder(int $orderId): void {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // Get all order items
            $stmt = $db->prepare("
                SELECT product_id, quantity 
                FROM order_item 
                WHERE order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Deduct stock for each item
            foreach ($items as $item) {
                $stmt = $db->prepare("
                    UPDATE products
                    SET stock = stock - ?
                    WHERE id = ? AND stock >= ?
                ");
                $stmt->execute([
                    $item['quantity'],
                    $item['product_id'],
                    $item['quantity']
                ]);

                if ($stmt->rowCount() === 0) {
                    throw new Exception("Insufficient stock for product ID " . $item['product_id']);
                }
            }

            // Update order status to confirmed
            $stmt = $db->prepare("
                UPDATE orders 
                SET status = 'confirmed' 
                WHERE id = ?
            ");
            $stmt->execute([$orderId]);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Admin confirms an order that was already created (stock already deducted).
     * Simply updates the status to confirmed without deducting stock again.
     *
     * @param int $orderId Order ID
     * @return void
     */
    public static function confirmPendingOrder(int $orderId): void {
        self::updateStatus($orderId, 'confirmed');
    }

    /**
     * Marks an order as shipped.
     *
     * @param int $orderId Order ID
     * @return void
     */
    public static function markShipped(int $orderId): void {
        self::updateStatus($orderId, 'shipped');
    }

    /**
     * Marks an order as delivered.
     *
     * @param int $orderId Order ID
     * @return void
     */
    public static function markDelivered(int $orderId): void {
        self::updateStatus($orderId, 'delivered');
    }

    /**
     * Cancels an order and refunds stock.
     * Adds stock back for all items in the order.
     *
     * @param int $orderId Order ID
     * @return void
     * @throws Exception If transaction fails
     */
    public static function cancelOrder(int $orderId): void {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // Get all order items
            $stmt = $db->prepare("
                SELECT product_id, quantity 
                FROM order_item 
                WHERE order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return stock for each item
            foreach ($items as $item) {
                $stmt = $db->prepare("
                    UPDATE products
                    SET stock = stock + ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $item['quantity'],
                    $item['product_id']
                ]);
            }

            // Update order status to canceled
            $stmt = $db->prepare("
                UPDATE orders 
                SET status = 'canceled' 
                WHERE id = ?
            ");
            $stmt->execute([$orderId]);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    // ================================================================
    // SUPPLIER - SUPPLIER'S ORDERS
    // ================================================================

    /**
     * Returns all orders containing products from a specific supplier.
     *
     * @param int $supplierId Supplier ID
     * @return array[] List of orders containing supplier's products
     */
    public static function getOrdersBySupplier(int $supplierId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT DISTINCT o.id, o.customer_id, u.name AS customer_name,
                   o.status, o.total_cents, o.created_at
            FROM orders o
            JOIN order_item oi ON oi.order_id = o.id
            JOIN products p   ON p.id = oi.product_id
            JOIN users u      ON u.id = o.customer_id
            WHERE p.supplier_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns items in an order that belong to a specific supplier.
     *
     * @param int $orderId Order ID
     * @param int $supplierId Supplier ID
     * @return array[] List of order items from the supplier
     */
    public static function getSupplierOrderItems(int $orderId, int $supplierId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT oi.product_id, p.name, oi.quantity, oi.unit_price_cents
            FROM order_item oi
            JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = ? AND p.supplier_id = ?
            ORDER BY p.name
        ");
        $stmt->execute([$orderId, $supplierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns customer information for an order.
     *
     * @param int $orderId Order ID
     * @return array|null Customer data or null if not found
     */
    public static function getOrderCustomer(int $orderId): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT u.id, u.name, u.email
            FROM orders o
            JOIN users u ON u.id = o.customer_id
            WHERE o.id = ?
            LIMIT 1
        ");
        $stmt->execute([$orderId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Returns complete order information including customer and delivery details.
     *
     * @param int $orderId Order ID
     * @return array|null Order data with customer info or null if not found
     */
    public static function getOrderWithCustomer(int $orderId): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT o.*, u.name as customer_name, u.email as customer_email
            FROM orders o
            JOIN users u ON u.id = o.customer_id
            WHERE o.id = ?
            LIMIT 1
        ");
        $stmt->execute([$orderId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
