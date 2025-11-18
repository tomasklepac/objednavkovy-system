<?php

/**
 * Model for products.
 * Contains methods for communicating with the `products` table in the database.
 */
class product_model {
    /** @var PDO */
    private $pdo; // Database connection

    // ================================================================
    // CONSTRUCTOR
    // ================================================================

    /**
     * Creates a model instance and stores the PDO connection.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ================================================================
    // READ
    // ================================================================

    /**
     * Returns all active products (is_active = 1).
     *
     * @return array[] List of products as associative array
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
