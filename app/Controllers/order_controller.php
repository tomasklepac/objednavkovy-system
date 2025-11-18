<?php

/**
 * Controller for working with orders.
 * Contains methods for reading, changing status and confirming orders,
 * from the perspective of admin, customer, and supplier.
 */
class order_controller {
    /** @var PDO */
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ================================================================
    // ADMIN
    // ================================================================

    public function getAllOrders(): array {
        $stmt = $this->pdo->query("
            SELECT o.id, o.customer_id, u.name AS customer_name, 
                   o.status, o.total_cents, o.created_at
            FROM orders o
            JOIN users u ON o.customer_id = u.id
            ORDER BY o.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    // CUSTOMER
    // ================================================================

    public function getOrdersByCustomer(int $customerId): array {
        $stmt = $this->pdo->prepare("
            SELECT id, status, total_cents, created_at
            FROM orders
            WHERE customer_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderItems(int $orderId): array {
        $stmt = $this->pdo->prepare("
            SELECT oi.product_id, oi.quantity, oi.unit_price_cents, p.name
            FROM order_item oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    // ORDER STATUS CHANGES
    // ================================================================

    /** Sets a new status for the order. */
    public function updateStatus(int $orderId, string $status): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
                die('Invalid CSRF token.');
            }
        }

        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
    }

    /** Confirms the order (deducts items from stock and changes status to confirmed). */
    public function confirmOrder(int $orderId): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
                die('Invalid CSRF token.');
            }
        }

        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("
                SELECT product_id, quantity 
                FROM order_item 
                WHERE order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $item) {
                $stmt = $this->pdo->prepare("
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

            $stmt = $this->pdo->prepare("
                UPDATE orders 
                SET status = 'confirmed' 
                WHERE id = ?
            ");
            $stmt->execute([$orderId]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /** Transitions order to shipped status. */
    public function markShipped(int $orderId): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
                die('Invalid CSRF token.');
            }
        }

        $this->updateStatus($orderId, 'shipped');
    }

    /** Transitions order to delivered status. */
    public function markDelivered(int $orderId): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
                die('Invalid CSRF token.');
            }
        }

        $this->updateStatus($orderId, 'delivered');
    }

    // ================================================================
    // SUPPLIER
    // ================================================================

    public function getOrdersBySupplier(int $supplierId): array {
        $stmt = $this->pdo->prepare("
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

    public function getSupplierOrderItems(int $orderId, int $supplierId): array {
        $stmt = $this->pdo->prepare("
            SELECT oi.product_id, p.name, oi.quantity, oi.unit_price_cents
            FROM order_item oi
            JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = ? AND p.supplier_id = ?
            ORDER BY p.name
        ");
        $stmt->execute([$orderId, $supplierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderCustomer(int $orderId): ?array {
        $stmt = $this->pdo->prepare("
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
}
