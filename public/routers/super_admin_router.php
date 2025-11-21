<?php
// -------------------------------------------------
// Router: SuperAdmin - Management of admin users
// -------------------------------------------------

require_once __DIR__ . '/../../app/Controllers/user_controller.php';

// Check if user is SuperAdmin
if (!isset($_SESSION['user_id']) || !user_model::isSuperAdmin($_SESSION['user_id'])) {
    header("Location: index.php?action=dashboard");
    exit;
}

$action = $_GET['action'] ?? null;
$userController = new user_controller();

switch ($action) {
    case 'super_admin':
        // Display SuperAdmin dashboard with all admins
        $adminUsers = $userController->getAllAdmins();
        $pendingRequests = $userController->getPendingAdminRequests();
        require __DIR__ . "/../../app/Views/super_admin_view.php";
        break;

    case 'approve_admin':
        // Approve a pending admin request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_id'])) {
            $adminId = (int)$_POST['admin_id'];
            
            // Verify it's a pending request
            $admin = user_model::findById($adminId);
            if ($admin && (int)$admin['is_approved'] === 0) {
                $userController->approveAdmin($adminId);
                // Auto-activate when approving
                $userController->unblockAdmin($adminId);
            }
        }
        header("Location: index.php?action=super_admin");
        exit;

    case 'reject_admin':
        // Reject (delete) a pending admin request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_id'])) {
            $adminId = (int)$_POST['admin_id'];
            
            // Verify it's a pending request
            $admin = user_model::findById($adminId);
            if ($admin && (int)$admin['is_approved'] === 0) {
                $userController->rejectAdmin($adminId);
            }
        }
        header("Location: index.php?action=super_admin");
        exit;

    case 'block_admin':
        // Block an approved admin
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_id'])) {
            $adminId = (int)$_POST['admin_id'];
            
            // Verify admin exists and is approved
            $admin = user_model::findById($adminId);
            if ($admin && (int)$admin['is_approved'] === 1) {
                $userController->blockAdmin($adminId);
            }
        }
        header("Location: index.php?action=super_admin");
        exit;

    case 'unblock_admin':
        // Unblock a blocked admin
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_id'])) {
            $adminId = (int)$_POST['admin_id'];
            
            // Verify admin exists
            $admin = user_model::findById($adminId);
            if ($admin) {
                $userController->unblockAdmin($adminId);
            }
        }
        header("Location: index.php?action=super_admin");
        exit;

    default:
        header("Location: index.php?action=super_admin");
        exit;
}
