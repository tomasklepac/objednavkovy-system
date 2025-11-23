<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Config\Database;

/**
 * Controller for products – handles CRUD operations for products.
 * Works with ProductModel for database operations.
 */
class ProductController {

    // ================================================================
    // READ
    // ================================================================

    /**
     * Returns all active products.
     *
     * @return array[] List of all active products
     */
    public function index(): array {
        return ProductModel::getAllProducts();
    }

    /**
     * Returns ALL products (active and archived) for admin view.
     *
     * @return array[] List of all products
     */
    public function getAllProductsAdmin(): array {
        return ProductModel::getAllProductsAdmin();
    }

    /**
     * Returns a specific product by ID.
     *
     * @param int $id Product ID
     * @return array|null Product data or null if not found
     */
    public function getById(int $id): ?array {
        return ProductModel::getById($id);
    }

    /**
     * Returns all products from a specific supplier.
     *
     * @param int $supplierId Supplier ID
     * @return array[] List of supplier's products
     */
    public function getBySupplierId(int $supplierId): array {
        return ProductModel::getBySupplierId($supplierId);
    }

    /**
     * Returns ALL products (active and archived) from a specific supplier.
     * Used for supplier management interface.
     *
     * @param int $supplierId Supplier ID
     * @return array[] List of ALL supplier's products
     */
    public function getAllBySupplierId(int $supplierId): array {
        return ProductModel::getAllBySupplierId($supplierId);
    }

    // ================================================================
    // CREATE
    // ================================================================

    /**
     * Creates a new product with image upload handling.
     *
     * @param string $name Product name
     * @param string $description Product description
     * @param float $price Price in CZK
     * @param int $stock Stock quantity
     * @param int $supplierId Supplier ID
     * @param array|null $imageFile Image file from $_FILES
     * @return void
     * @throws RuntimeException If image upload fails
     */
    public function createProduct(
        string $name,
        string $description,
        float $price,
        int $stock,
        int $supplierId,
        ?array $imageFile = null
    ): void {
        $imagePath = null;

        if ($imageFile) {
            $imagePath = $this->handleImageUpload($imageFile);
        }

        ProductModel::createProduct($name, $description, $price, $stock, $supplierId, $imagePath);
    }

    // ================================================================
    // UPDATE
    // ================================================================

    /**
     * Updates an existing product with image upload handling.
     *
     * @param int $id Product ID
     * @param string $name Product name
     * @param string $description Product description
     * @param float $price Price in CZK
     * @param int $stock Stock quantity
     * @param array|null $imageFile Image file from $_FILES
     * @return void
     * @throws RuntimeException If image upload fails
     */
    public function updateProduct(
        int $id,
        string $name,
        string $description,
        float $price,
        int $stock,
        ?array $imageFile = null
    ): void {
        $imagePath = null;

        // Only process image if provided
        if ($imageFile && $imageFile['error'] !== UPLOAD_ERR_NO_FILE) {
            $imagePath = $this->handleImageUpload($imageFile);
        }

        // If no new image, keep existing image path
        if (!$imagePath) {
            $existing = ProductModel::getById($id);
            $imagePath = $existing['image_path'] ?? null;
        }

        ProductModel::updateProduct($id, $name, $description, $price, $stock, $imagePath);
    }

    // ================================================================
    // DELETE (ARCHIVE)
    // ================================================================

    /**
     * Archives a product by ID (soft delete).
     * Sets is_active to 0 instead of deleting the record.
     *
     * @param int $id Product ID
     * @return void
     */
    public function archiveProduct(int $id): void {
        ProductModel::archiveProduct($id);
    }

    /**
     * Deletes a product by ID (kept for compatibility).
     * Calls archiveProduct instead.
     *
     * @param int $id Product ID
     * @return void
     * @deprecated Use archiveProduct() instead
     */
    public function deleteProduct(int $id): void {
        $this->archiveProduct($id);
    }

    /**
     * Reactivates an archived product.
     * Sets is_active to 1 and stock to 0.
     *
     * @param int $id Product ID
     * @return void
     */
    public function reactivateProduct(int $id): void {
        ProductModel::reactivateProduct($id);
    }

    // ================================================================
    // IMAGE UPLOAD
    // ================================================================

    /**
     * Handles image file upload with validation.
     * Validates file size (max 2MB) and MIME type (JPEG, PNG, WebP).
     * Generates unique filename using uniqid().
     *
     * @param array $file File from $_FILES
     * @return string|null Path to uploaded file or null if no file
     * @throws RuntimeException If validation or upload fails
     */
    public function handleImageUpload(array $file): ?string {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Error uploading file.");
        }

        // Validate file size (max 2 MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new RuntimeException("File is too large (max 2 MB).");
        }

        // Validate MIME type
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            throw new RuntimeException("Unsupported file type.");
        }

        // Create upload directory if needed
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename with uniqid()
        $filename = uniqid('prod_', true) . '.' . $allowed[$mime];
        $targetPath = $uploadDir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException("Failed to save file.");
        }

        return 'uploads/' . $filename;
    }
}
