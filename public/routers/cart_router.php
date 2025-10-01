<?php
$action = $_GET['action'] ?? null;
$productController = new product_controller(Database::getInstance());

// pouze přihlášení uživatelé mají košík
if (empty($_SESSION['user_id'])) {
    echo "<p style='color:red'>Musíš být přihlášený pro práci s košíkem.</p>";
    exit;
}

// ZOBRAZIT KOŠÍK
if ($action === 'view_cart') {
    require __DIR__ . '/../../app/Views/cart_view.php';
    exit;
}

// PŘIDAT PRODUKT DO KOŠÍKU
if ($action === 'add_to_cart' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $product = $productController->getById($id);

    if (!$product) {
        echo "<p style='color:red'>Produkt nenalezen.</p>";
        exit;
    }

    // Kontrola, jestli je produkt na skladě
    $available = (int)$product['stock'] ?? 0;
    if ($available <= 0) {
        echo "<p style='color:red'>Tento produkt není skladem.</p>";
        exit;
    }

    // Inicializace košíku
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Pokud už produkt v košíku je → zvýšíme množství, ale jen do výše dostupného skladu
    if (isset($_SESSION['cart'][$id])) {
        if ($_SESSION['cart'][$id]['quantity'] < $available) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            echo "<p style='color:orange'>Překročeno množství skladem!</p>";
        }
    } else {
        $_SESSION['cart'][$id] = [
            'name' => $product['name'],
            'price_cents' => $product['price_cents'],
            'quantity' => 1
        ];
    }

    echo "<p style='color:green'>Produkt byl přidán do košíku!</p>";
    echo "<p><a href='index.php'>Pokračovat v nákupu</a> | <a href='index.php?action=view_cart'>Zobrazit košík</a></p>";
    exit;
}

// ODEBRAT 1 KUS
if ($action === 'decrease_from_cart' && isset($_GET['id'])) {
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

// PŘIDAT 1 KUS
if ($action === 'increase_from_cart' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        $product = $productController->getById($id);
        $available = (int)$product['stock'] ?? 0;

        if ($_SESSION['cart'][$id]['quantity'] < $available) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            echo "<p style='color:orange'>Překročeno množství skladem!</p>";
        }
    }
    header("Location: index.php?action=view_cart");
    exit;
}

// SMAZAT CELÝ PRODUKT
if ($action === 'remove_from_cart' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    unset($_SESSION['cart'][$id]);
    header("Location: index.php?action=view_cart");
    exit;
}
