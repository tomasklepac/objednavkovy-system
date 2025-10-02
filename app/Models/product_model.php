<?php

/**
 * Model pro produkty.
 * Obsahuje metody pro komunikaci s tabulkou `products` v databázi.
 */
class product_model {
    /** @var PDO */
    private $pdo; // Připojení k databázi

    // ================================================================
    // KONSTRUKTOR
    // ================================================================

    /**
     * Vytvoří instanci modelu a uloží PDO připojení.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ================================================================
    // READ
    // ================================================================

    /**
     * Vrátí všechny aktivní produkty (is_active = 1).
     *
     * @return array[] Seznam produktů jako asociativní pole
     */
    public function getAllProducts(): array {
        $stmt = $this->pdo->query("
            SELECT *
            FROM products
            WHERE is_active = 1
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
