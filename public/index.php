<?php
// Spustí session – umožňuje ukládat data napříč stránkami
session_start();

// === NAČTENÍ KONFIGURACE A CONTROLLERŮ ===
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/Controllers/UserController.php';
require_once __DIR__ . '/../app/Controllers/ProductController.php';

// Připojení k databázi
$pdo = Database::getInstance();

// Vytvoření instancí controllerů
$userController = new UserController($pdo);
$productController = new ProductController($pdo);

// === ROUTING ==
$action = $_GET['action'] ?? null;

// ====================================================
// 1) REGISTRACE
// ====================================================
if ($action === 'register') {
    $error = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Načtení dat z formuláře
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $role = $_POST['role'] ?? '';
        $name = trim($_POST['name'] ?? '');

        // Zavoláme funkci register v UserControlleru
        $result = $userController->register($email, $password, $passwordConfirm, $role, $name);

        // Různé výsledky registrace
        if ($result === 'registered_active') {
            echo "<p style='color:green'>Registrace proběhla úspěšně. Nyní se můžete přihlásit.</p>";
            echo "<p><a href='index.php'>Přihlášení</a></p>";
        } elseif ($result === 'registered_inactive') {
            echo "<p style='color:orange'>Registrace proběhla. Váš účet musí schválit administrátor.</p>";
            echo "<p><a href='index.php'>Zpět na přihlášení</a></p>";
        } else {
            // chyba → znovu zobrazíme formulář
            $error = $result;
            require __DIR__ . '/../app/Views/register.php';
        }
    } else {
        // GET → ukážeme registrační formulář
        require __DIR__ . '/../app/Views/register.php';
    }
    exit; // konec, nic dalšího se nespustí
}

// ====================================================
// 2) ADMIN – SPRÁVA UŽIVATELŮ
// ====================================================
if (!empty($_SESSION['roles']) && in_array('admin', $_SESSION['roles'], true)) {
    if ($action === 'users') {
        $users = $userController->getAllUsers();
        require __DIR__ . '/../app/Views/users.php';
        exit;
    }

    if ($action === 'approve_user' && isset($_GET['id'])) {
        $userController->approveUser((int)$_GET['id']);
        header("Location: index.php?action=users");
        exit;
    }

    if ($action === 'block_user' && isset($_GET['id'])) {
        $userController->blockUser((int)$_GET['id']);
        header("Location: index.php?action=users");
        exit;
    }
}

// ====================================================
// 3) LOGIN FLOW
// ====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === null) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $status = $userController->login($email, $password);

    if ($status === 'ok') {
        header("Location: index.php");
        exit;
    } elseif ($status === 'inactive') {
        echo "<p style='color:red'>Účet ještě nebyl schválen administrátorem.</p>";
    } else {
        echo "<p style='color:red'>Špatný email nebo heslo!</p>";
    }
}

// ====================================================
// 4) PŘIHLÁŠENÝ UŽIVATEL
// ====================================================
if (!empty($_SESSION['user_id'])) {
    echo "<h1>Ahoj, " . htmlspecialchars($_SESSION['user_name']) . "!</h1>";
    echo "Jsi přihlášen jako: " . htmlspecialchars($_SESSION['user_email']) . "<br>";

    if (!empty($_SESSION['roles'])) {
        echo "Role: " . implode(", ", $_SESSION['roles']) . "<br><br>";
    }

    echo '<a href="logout.php">Odhlásit se</a><br><br>';

    // Pokud je admin, ukážeme odkaz na správu uživatelů
    if (in_array('admin', $_SESSION['roles'], true)) {
        echo '<p><a href="index.php?action=users">Správa uživatelů</a></p>';
    }

    // ADD PRODUCT
    if ($action === 'add_product') {
        if (!in_array('supplier', $_SESSION['roles']) && !in_array('admin', $_SESSION['roles'])) {
            echo "<p style='color:red'>Nemáš oprávnění přidávat produkty.</p>";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);

            $productController->createProduct($name, $description, $price, $_SESSION['user_id'], null);

            echo "<p style='color:green'>Produkt byl přidán!</p>";
            echo "<p><a href='index.php'>Zpět na produkty</a></p>";
        } else {
            require __DIR__ . '/../app/Views/add_product.php';
        }
        exit;
    }

    // DELETE PRODUCT
    if ($action === 'delete_product') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = $productController->getById($id);

        if (!$product) {
            http_response_code(404);
            echo "<p style='color:red'>Produkt nenalezen.</p>";
            exit;
        }

        $isOwner = ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
        $isAdmin = in_array('admin', $_SESSION['roles'], true);

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            echo "<p style='color:red'>Tento produkt nemůžeš mazat.</p>";
            exit;
        }

        $productController->deleteProduct($id);
        header("Location: index.php");
        exit;
    }

    // EDIT PRODUCT
    if ($action === 'edit_product' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $product = $productController->getById($id);

        if (!$product) {
            http_response_code(404);
            echo "<p style='color:red'>Produkt nenalezen.</p>";
            exit;
        }

        $isOwner = ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
        $isAdmin = in_array('admin', $_SESSION['roles'], true);

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            echo "<p style='color:red'>Tento produkt nemůžeš upravovat.</p>";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);

            $productController->updateProduct($id, $name, $description, $price, $product['image_path']);

            echo "<p style='color:green'>Produkt byl upraven!</p>";
            echo "<p><a href='index.php'>Zpět na produkty</a></p>";
            exit;
        } else {
            require __DIR__ . '/../app/Views/edit_product.php';
            exit;
        }
    }

    // ----------------------------------------------------
    // ADD TO CART (přidání produktu do košíku)
    // ----------------------------------------------------
    if ($action === 'add_to_cart' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $product = $productController->getById($id);

        if (!$product) {
            echo "<p style='color:red'>Produkt nenalezen.</p>";
            exit;
        }

        // inicializace košíku
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // pokud už produkt v košíku je → zvýšíme množství
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
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

    // Odkaz na košík (pro zákazníky i adminy, prostě vždy přihlášený uživatel)
    echo '<p><a href="index.php?action=view_cart">🛒 Zobrazit košík</a></p>';

    // VIEW CART
    if ($action === 'view_cart') {
        require __DIR__ . '/../app/Views/view_cart.php';
        exit;
    }

    // ODEBRAT 1 KUS
    if ($action === 'decrease_from_cart' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']--;
            if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$id]); // pokud už je 0, odstraníme celý produkt
            }
        }
        header("Location: index.php?action=view_cart");
        exit;
    }

    // ----------------------------------------------------
    // PŘIDAT 1 KUS
    // ----------------------------------------------------
    if ($action === 'increase_from_cart' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
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

    // ----------------------------------------------------
    // CONFIRM ORDER (potvrzení objednávky)
    // ----------------------------------------------------
    if ($action === 'confirm_order') {
        // musí být přihlášený zákazník a mít něco v košíku
        if (empty($_SESSION['user_id']) || empty($_SESSION['roles']) || !in_array('customer', $_SESSION['roles'], true)) {
            echo "<p style='color:red'>Jen zákazníci mohou potvrdit objednávku.</p>";
            exit;
        }
        if (empty($_SESSION['cart'])) {
            echo "<p style='color:red'>Košík je prázdný, nemůžeš vytvořit objednávku.</p>";
            exit;
        }

        // pokud byl odeslán formulář
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $address = trim($_POST['address'] ?? '');
            $note = trim($_POST['note'] ?? '');

            if ($address === '') {
                echo "<p style='color:red'>Musíš zadat adresu!</p>";
                require __DIR__ . '/../app/Views/confirm_order.php';
                exit;
            }

            // === vytvoříme objednávku v DB ===
            $stmt = $pdo->prepare("
                INSERT INTO orders (customer_id, status, delivery_address, note, total_cents, created_at)
                VALUES (?, 'pending', ?, ?, ?, NOW())
            ");

            // spočítáme celkovou cenu košíku
                        $totalCents = 0;
                        foreach ($_SESSION['cart'] as $item) {
                            $totalCents += $item['price_cents'] * $item['quantity'];
                        }

            // vložíme objednávku
                        $stmt->execute([
                                $_SESSION['user_id'],   // customer_id
                                $address,               // delivery_address
                                $note,                  // poznámka
                                $totalCents             // total_cents
                        ]);

                        $orderId = $pdo->lastInsertId();


            // === vložíme položky objednávky ===
                        $itemStmt = $pdo->prepare("
                INSERT INTO order_item (order_id, product_id, quantity, unit_price_cents)
                VALUES (?, ?, ?, ?)
            ");

                        foreach ($_SESSION['cart'] as $productId => $item) {
                            $itemStmt->execute([
                                    $orderId,
                                    $productId,
                                    $item['quantity'],
                                    $item['price_cents']
                            ]);
                        }


            // --- vyčistíme košík ---
            unset($_SESSION['cart']);

            echo "<p style='color:green'>Objednávka byla úspěšně vytvořena!</p>";
            echo "<p><a href='index.php'>Zpět na produkty</a></p>";
            exit;
        } else {
            // GET → zobrazíme formulář
            require __DIR__ . '/../app/Views/confirm_order.php';
            exit;
        }
    }

    // VÝPIS PRODUKTŮ
    $products = $productController->index();
    require __DIR__ . '/../app/Views/products.php';
    exit;
}

// ====================================================
// 5) NEPŘIHLÁŠENÝ UŽIVATEL – LOGIN FORM
// ====================================================
?>
<h1>Přihlášení</h1>
<form method="post">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Heslo:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Přihlásit</button>
</form>

<p>Nemáš účet? <a href="index.php?action=register">Zaregistrovat se</a></p>
