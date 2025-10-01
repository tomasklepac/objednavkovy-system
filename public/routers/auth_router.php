<?php
// public/routers/auth_router.php

require_once __DIR__ . '/../../app/Controllers/user_controller.php';
require_once __DIR__ . '/../../config/db.php';

$action = $_GET['action'] ?? null;
$userController = new user_controller(Database::getInstance());

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($userController->login($email, $password)) {
                // ✅ Po úspěšném přihlášení rovnou na dashboard
                header("Location: index.php?action=dashboard");
                exit;
            } else {
                echo "<p style='color:red'>Neplatné přihlašovací údaje</p>";
                require __DIR__ . "/../../app/Views/login_view.php";
            }
        } else {
            require __DIR__ . "/../../app/Views/login_view.php";
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
