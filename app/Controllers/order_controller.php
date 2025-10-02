<?php

/**
 * Controller pro práci s objednávkami.
 * Obsahuje metody pro čtení, změny stavu a potvrzování objednávek,
 * a to jak z pohledu admina, zákazníka, tak i dodavatele.
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

    /**
     * Vrátí všechny objednávky (viditelné pro admina).
     */
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
    // ZÁKAZNÍK
    // ================================================================

    /**
     * Vrátí objednávky konkrétního zákazníka.
     */
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

    /**
     * Vrátí položky konkrétní objednávky.
     */
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
    // ZMĚNY STAVU OBJEDNÁVEK
    // ================================================================

    /**
     * Nastaví objednávce nový stav.
     */
    public function updateStatus(int $orderId, string $status): void {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
    }

    /**
     * Potvrdí objednávku (odečte zboží ze skladu a změní stav na confirmed).
     * Pokud není dostatek skladu, transakce se vrátí zpět.
     */
    public function confirmOrder(int $orderId): void {
        try {
            $this->pdo->beginTransaction();

            // Načtení položek objednávky
            $stmt = $this->pdo->prepare("
                SELECT product_id, quantity 
                FROM order_item 
                WHERE order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $item) {
                // Pokus o odečtení skladu (jen pokud je dostatek kusů)
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
                    throw new Exception("Nedostatek zásob pro produkt ID " . $item['product_id']);
                }
            }

            // Změna stavu objednávky na confirmed
            $stmt = $this->pdo->prepare("
                UPDATE orders 
                SET status = 'confirmed' 
                WHERE id = ?
            ");
            $stmt->execute([$orderId]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e; // vyhodí výjimku → zachytí router a vypíše chybu
        }
    }

    /** Přechod objednávky na stav shipped. */
    public function markShipped(int $orderId): void {
        $this->updateStatus($orderId, 'shipped');
    }

    /** Přechod objednávky na stav delivered. */
    public function markDelivered(int $orderId): void {
        $this->updateStatus($orderId, 'delivered');
    }

    // ================================================================
    // DODAVATEL
    // ================================================================

    /**
     * Vrátí objednávky, které obsahují produkty konkrétního dodavatele.
     */
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

    /**
     * Vrátí položky z objednávky, které patří konkrétnímu dodavateli.
     */
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

    /**
     * Vrátí zákazníka k objednávce (pro zobrazení detailu).
     */
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
