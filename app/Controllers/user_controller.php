<?php

namespace App\Controllers;

use App\Models\UserModel;

require_once __DIR__ . '/../../config/db.php';

/**
 * Controller for users – handles registration, login/logout, user management and roles.
 * Works with UserModel for database operations.
 */
class UserController {

    // ================================================================
    // LOGIN / LOGOUT
    // ================================================================

    /**
     * Authenticates a user with email and password.
     * Sets session variables on successful login.
     * Admins cannot login if not approved by SuperAdmin.
     *
     * @param string $email User's email
     * @param string $password User's password (plain text)
     * @return string Status: 'ok', 'invalid', 'inactive', or 'not_approved'
     */
    public function login(string $email, string $password): string {
        $user = UserModel::findByEmail($email);

        if (!$user) {
            return 'invalid';
        }

        if ((int)$user['is_active'] !== 1) {
            return 'inactive';
        }

        // Check if admin is approved
        if ((int)$user['is_approved'] !== 1) {
            return 'not_approved';
        }

        if (!password_verify($password, $user['password_hash'])) {
            return 'invalid';
        }

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['roles']      = UserModel::fetchRoles($user['id']);

        return 'ok';
    }

    /**
     * Logs out the user by destroying the session.
     *
     * @return void
     */
    public function logout(): void {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    // ================================================================
    // REGISTRATION
    // ================================================================

    /**
     * Registers a new user with validation.
     * Customers are auto-activated. Suppliers and Admins need approval.
     *
     * @param string $email User's email
     * @param string $password Password (plain text)
     * @param string $passwordConfirm Password confirmation
     * @param string $role Role ('customer', 'supplier', or 'admin')
     * @param string $name User's full name
     * @return string Registration status message
     */
    public function register(
        string $email,
        string $password,
        string $passwordConfirm,
        string $role,
        string $name
    ): string {
        // Validate passwords match
        if ($password !== $passwordConfirm) {
            return "Passwords do not match!";
        }

        // Validate role is allowed
        if (!in_array($role, ['customer', 'supplier', 'admin'], true)) {
            return "Invalid role.";
        }

        // Check if email already exists
        if (UserModel::emailExists($email)) {
            return "User with this email already exists!";
        }

        // Hash password with BCRYPT
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Customers are auto-activated, suppliers and admins need approval
        $isActive = ($role === 'customer') ? 1 : 0;
        $isApproved = ($role === 'customer') ? 1 : 0;

        // Create user
        $userId = UserModel::createUser($email, $hash, $name, $isActive, $isApproved);

        // Assign role
        UserModel::assignRole($userId, $role);

        if ($role === 'customer') {
            return 'registered_active';
        } elseif ($role === 'supplier') {
            return 'registered_inactive';
        } else {
            // Admin request
            return 'admin_request_sent';
        }
    }

    // ================================================================
    // ADMIN: USER MANAGEMENT
    // ================================================================

    /**
     * Returns all users with their roles and active status.
     *
     * @return array[] List of all users
     */
    public function getAllUsers(): array {
        return UserModel::getAllUsers();
    }

    /**
     * Activates (approves) a user account.
     *
     * @param int $userId User ID
     * @return void
     */
    public function approveUser(int $userId): void {
        UserModel::approveUser($userId);
    }

    /**
     * Deactivates (blocks) a user account.
     *
     * @param int $userId User ID
     * @return void
     */
    public function blockUser(int $userId): void {
        UserModel::blockUser($userId);
    }

    // ================================================================
    // SUPER ADMIN: ADMIN MANAGEMENT
    // ================================================================

    /**
     * Returns all admin users with their approval status.
     *
     * @return array[] List of all admins
     */
    public function getAllAdmins(): array {
        return UserModel::getAllAdmins();
    }

    /**
     * Returns pending admin requests (not yet approved by SuperAdmin).
     *
     * @return array[] List of pending requests
     */
    public function getPendingAdminRequests(): array {
        return UserModel::getPendingAdminRequests();
    }

    /**
     * Approves an admin user (SuperAdmin only).
     *
     * @param int $userId Admin user ID
     * @return void
     */
    public function approveAdmin(int $userId): void {
        UserModel::approveAdmin($userId);
    }

    /**
     * Rejects an admin user (SuperAdmin only).
     *
     * @param int $userId Admin user ID
     * @return void
     */
    public function rejectAdmin(int $userId): void {
        UserModel::rejectAdmin($userId);
    }

    /**
     * Blocks an admin user (SuperAdmin only).
     *
     * @param int $userId Admin user ID
     * @return void
     */
    public function blockAdmin(int $userId): void {
        UserModel::blockUser($userId);
    }

    /**
     * Unblocks (reactivates) an admin user (SuperAdmin only).
     *
     * @param int $userId Admin user ID
     * @return void
     */
    public function unblockAdmin(int $userId): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
        $stmt->execute([$userId]);
    }
}

