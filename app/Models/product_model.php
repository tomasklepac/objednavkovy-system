<?php

/**
 * Model for products.
 * Contains static methods for communicating with the `products` table in the database.
 */
class ProductModel {

    // ================================================================
    // READ
    // ================================================================

    /**
     * Returns all active products (is_active = 1).
     *
     * @return array[] List of products as associative array
     */
    public static function getAllProducts(): array {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT *
            FROM products
            WHERE is_active = 1
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
