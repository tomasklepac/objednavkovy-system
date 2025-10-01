<?php
// Natáhneme soubor s databázovým připojením
require_once __DIR__ . '/../../config/db.php';

class user_model {
    // -------------------------------------------------
    // Najde uživatele podle emailu
    // -------------------------------------------------
    public static function findByEmail($email) {
        // získáme PDO připojení k databázi
        $pdo = Database::getInstance();

        // připravíme SQL dotaz s placeholderem
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");

        // spustíme dotaz a dosadíme hodnotu
        $stmt->execute([$email]);

        // vrátíme nalezeného uživatele jako asociativní pole
        // nebo false, pokud nebyl nalezen
        return $stmt->fetch();
    }
}
