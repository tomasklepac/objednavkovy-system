<?php
// Natáhneme model productmodel, který se stará o komunikaci s tabulkou `products`
require_once __DIR__ . '/../Models/product_model.php';

class product_controller {
    private $pdo;           // PDO objekt – připojení k databázi
    private $productModel;  // Instance modelu productmodel

    // Konstruktor – spustí se při vytvoření třídy
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->productModel = new product_model($pdo);
    }

    // ------------------------------------------------
    // READ: načtení všech produktů
    // ------------------------------------------------
    public function index() {
        // Vrátí pole všech produktů z databáze
        return $this->productModel->getAllProducts();
    }

    // ------------------------------------------------
    // CREATE: vložení nového produktu
    // ------------------------------------------------
    public function createProduct($name, $description, $price, $supplierId, $imagePath = null) {
        // SQL příkaz s placeholders (otazníky) pro bezpečné vkládání dat
        $stmt = $this->pdo->prepare("
            INSERT INTO products (name, description, price_cents, supplier_id, image_path, created_at, is_active)
            VALUES (?, ?, ?, ?, ?, NOW(), 1)
        ");

        // Provedeme SQL dotaz s daty od uživatele
        $stmt->execute([
            $name,
            $description,
            (int)round($price * 100), // cenu uložíme v centech (např. 199.90 Kč → 19990)
            $supplierId,
            $imagePath
        ]);
    }

    // ------------------------------------------------
    // READ: načtení jednoho produktu podle ID
    // ------------------------------------------------
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // vrátí 1 řádek jako asociativní pole
    }

    // ------------------------------------------------
    // DELETE: smazání produktu podle ID
    // ------------------------------------------------
    public function deleteProduct($id) {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ------------------------------------------------
    // UPDATE: úprava existujícího produktu
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
            (int)round($price * 100), // zase ukládáme v centech
            $imagePath,
            $id
        ]);
    }
}
