<?php
session_start(); // zapne práci se session (paměť na straně serveru)

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/Controllers/UserController.php';

$controller = new UserController();
$error = "";

// Pokud přišel POST požadavek a tlačítko mělo value="login"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $email = trim($_POST['email'] ?? '');      // vezmeme email z formuláře
    $password = $_POST['password'] ?? '';      // vezmeme heslo z formuláře

    if ($controller->login($email, $password)) {
        // uložíme do session
        $_SESSION['user_id'] = 1; // tady bys mohl dát skutečné ID z DB
        $_SESSION['user_email'] = $email;

        // přesměrování po přihlášení
        header('Location: /objednavkovy-system/public/?logged=1');
        exit;
    } else {
        $error = "Špatný email nebo heslo.";
    }
}

// Pokud je uživatel přihlášený, ukážeme mu uvítací stránku
if (!empty($_SESSION['user_id'])) {
    echo "<h1>Ahoj!</h1>";
    echo "Jsi přihlášen jako: " . htmlspecialchars($_SESSION['user_email']);
    exit;
}

// Pokud není přihlášený, zobrazíme login formulář
require __DIR__ . '/../app/Views/login.php';
