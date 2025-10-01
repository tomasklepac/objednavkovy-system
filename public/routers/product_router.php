<?php
$action = $_GET['action'] ?? null;

require_once __DIR__ . '/../../app/Controllers/product_controller.php';

$productController = new productcontroller(Database::getInstance());

switch ($action) {
    case 'add_product':
        if (!in_array('supplier', $_SESSION['roles']) && !in_array('admin', $_SESSION['roles'])) {
            echo "<p style='color:red'>Nemáš oprávnění přidávat produkty.</p>";
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);

            $productController->createProduct($name, $description, $price, $_SESSION['user_id'], null);

            echo "<p style='color:green'>Produkt byl přidán!</p>";
            echo "<p><a href='index.php'>Zpět na produkty</a></p>";
        } else {
            require __DIR__ . '/../app/Views/add_product_view.php';
        }
        break;

    case 'edit_product':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = $productController->getById($id);

        if (!$product) {
            http_response_code(404);
            echo "<p style='color:red'>Produkt nenalezen.</p>";
            break;
        }

        $isOwner = ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
        $isAdmin = in_array('admin', $_SESSION['roles'], true);

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            echo "<p style='color:red'>Tento produkt nemůžeš upravovat.</p>";
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);

            $productController->updateProduct($id, $name, $description, $price, $product['image_path']);

            echo "<p style='color:green'>Produkt byl upraven!</p>";
            echo "<p><a href='index.php'>Zpět na produkty</a></p>";
        } else {
            require __DIR__ . '/../../app/Views/edit_product_view.php';
        }
        break;

    case 'delete_product':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = $productController->getById($id);

        if (!$product) {
            http_response_code(404);
            echo "<p style='color:red'>Produkt nenalezen.</p>";
            break;
        }

        $isOwner = ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
        $isAdmin = in_array('admin', $_SESSION['roles'], true);

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            echo "<p style='color:red'>Tento produkt nemůžeš mazat.</p>";
            break;
        }

        $productController->deleteProduct($id);
        header("Location: index.php");
        exit;

    case 'my_products':
        $products = $productController->getBySupplierId($_SESSION['user_id']);
        require __DIR__ . '/../../app/Views/my_products.php';
        break;

    default:
        // Výpis všech produktů
        $products = $productController->index();
        require __DIR__ . '/../../app/Views/products_view.php';
        break;
}
