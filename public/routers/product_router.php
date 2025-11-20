<?php
// public/routers/product_router.php
// ----------------------------------------------
// Router for product management
// Supplier + Admin: CRUD operations
// Customer: view only
// ----------------------------------------------

require_once __DIR__ . '/../../app/Controllers/product_controller.php';

$productController = new product_controller();

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

            // Image upload
            $imagePath = null;
            if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                try {
                    $imagePath = $productController->handleImageUpload($_FILES['image']);
                } catch (RuntimeException $e) {
                    echo "<p style='color:red'>" . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }

            $productController->createProduct(
                $name,
                $description,
                $price,
                $stock,
                $_SESSION['user_id'], // supplier_id
                $imagePath
            );

            echo "<p style='color:green'>Product was added!</p>";
            echo "<p><a href='index.php?action=products'>Back to products</a></p>";
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

            // Keep original image
            $imagePath = $product['image_path'] ?? null;

            // If new file was uploaded -> overwrite
            if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                try {
                    $imagePath = $productController->handleImageUpload($_FILES['image']);
                } catch (RuntimeException $e) {
                    echo "<p style='color:red'>" . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }

            $productController->updateProduct(
                $id,
                $name,
                $description,
                $price,
                $stock,
                $imagePath
            );

            echo "<p style='color:green'>Product was updated!</p>";
            echo "<p><a href='index.php'>Back to dashboard</a></p>";
            echo "<p><a href='index.php?action=my_products'>Back to my products</a></p>";

        } else {
            require __DIR__ . '/../../app/Views/edit_product_view.php';
        }
        break;

    // ------------------------------------------------
    // 3. Delete product (only owner or admin)
    // ------------------------------------------------
    case 'delete_product':
        $id      = (int)($_GET['id'] ?? 0);
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
        echo "<p style='color:green'>Product has been archived successfully.</p>";
        echo "<p><a href='index.php?action=my_products'>Back to my products</a></p>";
        exit;

    // ------------------------------------------------
    // 4. Current supplier's products
    // ------------------------------------------------
    case 'my_products':
        $products = $productController->getBySupplierId($_SESSION['user_id']);
        require __DIR__ . '/../../app/Views/my_products_view.php';
        break;

    // ------------------------------------------------
    // 5. List all products
    // ------------------------------------------------
    default:
        $products = $productController->index();
        require __DIR__ . '/../../app/Views/products_view.php';
        break;
}
