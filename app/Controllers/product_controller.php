<?php

// Controller pro produkty – zajišťuje CRUD operace a napojení na model.
require_once __DIR__ . '/../Models/product_model.php';

class product_controller {
    /** @var PDO */
    private $pdo;

    /** @var product_model */
    private $productModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->productModel = new product_model($pdo);
    }

    // ================================================================
    // READ
    // ================================================================

    public function index(): array {
        return $this->productModel->getAllProducts();
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

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

    public function createProduct(
        string $name,
        string $description,
        float $price,
        int $stock,
        int $supplierId,
        ?string $imagePath = null
    ): void {
        // CSRF ochrana
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
            ) {
                die('Neplatný CSRF token.');
            }
        }

        $stmt = $this->pdo->prepare("
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

    public function updateProduct(
        int $id,
        string $name,
        string $description,
        float $price,
        int $stock,
        ?string $imagePath = null
    ): void {
        // CSRF ochrana
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
            ) {
                die('Neplatný CSRF token.');
            }
        }

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

    public function deleteProduct(int $id): void {
        // CSRF ochrana pro mazání
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
            ) {
                die('Neplatný CSRF token.');
            }
        }

        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ================================================================
    // IMAGE UPLOAD
    // ================================================================
    public function handleImageUpload(array $file): ?string {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Chyba při nahrávání souboru.");
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            throw new RuntimeException("Soubor je příliš velký (max 2 MB).");
        }

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            throw new RuntimeException("Nepodporovaný typ souboru.");
        }

        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = uniqid('prod_', true) . '.' . $allowed[$mime];
        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException("Nepodařilo se uložit soubor.");
        }

        return 'uploads/' . $filename;
    }
}
