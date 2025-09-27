<?php
require_once __DIR__ . '/../Models/User.php';

class UserController {
    public function login($email, $password) {
        $user = User::findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            // přihlášení OK
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            return true;
        }

        // špatný email nebo heslo
        return false;
    }
}
