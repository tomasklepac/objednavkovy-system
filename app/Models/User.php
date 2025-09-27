<?php
require_once __DIR__ . '/../../config/db.php';

class User {
    public static function findByEmail($email) {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(); // vrátí asociativní pole (nebo false, pokud není nalezen)
    }
}
