<?php

namespace App\Models;

require_once __DIR__ . '/../../config/db.php';

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
            SELECT p.*, u.name AS supplier_name
            FROM products p
            LEFT JOIN users u ON p.supplier_id = u.id
            WHERE p.is_active = 1
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns a specific product by ID.
     *
     * @param int $id Product ID
     * @return array|null Product data or null if not found
     */
    public static function getById(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Returns all active products from a specific supplier.
     *
     * @param int $supplierId Supplier ID
     * @return array[] List of supplier's products
     */
    public static function getBySupplierId(int $supplierId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * 
            FROM products 
            WHERE is_active = 1 AND supplier_id = ?
        ");
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns ALL products (active and archived) from a specific supplier.
     * Used for supplier management interface.
     *
     * @param int $supplierId Supplier ID
     * @return array[] List of ALL supplier's products
     */
    public static function getAllBySupplierId(int $supplierId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * 
            FROM products 
            WHERE supplier_id = ?
            ORDER BY is_active DESC, name ASC
        ");
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    // CREATE
    // ================================================================

    /**
     * Creates a new product.
     *
     * @param string $name Product name
     * @param string $description Product description
     * @param float $price Price in CZK (will be converted to cents)
     * @param int $stock Quantity in stock
     * @param int $supplierId Supplier ID
     * @param string|null $imagePath Path to product image
     * @return void
     */
    public static function createProduct(
        string $name,
        string $description,
        float $price,
        int $stock,
        int $supplierId,
        ?string $imagePath = null
    ): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO products (name, description, price_cents, stock, supplier_id, image_path, created_at, is_active)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), 1)
        ");

        $stmt->execute([
            $name,
            $description,
            (int)round($price * 100),
            $stock,
            $supplierId,
            $imagePath
        ]);
    }

    // ================================================================
    // UPDATE
    // ================================================================

    /**
     * Updates an existing product.
     *
     * @param int $id Product ID
     * @param string $name Product name
     * @param string $description Product description
     * @param float $price Price in CZK (will be converted to cents)
     * @param int $stock Quantity in stock
     * @param string|null $imagePath Path to product image
     * @return void
     */
    public static function updateProduct(
        int $id,
        string $name,
        string $description,
        float $price,
        int $stock,
        ?string $imagePath = null
    ): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE products
            SET name = ?, description = ?, price_cents = ?, stock = ?, image_path = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $name,
            $description,
            (int)round($price * 100),
            $stock,
            $imagePath,
            $id
        ]);
    }

    // ================================================================
    // DELETE (ARCHIVE)
    // ================================================================

    /**
     * Archives a product by ID (soft delete).
     * Sets is_active to 0 instead of deleting the record.
     * This preserves referential integrity with order_item records.
     *
     * @param int $id Product ID
     * @return void
     */
    public static function archiveProduct(int $id): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);
    }

    /**
     * Deletes a product by ID (kept for compatibility).
     * Calls archiveProduct instead.
     *
     * @param int $id Product ID
     * @return void
     * @deprecated Use archiveProduct() instead
     */
    public static function deleteProduct(int $id): void {
        self::archiveProduct($id);
    }

    /**
     * Reactivates an archived product (soft undelete).
     * Sets is_active to 1 and stock to 0 (user must set stock manually).
     *
     * @param int $id Product ID
     * @return void
     */
    public static function reactivateProduct(int $id): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET is_active = 1, stock = 0 WHERE id = ?");
        $stmt->execute([$id]);
    }
}

