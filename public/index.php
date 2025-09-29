<?php
session_start();

// === načtení připojení k DB a controllerů ===
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/Controllers/UserController.php';
require_once __DIR__ . '/../app/Controllers/ProductController.php';

$pdo = Database::getInstance();
$userController = new UserController($pdo);
$productController = new ProductController($pdo);

// === routing (co chceme dělat) ===
$action = $_GET['action'] ?? null;

// ----------------------------------------------------
// 1) REGISTRACE
// ----------------------------------------------------
if ($action === 'register') {
    $error = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // načtení dat z formuláře
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $role = $_POST['role'] ?? '';
        $name = trim($_POST['name'] ?? '');

        // volání registrace v controlleru
        $result = $userController->register($email, $password, $passwordConfirm, $role, $name);

        if ($result === 'registered_active') {
            echo "<p style='color:green'>Registrace proběhla úspěšně. Nyní se můžete přihlásit.</p>";
            echo "<p><a href='index.php'>Přihlášení</a></p>";
        } elseif ($result === 'registered_inactive') {
            echo "<p style='color:orange'>Registrace proběhla. Váš účet musí schválit administrátor.</p>";
            echo "<p><a href='index.php'>Zpět na přihlášení</a></p>";
        } else {
            $error = $result;
            require __DIR__ . '/../app/Views/register.php';
        }
    } else {
        // GET → zobrazíme formulář
        require __DIR__ . '/../app/Views/register.php';
    }
    exit;
}

// ----------------------------------------------------
// 2) ADMIN – správa uživatelů
// ----------------------------------------------------
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

// ----------------------------------------------------
// 3) LOGIN FLOW
// ----------------------------------------------------
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

// ----------------------------------------------------
// 4) PŘIHLÁŠENÝ UŽIVATEL
// ----------------------------------------------------
if (!empty($_SESSION['user_id'])) {
    echo "<h1>Ahoj, " . htmlspecialchars($_SESSION['user_name']) . "!</h1>";
    echo "Jsi přihlášen jako: " . htmlspecialchars($_SESSION['user_email']) . "<br>";

    if (!empty($_SESSION['roles'])) {
        echo "Role: " . implode(", ", $_SESSION['roles']) . "<br><br>";
    }

    echo '<a href="logout.php">Odhlásit se</a><br><br>';

    // pokud je admin, zobrazíme odkaz na správu uživatelů
    if (in_array('admin', $_SESSION['roles'], true)) {
        echo '<p><a href="index.php?action=users">Správa uživatelů</a></p>';
    }

    // ----------------------------------------------------
    // 5) ADD PRODUCT (jen supplier nebo admin)
    // ----------------------------------------------------
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
    // ----------------------------------------------------
    // DELETE PRODUCT (jen vlastník nebo admin)
    // ----------------------------------------------------
    if ($action === 'delete_product') {
        // musí být přihlášen a mít roli supplier/admin
        if (!in_array('supplier', $_SESSION['roles']) && !in_array('admin', $_SESSION['roles'])) {
            http_response_code(403);
            echo "<p style='color:red'>Nemáš oprávnění mazat produkty.</p>";
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = $productController->getById($id);

        if (!$product) {
            http_response_code(404);
            echo "<p style='color:red'>Produkt nenalezen.</p>";
            exit;
        }

        // povolíme mazat adminovi nebo vlastníkovi produktu
        $isOwner = ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
        $isAdmin = in_array('admin', $_SESSION['roles'], true);

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            echo "<p style='color:red'>Tento produkt nemůžeš mazat.</p>";
            exit;
        }

        // smažeme
        $productController->deleteProduct($id);

        // jednoduchý návrat
        header("Location: index.php");
        exit;
    }

    // ----------------------------------------------------
    // EDIT PRODUCT (jen vlastník nebo admin)
    // ----------------------------------------------------
    if ($action === 'edit_product' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $product = $productController->getById($id);

        if (!$product) {
            http_response_code(404);
            echo "<p style='color:red'>Produkt nenalezen.</p>";
            exit;
        }

        // povolení: vlastník nebo admin
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
    // 6) VÝPIS PRODUKTŮ
    // ----------------------------------------------------
    $products = $productController->index();
    require __DIR__ . '/../app/Views/products.php';
    exit;
}

// ----------------------------------------------------
// 7) NEPŘIHLÁŠENÝ UŽIVATEL – LOGIN FORM
// ----------------------------------------------------
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
