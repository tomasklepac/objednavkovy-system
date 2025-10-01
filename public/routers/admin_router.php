<?php
// Tento router zpracovává akce správy uživatelů pro admina

require_once __DIR__ . '/../../app/Controllers/user_controller.php';

$pdo = Database::getInstance();
$userController = new User_Controller($pdo);

// Získání požadované akce
$action = $_GET['action'] ?? null;

// Zajištění, že uživatel je přihlášen a je admin
if (!isset($_SESSION['user_id']) || !in_array('admin', $_SESSION['roles'])) {
    echo "Přístup zamítnut.";
    exit;
}

switch ($action) {
    case 'users':
        // Zobrazit všechny uživatele
        $users = $userController->getAllUsers();
        require __DIR__ . '/../../app/Views/users_view.php';
        break;

    case 'approve_user':
        // Schválení dodavatele (např. approve_user?id=5)
        if (isset($_GET['id'])) {
            $userController->approveUser($_GET['id']);
        }
        header("Location: index.php?action=users");
        exit;

    case 'block_user':
        // Zablokování uživatele (např. block_user?id=3)
        if (isset($_GET['id'])) {
            $userController->blockUser($_GET['id']);
        }
        header("Location: index.php?action=users");
        exit;

    default:
        echo "Neznámá akce admina.";
        break;
}
