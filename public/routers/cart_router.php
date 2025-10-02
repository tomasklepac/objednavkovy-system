<?php
// -------------------------------------------------
// Router: správa košíku
// -------------------------------------------------

$productController = new product_controller(Database::getInstance());
$action = $_GET['action'] ?? null;

// Jen přihlášení uživatelé mohou pracovat s košíkem
if (empty($_SESSION['user_id'])) {
    echo "<p style='color:red'>Musíš být přihlášený pro práci s košíkem.</p>";
    exit;
}

switch ($action) {
    case 'view_cart':
        // Zobrazení obsahu košíku
        require __DIR__ . '/../../app/Views/cart_view.php';
        break;

    case 'add_to_cart':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $product = $productController->getById($id);

            if (!$product) {
                echo "<p style='color:red'>Produkt nenalezen.</p>";
                exit;
            }

            // Kontrola skladu
            $available = (int)($product['stock'] ?? 0);
            if ($available <= 0) {
                echo "<p style='color:red'>Tento produkt není skladem.</p>";
                exit;
            }

            // Inicializace košíku
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Pokud produkt už v košíku je → zvýšíme množství
            if (isset($_SESSION['cart'][$id])) {
                if ($_SESSION['cart'][$id]['quantity'] < $available) {
                    $_SESSION['cart'][$id]['quantity']++;
                } else {
                    echo "<p style='color:orange'>Překročeno množství skladem!</p>";
                }
            } else {
                // Nový produkt do košíku
                $_SESSION['cart'][$id] = [
                    'name'        => $product['name'],
                    'price_cents' => $product['price_cents'],
                    'quantity'    => 1
                ];
            }

            echo "<p style='color:green'>Produkt byl přidán do košíku!</p>";
            echo "<p><a href='index.php'>Pokračovat v nákupu</a> | <a href='index.php?action=view_cart'>Zobrazit košík</a></p>";
        }
        break;

    case 'decrease_from_cart':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity']--;
                if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$id]);
                }
            }
            header("Location: index.php?action=view_cart");
            exit;
        }
        break;

    case 'increase_from_cart':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if (isset($_SESSION['cart'][$id])) {
                $product   = $productController->getById($id);
                $available = (int)($product['stock'] ?? 0);

                if ($_SESSION['cart'][$id]['quantity'] < $available) {
                    $_SESSION['cart'][$id]['quantity']++;
                } else {
                    echo "<p style='color:orange'>Překročeno množství skladem!</p>";
                }
            }
            header("Location: index.php?action=view_cart");
            exit;
        }
        break;

    case 'remove_from_cart':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            unset($_SESSION['cart'][$id]);
            header("Location: index.php?action=view_cart");
            exit;
        }
        break;

    default:
        echo "<p style='color:red'>Neznámá akce s košíkem.</p>";
        break;
}
