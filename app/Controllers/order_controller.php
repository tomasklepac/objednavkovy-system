<?php
class order_controller {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Všechny objednávky (pro admina)
    public function getAllOrders() {
        $stmt = $this->pdo->query("
            SELECT o.id, o.customer_id, u.name AS customer_name, o.status, o.total_cents, o.created_at
            FROM orders o
            JOIN users u ON o.customer_id = u.id
            ORDER BY o.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Objednávky konkrétního zákazníka
    public function getOrdersByCustomer($customerId) {
        $stmt = $this->pdo->prepare("
            SELECT id, status, total_cents, created_at
            FROM orders
            WHERE customer_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Detaily jedné objednávky (položky)
    public function getOrderItems($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT oi.quantity, oi.unit_price_cents, p.name
            FROM order_item oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Změna stavu objednávky
    public function updateStatus($orderId, $status) {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
    }
}
