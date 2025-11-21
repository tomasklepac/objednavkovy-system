<?php

/**
 * Controller for users – handles registration, login/logout, user management and roles.
 * Works with user_model.php for database operations.
 */
require_once __DIR__ . '/../Models/user_model.php';
require_once __DIR__ . '/../../config/db.php';

class user_controller {

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
        $user = user_model::findByEmail($email);

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
        $_SESSION['roles']      = user_model::fetchRoles($user['id']);

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
        if (user_model::emailExists($email)) {
            return "User with this email already exists!";
        }

        // Hash password with BCRYPT
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Customers are auto-activated, suppliers and admins need approval
        $isActive = ($role === 'customer') ? 1 : 0;
        $isApproved = ($role === 'customer') ? 1 : 0;

        // Create user
        $userId = user_model::createUser($email, $hash, $name, $isActive, $isApproved);

        // Assign role
        user_model::assignRole($userId, $role);

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
        return user_model::getAllUsers();
    }

    /**
     * Activates (approves) a user account.
     *
     * @param int $userId User ID
     * @return void
     */
    public function approveUser(int $userId): void {
        user_model::approveUser($userId);
    }

    /**
     * Deactivates (blocks) a user account.
     *
     * @param int $userId User ID
     * @return void
     */
    public function blockUser(int $userId): void {
        user_model::blockUser($userId);
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
        return user_model::getAllAdmins();
    }

    /**
     * Returns pending admin requests (not yet approved by SuperAdmin).
     *
     * @return array[] List of pending requests
     */
    public function getPendingAdminRequests(): array {
        return user_model::getPendingAdminRequests();
    }

    /**
     * Approves an admin user (SuperAdmin only).
     *
     * @param int $userId Admin user ID
     * @return void
     */
    public function approveAdmin(int $userId): void {
        user_model::approveAdmin($userId);
    }

    /**
     * Rejects an admin user (SuperAdmin only).
     *
     * @param int $userId Admin user ID
     * @return void
     */
    public function rejectAdmin(int $userId): void {
        user_model::rejectAdmin($userId);
    }

    /**
     * Blocks an admin user (SuperAdmin only).
     *
     * @param int $userId Admin user ID
     * @return void
     */
    public function blockAdmin(int $userId): void {
        user_model::blockUser($userId);
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

