<?php
require_once __DIR__ . '/../Models/Product.php';

class ProductController {
    private $pdo;
    private $productModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->productModel = new Product($pdo);
    }

    // ------------------------------------------------
    // READ: všechny produkty
    // ------------------------------------------------
    public function index() {
        return $this->productModel->getAllProducts();
    }

    // ------------------------------------------------
    // CREATE: nový produkt
    // ------------------------------------------------
    public function createProduct($name, $description, $price, $supplierId, $imagePath = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO products (name, description, price_cents, supplier_id, image_path, created_at, is_active)
            VALUES (?, ?, ?, ?, ?, NOW(), 1)
        ");
        $stmt->execute([
            $name,
            $description,
            (int)round($price * 100), // uložíme cenu v centech
            $supplierId,
            $imagePath
        ]);
    }

    // ------------------------------------------------
    // READ: produkt podle ID
    // ------------------------------------------------
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------
    // DELETE: smaže produkt podle ID
    // ------------------------------------------------
    public function deleteProduct($id) {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ------------------------------------------------
    // UPDATE: upraví produkt
    // ------------------------------------------------
    public function updateProduct($id, $name, $description, $price, $imagePath = null) {
        $stmt = $this->pdo->prepare("
            UPDATE products
            SET name = ?, description = ?, price_cents = ?, image_path = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $name,
            $description,
            (int)round($price * 100),
            $imagePath,
            $id
        ]);
    }
}
