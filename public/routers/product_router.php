<?php
// public/routers/product_router.php
// ----------------------------------------------
// Router for product management
// Supplier + Admin: CRUD operations
// Customer: view only
// ----------------------------------------------

use App\Controllers\ProductController;
use App\Config\Database;

$productController = new ProductController();

// Action from URL
$action = $_GET['action'] ?? null;

switch ($action) {

    // ------------------------------------------------
    // 1. Add product (supplier / admin)
    // ------------------------------------------------
    case 'add_product':
        if (!in_array('supplier', $_SESSION['roles'] ?? [])
            && !in_array('admin', $_SESSION['roles'] ?? [])) {
            echo "<p style='color:red'>You don't have permission to add products.</p>";
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name        = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price       = (float)($_POST['price'] ?? 0);
            $stock       = (int)($_POST['stock'] ?? 0);

            // Prepare image file
            $imageFile = (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE)
                ? $_FILES['image']
                : null;

            try {
                $productController->createProduct(
                    $name,
                    $description,
                    $price,
                    $stock,
                    $_SESSION['user_id'], // supplier_id
                    $imageFile
                );
            } catch (RuntimeException $e) {
                echo "<p style='color:red'>" . htmlspecialchars($e->getMessage()) . "</p>";
                break;
            }

            $isAdmin = in_array('admin', $_SESSION['roles'] ?? [], true);

            $title = 'Produkt byl úspěšně přidán!';
            $message = "Produkt '$name' je nyní dostupný v katalogu.";
            $details = [
                'Název' => $name,
                'Cena' => number_format($price, 2, ',', ' ') . ' Kč',
                'Skladem' => $stock . ' ks'
            ];
            $actions = [
                'Zpět na produkty' => $isAdmin ? 'index.php?action=all_products' : 'index.php?action=my_products',
                'Dashboard' => 'index.php'
            ];
            require __DIR__ . '/../../app/Views/success_message_view.php';
        } else {
            require __DIR__ . '/../../app/Views/add_product_view.php';
        }
        break;

    // ------------------------------------------------
    // 2. Edit product (only owner or admin)
    // ------------------------------------------------
    case 'edit_product':
        $id = (int)($_GET['id'] ?? 0);
        $product = $productController->getById($id);

        if (!$product) {
            http_response_code(404);
            echo "<p style='color:red'>Product not found.</p>";
            break;
        }

        $isOwner = ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
        $isAdmin = in_array('admin', $_SESSION['roles'] ?? [], true);

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            echo "<p style='color:red'>You cannot edit this product.</p>";
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);

            // Pass $_FILES['image'] directly to controller
            // Controller will handle image upload and fallback to existing image if needed
            $imageFile = (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE)
                ? $_FILES['image']
                : null;

            $productController->updateProduct(
                $id,
                $name,
                $description,
                $price,
                $stock,
                $imageFile
            );

            $title = 'Produkt byl úspěšně aktualizován!';
            $message = "Změny v produktu '$name' byly uloženy.";
            $details = [
                'Název' => $name,
                'Cena' => number_format($price, 2, ',', ' ') . ' Kč',
                'Skladem' => $stock . ' ks'
            ];
            $actions = [
                'Zpět na produkty' => $isAdmin ? 'index.php?action=all_products' : 'index.php?action=my_products',
                'Dashboard' => 'index.php'
            ];
            require __DIR__ . '/../../app/Views/success_message_view.php';

        } else {
            require __DIR__ . '/../../app/Views/edit_product_view.php';
        }
        break;

    // ------------------------------------------------
    // 3. Delete product (only owner or admin)
    // ------------------------------------------------
    case 'delete_product':
        // Handle both GET and POST requests
        $id = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['product_id'] ?? 0);
        } else {
            $id = (int)($_GET['id'] ?? 0);
        }
        
        $product = $productController->getById($id);

        if (!$product) {
            http_response_code(404);
            echo "<p style='color:red'>Product not found.</p>";
            echo "<p><a href='index.php?action=my_products'>Back to my products</a></p>";
            break;
        }

        $isOwner = ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
        $isAdmin = in_array('admin', $_SESSION['roles'] ?? [], true);

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            echo "<p style='color:red'>You cannot delete this product.</p>";
            break;
        }

        // Check if product is already archived
        if (!$product['is_active']) {
            echo "<p style='color:orange'>This product is already archived.</p>";
            echo "<p><a href='index.php?action=my_products'>Back to my products</a></p>";
            break;
        }

        $productController->archiveProduct($id);
        
        $title = 'Produkt byl úspěšně archivován';
        $message = "Produkt '{$product['name']}' je nyní archivován a nebude viditelný v katalogu.";
        $details = [
            'Produkt' => $product['name'],
            'Stav' => 'Archivován'
        ];
        $actions = [
            'Zpět na produkty' => $isAdmin ? 'index.php?action=all_products' : 'index.php?action=my_products',
            'Dashboard' => 'index.php'
        ];
        require __DIR__ . '/../../app/Views/success_message_view.php';
        exit;

    // ------------------------------------------------
    // 4. Current supplier's products (all, including archived)
    // ------------------------------------------------
    case 'my_products':
        $products = $productController->getAllBySupplierId($_SESSION['user_id']);
        require __DIR__ . '/../../app/Views/my_products_view.php';
        break;

    // ------------------------------------------------
    // 4b. Reactivate product (supplier or admin)
    // ------------------------------------------------
    case 'reactivate_product':
        $id      = (int)($_GET['id'] ?? 0);
        $product = $productController->getById($id);

        if (!$product) {
            http_response_code(404);
            echo "<p style='color:red'>Product not found.</p>";
            echo "<p><a href='index.php?action=my_products'>Back to my products</a></p>";
            break;
        }

        $isOwner = ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
        $isAdmin = in_array('admin', $_SESSION['roles'] ?? [], true);

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            echo "<p style='color:red'>You cannot reactivate this product.</p>";
            break;
        }

        // Check if product is already active
        if ($product['is_active']) {
            echo "<p style='color:orange'>This product is already active.</p>";
            echo "<p><a href='index.php?action=my_products'>Back to my products</a></p>";
            break;
        }

        $productController->reactivateProduct($id);
        
        $title = 'Produkt byl úspěšně reaktivován';
        $message = "Produkt '{$product['name']}' je nyní znovu aktivní. Skladem je nyní 0 ks - prosím aktualizujte stav skladem.";
        $details = [
            'Produkt' => $product['name'],
            'Stav' => 'Aktivní',
            'Skladem' => '0 ks'
        ];
        $actions = [
            'Upravit produkt' => 'index.php?action=edit_product&id=' . (int)$id,
            'Zpět na produkty' => $isAdmin ? 'index.php?action=all_products' : 'index.php?action=my_products'
        ];
        require __DIR__ . '/../../app/Views/success_message_view.php';
        exit;

    // ------------------------------------------------
    // 5. List all products (admin only)
    // ------------------------------------------------
    case 'all_products':
        if (!in_array('admin', $_SESSION['roles'] ?? [])) {
            echo "<p style='color:red'>You don't have permission to view all products.</p>";
            break;
        }
        
        $products = $productController->getAllProductsAdmin();
        require __DIR__ . '/../../app/Views/all_products_view.php';
        break;

    // ------------------------------------------------
    // 6. List all products (public/dashboard view)
    // ------------------------------------------------
    default:
        $products = $productController->index();
        require __DIR__ . '/../../app/Views/products_view.php';
        break;
}
