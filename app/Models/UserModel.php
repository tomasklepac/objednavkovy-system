<?php

namespace App\Models;

// Load the database connection file
require_once __DIR__ . '/../../config/db.php';

/**
 * Model for users.
 * Contains static methods for communicating with the `users` table.
 */
class UserModel {

    // ================================================================
    // READ
    // ================================================================

    /**
     * Finds a user by email.
     *
     * @param string $email User's email
     * @return array|null User data or null if not found
     */
    public static function findByEmail(string $email): ?array {
        $db = \Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Returns all users with their roles.
     *
     * @return array[] List of all users with roles
     */
    public static function getAllUsers(): array {
        $db = \Database::getInstance();
        $stmt = $db->query("
            SELECT u.id, u.email, u.name, u.is_active, GROUP_CONCAT(r.code) as roles
            FROM users u
            LEFT JOIN user_role ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            GROUP BY u.id
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Gets all roles for a user.
     *
     * @param int $userId User ID
     * @return array List of role codes
     */
    public static function fetchRoles(int $userId): array {
        $db = \Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.code
            FROM roles r
            JOIN user_role ur ON r.id = ur.role_id
            WHERE ur.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    // ================================================================
    // CREATE
    // ================================================================

    /**
     * Checks if email already exists in the database.
     *
     * @param string $email Email to check
     * @return bool True if email exists, false otherwise
     */
    public static function emailExists(string $email): bool {
        $db = \Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Creates a new user with email, password hash, name, active status and approval status.
     *
     * @param string $email User's email
     * @param string $passwordHash Password hash (use PASSWORD_BCRYPT)
     * @param string $name User's name
     * @param int $isActive Active status (1 or 0)
     * @param int $isApproved Approval status for admins (1 or 0)
     * @return int Last inserted user ID
     */
    public static function createUser(
        string $email,
        string $passwordHash,
        string $name,
        int $isActive,
        int $isApproved = 1
    ): int {
        $db = \Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO users (email, password_hash, name, is_active, is_approved)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$email, $passwordHash, $name, $isActive, $isApproved]);
        return (int)$db->lastInsertId();
    }

    /**
     * Assigns a role to a user.
     *
     * @param int $userId User ID
     * @param string $roleCode Role code (e.g., 'customer', 'supplier', 'admin')
     * @return void
     */
    public static function assignRole(int $userId, string $roleCode): void {
        $db = \Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO user_role (user_id, role_id)
            SELECT ?, id FROM roles WHERE code = ?
        ");
        $stmt->execute([$userId, $roleCode]);
    }

    // ================================================================
    // UPDATE
    // ================================================================

    /**
     * Activates (approves) a user.
     *
     * @param int $userId User ID
     * @return void
     */
    public static function approveUser(int $userId): void {
        $db = \Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
        $stmt->execute([$userId]);
    }

    /**
     * Blocks (deactivates) a user.
     *
     * @param int $userId User ID
     * @return void
     */
    public static function blockUser(int $userId): void {
        $db = \Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        $stmt->execute([$userId]);
    }

    /**
     * Gets all admin users with their approval status for SuperAdmin panel.
     *
     * @return array[] List of admins with approval status
     */
    public static function getAllAdmins(): array {
        $db = \Database::getInstance();
        $stmt = $db->query("
            SELECT u.id, u.email, u.name, u.is_active, u.is_approved, u.created_at
            FROM users u
            JOIN user_role ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE r.code = 'admin'
            ORDER BY u.created_at DESC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Gets pending admin requests (not yet approved by SuperAdmin).
     *
     * @return array[] List of pending admin requests
     */
    public static function getPendingAdminRequests(): array {
        $db = \Database::getInstance();
        $stmt = $db->query("
            SELECT u.id, u.email, u.name, u.created_at
            FROM users u
            JOIN user_role ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE r.code = 'admin' AND u.is_approved = 0
            ORDER BY u.created_at ASC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Approves an admin user (SuperAdmin only).
     *
     * @param int $userId Admin user ID
     * @return void
     */
    public static function approveAdmin(int $userId): void {
        $db = \Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET is_approved = 1 WHERE id = ?");
        $stmt->execute([$userId]);
    }

    /**
     * Rejects an admin user (SuperAdmin only).
     *
     * @param int $userId Admin user ID
     * @return void
     */
    public static function rejectAdmin(int $userId): void {
        $db = \Database::getInstance();
        // First, remove admin role
        $stmt = $db->prepare("
            DELETE FROM user_role 
            WHERE user_id = ? AND role_id = (SELECT id FROM roles WHERE code = 'admin')
        ");
        $stmt->execute([$userId]);
        
        // Then delete the user if no other roles
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM user_role WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        
        if ($stmt->fetchColumn() === 0) {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
        }
    }

    /**
     * Checks if a user has super_admin role.
     *
     * @param int $userId User ID
     * @return bool True if user is super_admin
     */
    public static function isSuperAdmin(int $userId): bool {
        $db = \Database::getInstance();
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM user_role ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = ? AND r.code = 'super_admin'
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Finds a user by ID.
     *
     * @param int $userId User ID
     * @return array|null User data or null if not found
     */
    public static function findById(int $userId): ?array {
        $db = \Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}

