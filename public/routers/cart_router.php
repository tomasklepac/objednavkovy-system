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
        // Preferuj POST + CSRF, fallback na GET kvůli starým linkům
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tokenOk = hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '');
            if (!$tokenOk) { http_response_code(400); exit('Neplatný CSRF token'); }
            $id = (int)($_POST['product_id'] ?? 0);
        } else {
            $id = (int)($_GET['id'] ?? 0);
        }

        if ($id <= 0) { header('Location: index.php'); exit; }

        $product = $productController->getById($id);
        if (!$product) { header('Location: index.php'); exit; }

        $available = (int)($product['stock'] ?? 0);
        if ($available <= 0) { header('Location: index.php'); exit; }

        $_SESSION['cart'] ??= [];
        if (isset($_SESSION['cart'][$id])) {
            if ($_SESSION['cart'][$id]['quantity'] < $available) {
                $_SESSION['cart'][$id]['quantity']++;
            } // else plno, můžeš přidat flash zprávu
        } else {
            $_SESSION['cart'][$id] = [
                'name'        => $product['name'],
                'price_cents' => $product['price_cents'],
                'quantity'    => 1,
            ];
        }

        // žádné echo – přesměruj na košík
        header('Location: index.php?action=view_cart');
        exit;

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
