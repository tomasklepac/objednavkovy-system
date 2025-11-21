<?php
// -------------------------------------------------
// Router: user management (admin)
// -------------------------------------------------

use App\Controllers\UserController;
use App\Config\Database;

$userController = new UserController();

// Get action from parameter
$action = $_GET['action'] ?? null;

// Permission check: only logged in admin
if (!isset($_SESSION['user_id']) || !in_array('admin', $_SESSION['roles'], true)) {
    echo "Access denied.";
    exit;
}

switch ($action) {
    case 'users':
        // List all users
        $users = $userController->getAllUsers();
        require __DIR__ . '/../../app/Views/users_view.php';
        break;

    case 'approve_user':
        // Approve user (activate account)
        if (!empty($_GET['id'])) {
            $userController->approveUser((int)$_GET['id']);
        }
        header("Location: index.php?action=users");
        exit;

    case 'block_user':
        // Block user (deactivate account)
        if (!empty($_GET['id'])) {
            $userController->blockUser((int)$_GET['id']);
        }
        header("Location: index.php?action=users");
        exit;

    default:
        echo "Unknown user management action.";
        break;
}
