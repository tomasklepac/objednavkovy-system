<?php
// -------------------------------------------------
// Router: autentizace uživatelů (login / logout)
// -------------------------------------------------

require_once __DIR__ . '/../../app/Controllers/user_controller.php';
require_once __DIR__ . '/../../config/db.php';

$action = $_GET['action'] ?? null;
$userController = new user_controller(Database::getInstance());

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Načteme data z formuláře
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Pokus o přihlášení
            $result = $userController->login($email, $password);

            if ($result === 'ok') {
                // ✅ Úspěšné přihlášení → přesměrování na dashboard
                header("Location: index.php?action=dashboard");
                exit;
            } elseif ($result === 'inactive') {
                $error = "Účet ještě není schválen. Počkejte na aktivaci administrátorem.";
                require __DIR__ . "/../../app/Views/login_view.php";
            } else {
                $error = "Neplatné přihlašovací údaje.";
                require __DIR__ . "/../../app/Views/login_view.php";
            }
        } else {
            // Pokud není POST → zobrazí se formulář
            require __DIR__ . "/../../app/Views/login_view.php";
        }
        break;

    case 'logout':
        // Odhlášení uživatele
        $userController->logout();
        header("Location: index.php?action=login");
        exit;

    default:
        // Defaultně zobrazíme login
        require __DIR__ . "/../../app/Views/login_view.php";
        break;
}
