<?php
// -------------------------------------------------
// Router: user authentication (login / logout / register)
// -------------------------------------------------

require_once __DIR__ . '/../../app/Controllers/user_controller.php';

$action = $_GET['action'] ?? null;
$userController = new user_controller();

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $result = $userController->login($email, $password);

            if ($result === 'ok') {
                header("Location: index.php?action=dashboard");
                exit;
            } elseif ($result === 'inactive') {
                $error = "Account is not yet approved. Wait for administrator activation.";
                require __DIR__ . "/../../app/Views/login_view.php";
            } elseif ($result === 'not_approved') {
                $error = "Your admin account is awaiting approval from SuperAdmin. Please wait.";
                require __DIR__ . "/../../app/Views/login_view.php";
            } else {
                $error = "Invalid credentials.";
                require __DIR__ . "/../../app/Views/login_view.php";
            }
        } else {
            require __DIR__ . "/../../app/Views/login_view.php";
        }
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';
            $role = $_POST['role'] ?? 'customer';

            if ($password !== $passwordConfirm) {
                $error = "Passwords do not match.";
                require __DIR__ . "/../../app/Views/register_view.php";
                break;
            }

            $result = $userController->register($email, $password, $passwordConfirm, $role, $name);

            if ($result === 'registered_active') {
                $title = "Registration Successful!";
                $message = "Your account has been created. You can now log in.";
                $details = [];
                $buttons = [
                    ['label' => 'Go to Login', 'url' => 'index.php?action=login']
                ];
                require __DIR__ . "/../../app/Views/success_message_view.php";
            } elseif ($result === 'registered_inactive') {
                $title = "Registration Successful!";
                $message = "Your account has been created. Wait for administrator approval before logging in.";
                $details = [];
                $buttons = [
                    ['label' => 'Return to Login', 'url' => 'index.php?action=login']
                ];
                require __DIR__ . "/../../app/Views/success_message_view.php";
            } elseif ($result === 'admin_request_sent') {
                $title = "Admin Request Submitted!";
                $message = "Your admin account request has been submitted. SuperAdmin will review your request soon.";
                $details = [];
                $buttons = [
                    ['label' => 'Return to Login', 'url' => 'index.php?action=login']
                ];
                require __DIR__ . "/../../app/Views/success_message_view.php";
            } else {
                $error = $result;
                require __DIR__ . "/../../app/Views/register_view.php";
            }
        } else {
            require __DIR__ . "/../../app/Views/register_view.php";
        }
        break;

    case 'logout':
        $userController->logout();
        header("Location: index.php?action=login");
        exit;

    default:
        require __DIR__ . "/../../app/Views/login_view.php";
        break;
}
