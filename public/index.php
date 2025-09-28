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

        // volání registrace v controlleru
        $result = $userController->register($email, $password, $passwordConfirm, $role);

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
    echo "<h1>Ahoj!</h1>";
    echo "Jsi přihlášen jako: " . htmlspecialchars($_SESSION['user_email']) . "<br>";

    if (!empty($_SESSION['roles'])) {
        echo "Role: " . implode(", ", $_SESSION['roles']) . "<br><br>";
    }

    echo '<a href="logout.php">Odhlásit se</a><br><br>';

    // pokud je admin, zobrazíme odkaz na správu uživatelů
    if (!empty($_SESSION['roles']) && in_array('admin', $_SESSION['roles'], true)) {
        echo '<p><a href="index.php?action=users">Správa uživatelů</a></p>';
    }

    // načtení produktů
    $products = $productController->index();
    require __DIR__ . '/../app/Views/products.php';
    exit;
}

// ----------------------------------------------------
// 5) NEPŘIHLÁŠENÝ UŽIVATEL – LOGIN FORM
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
