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

            $result = $userController->register($name, $email, $password, $role);

            if ($result === 'ok') {
                if ($role === 'supplier') {
                    echo "<p style='color:green'>Registration successful. Wait for administrator approval.</p>";
                } else {
                    echo "<p style='color:green'>Registration successful. You can now log in.</p>";
                }
                echo "<p><a href='index.php?action=login'>← Log in</a></p>";
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
