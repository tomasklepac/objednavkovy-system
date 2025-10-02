<?php
// -------------------------------------------------
// Router: správa uživatelů (admin)
// -------------------------------------------------

require_once __DIR__ . '/../../app/Controllers/user_controller.php';

$pdo = Database::getInstance();
$userController = new user_controller($pdo);

// Získání akce z parametru
$action = $_GET['action'] ?? null;

// Kontrola oprávnění: jen přihlášený admin
if (!isset($_SESSION['user_id']) || !in_array('admin', $_SESSION['roles'], true)) {
    echo "Přístup zamítnut.";
    exit;
}

switch ($action) {
    case 'users':
        // Výpis všech uživatelů
        $users = $userController->getAllUsers();
        require __DIR__ . '/../../app/Views/users_view.php';
        break;

    case 'approve_user':
        // Schválení uživatele (aktivace účtu)
        if (!empty($_GET['id'])) {
            $userController->approveUser((int)$_GET['id']);
        }
        header("Location: index.php?action=users");
        exit;

    case 'block_user':
        // Blokování uživatele (deaktivace účtu)
        if (!empty($_GET['id'])) {
            $userController->blockUser((int)$_GET['id']);
        }
        header("Location: index.php?action=users");
        exit;

    default:
        echo "Neznámá akce správy uživatelů.";
        break;
}
