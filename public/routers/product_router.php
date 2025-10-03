<?php
// public/routers/product_router.php
// ----------------------------------------------
// Router pro správu produktů
// Dodavatel + Admin: CRUD operace
// Zákazník: jen prohlížení
// ----------------------------------------------

require_once __DIR__ . '/../../app/Controllers/product_controller.php';
require_once __DIR__ . '/../../config/db.php';

$productController = new product_controller(Database::getInstance());

// Akce z URL
$action = $_GET['action'] ?? null;

switch ($action) {

    // ------------------------------------------------
    // 1. Přidání produktu (supplier / admin)
    // ------------------------------------------------
    case 'add_product':
        if (!in_array('supplier', $_SESSION['roles'] ?? [])
            && !in_array('admin', $_SESSION['roles'] ?? [])) {
            echo "<p style='color:red'>Nemáš oprávnění přidávat produkty.</p>";
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name        = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price       = (float)($_POST['price'] ?? 0);
            $stock       = (int)($_POST['stock'] ?? 0);

            // Upload obrázku
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

            echo "<p style='color:green'>✅ Produkt byl přidán!</p>";
            echo "<p><a href='index.php?action=products'>← Zpět na produkty</a></p>";
        } else {
            require __DIR__ . '/../../app/Views/add_product_view.php';
        }
        break;

    // ------------------------------------------------
    // 2. Úprava produktu (jen owner nebo admin)
    // ------------------------------------------------
    case 'edit_product':
        $id = (int)($_GET['id'] ?? 0);
        $product = $productController->getById($id);

        if (!$product) {
            http_response_code(404);
            echo "<p style='color:red'>Produkt nenalezen.</p>";
            break;
        }

        $isOwner = ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
        $isAdmin = in_array('admin', $_SESSION['roles'] ?? [], true);

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            echo "<p style='color:red'>Tento produkt nemůžeš upravovat.</p>";
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);

            // Ponecháme původní obrázek
            $imagePath = $product['image_path'] ?? null;

            // Pokud byl nahrán nový soubor → přepíšeme
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

            echo "<p style='color:green'>✅ Produkt byl upraven!</p>";
            echo "<p><a href='index.php'>← Zpět na dashboard</a></p>";
            echo "<p><a href='index.php?action=my_products'>← Zpět na moje produkty</a></p>";

        } else {
            require __DIR__ . '/../../app/Views/edit_product_view.php';
        }
        break;

    // ------------------------------------------------
    // 3. Smazání produktu (jen owner nebo admin)
    // ------------------------------------------------
    case 'delete_product':
        $id      = (int)($_GET['id'] ?? 0);
        $product = $productController->getById($id);

        if (!$product) {
            http_response_code(404);
            echo "<p style='color:red'>Produkt nenalezen.</p>";
            break;
        }

        $isOwner = ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
        $isAdmin = in_array('admin', $_SESSION['roles'] ?? [], true);

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            echo "<p style='color:red'>Tento produkt nemůžeš mazat.</p>";
            break;
        }

        $productController->deleteProduct($id);
        header("Location: index.php?action=my_products");
        exit;

    // ------------------------------------------------
    // 4. Produkty aktuálního dodavatele
    // ------------------------------------------------
    case 'my_products':
        $products = $productController->getBySupplierId($_SESSION['user_id']);
        require __DIR__ . '/../../app/Views/my_products_view.php';
        break;

    // ------------------------------------------------
    // 5. Výpis všech produktů
    // ------------------------------------------------
    default:
        $products = $productController->index();
        require __DIR__ . '/../../app/Views/products_view.php';
        break;
}
