<?php

// Načteme soubor s databázovým připojením
require_once __DIR__ . '/../../config/db.php';

/**
 * Model pro uživatele.
 * Obsahuje metody pro komunikaci s tabulkou `users`.
 */
class user_model {

    // ================================================================
    // READ
    // ================================================================

    /**
     * Najde uživatele podle emailu.
     *
     * @param string $email Email uživatele
     * @return array|false Asociativní pole uživatele nebo false, pokud neexistuje
     */
    public static function findByEmail(string $email) {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("
            SELECT *
            FROM users
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
