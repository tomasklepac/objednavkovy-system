<?php

// Controller pro produkty – zajišťuje CRUD operace a napojení na model.
require_once __DIR__ . '/../Models/product_model.php';

class product_controller {
    /** @var PDO */
    private $pdo;

    /** @var product_model */
    private $productModel;

    /**
     * Konstruktor – při vytvoření controlleru uloží PDO a připraví model.
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->productModel = new product_model($pdo);
    }

    // ================================================================
    // READ
    // ================================================================

    /**
     * Vrátí všechny aktivní produkty (používá model).
     */
    public function index(): array {
        return $this->productModel->getAllProducts();
    }

    /**
     * Vrátí produkt podle ID.
     */
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Vrátí všechny produkty, které patří konkrétnímu dodavateli.
     */
    public function getBySupplierId(int $supplierId): array {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM products 
            WHERE is_active = 1 AND supplier_id = ?
        ");
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    // CREATE
    // ================================================================

    /**
     * Vytvoří nový produkt.
     */
    public function createProduct(
        string $name,
        string $description,
        float $price,
        int $stock,
        int $supplierId,
        ?string $imagePath = null
    ): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO products (name, description, price_cents, stock, supplier_id, image_path, created_at, is_active)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), 1)
        ");

        $stmt->execute([
            $name,
            $description,
            (int)round($price * 100), // cena ukládána v centech
            $stock,
            $supplierId,
            $imagePath
        ]);
    }

    // ================================================================
    // UPDATE
    // ================================================================

    /**
     * Aktualizuje produkt podle ID.
     */
    public function updateProduct(
        int $id,
        string $name,
        string $description,
        float $price,
        int $stock,
        ?string $imagePath = null
    ): void {
        $stmt = $this->pdo->prepare("
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
    // DELETE
    // ================================================================

    /**
     * Smaže produkt podle ID.
     */
    public function deleteProduct(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ================================================================
    // IMAGE UPLOAD
    // ================================================================
    /**
     * stara se o upload obrazku.
     */
    public function handleImageUpload(array $file): ?string {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // žádný soubor nebyl vybrán
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Chyba při nahrávání souboru.");
        }

        // Kontrola velikosti (max 2 MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new RuntimeException("Soubor je příliš velký (max 2 MB).");
        }

        // Povolené typy
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            throw new RuntimeException("Nepodporovaný typ souboru.");
        }

        // Cílová složka
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Unikátní název
        $filename = uniqid('prod_', true) . '.' . $allowed[$mime];
        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException("Nepodařilo se uložit soubor.");
        }

        // Vrátí cestu, kterou uložíme do DB
        return 'uploads/' . $filename;
    }
}
