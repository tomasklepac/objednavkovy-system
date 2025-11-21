<?php
// -------------------------------------------------
// Router: user authentication (login / logout / register)
// -------------------------------------------------

use App\Controllers\UserController;

$action = $_GET['action'] ?? null;
$userController = new UserController();

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
                $error = "Hesla se neshodují.";
                require __DIR__ . "/../../app/Views/register_view.php";
                break;
            }

            $result = $userController->register($email, $password, $passwordConfirm, $role, $name);

            if ($result === 'registered_active') {
                $title = "Registrace úspěšná!";
                $message = "Tvůj účet byl vytvořen. Nyní se můžeš přihlásit.";
                $details = [];
                $buttons = [
                    ['label' => 'Přejít na přihlášení', 'url' => 'index.php?action=login']
                ];
                require __DIR__ . "/../../app/Views/success_message_view.php";
            } elseif ($result === 'registered_inactive') {
                $title = "Registrace úspěšná!";
                $message = "Tvůj účet byl vytvořen. Počkej na schválení administrátorem, než se budeš moci přihlásit.";
                $details = [];
                $buttons = [
                    ['label' => 'Zpět na přihlášení', 'url' => 'index.php?action=login']
                ];
                require __DIR__ . "/../../app/Views/success_message_view.php";
            } elseif ($result === 'admin_request_sent') {
                $title = "Žádost o admin přístup odeslána!";
                $message = "Tvá žádost o admin práva byla odeslána. SuperAdmin si ji brzy projde a rozhodne.";
                $details = [];
                $buttons = [
                    ['label' => 'Zpět na přihlášení', 'url' => 'index.php?action=login']
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
