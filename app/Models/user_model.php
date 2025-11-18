<?php

// Load the database connection file
require_once __DIR__ . '/../../config/db.php';

/**
 * Model for users.
 * Contains methods for communicating with the `users` table.
 */
class user_model {

    // ================================================================
    // READ
    // ================================================================

    /**
     * Finds a user by email.
     *
     * @param string $email User's email
     * @return array|false Associative array of user or false if doesn't exist
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
