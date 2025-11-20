<?php

/**
 * Controller for users – handles registration, login/logout, user management and roles.
 * Works with user_model.php for database operations.
 */
require_once __DIR__ . '/../Models/user_model.php';

class user_controller {

    // ================================================================
    // LOGIN / LOGOUT
    // ================================================================

    /**
     * Authenticates a user with email and password.
     * Sets session variables on successful login.
     *
     * @param string $email User's email
     * @param string $password User's password (plain text)
     * @return string Status: 'ok', 'invalid', or 'inactive'
     */
    public function login(string $email, string $password): string {
        $user = user_model::findByEmail($email);

        if (!$user) {
            return 'invalid';
        }

        if ((int)$user['is_active'] !== 1) {
            return 'inactive';
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
     *
     * @param string $email User's email
     * @param string $password Password (plain text)
     * @param string $passwordConfirm Password confirmation
     * @param string $role Role ('customer' or 'supplier')
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
        if (!in_array($role, ['customer', 'supplier'], true)) {
            return "Invalid role.";
        }

        // Check if email already exists
        if (user_model::emailExists($email)) {
            return "User with this email already exists!";
        }

        // Hash password with BCRYPT
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Customers are auto-activated, suppliers need approval
        $isActive = ($role === 'customer') ? 1 : 0;

        // Create user
        $userId = user_model::createUser($email, $hash, $name, $isActive);

        // Assign role
        user_model::assignRole($userId, $role);

        return $isActive === 1 ? 'registered_active' : 'registered_inactive';
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
}

