<?php

// Controller for users – handles registration, login/logout, user management and roles.
require_once __DIR__ . '/../Models/user_model.php';

class user_controller {
    /** @var PDO */
    private $pdo; // Database connection

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ================================================================
    // LOGIN / LOGOUT
    // ================================================================

    public function login(string $email, string $password): string {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

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
        $_SESSION['roles']      = $this->fetchRoles($user['id']);

        return 'ok';
    }

    public function logout(): void {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    // ================================================================
    // REGISTRATION
    // ================================================================

    public function register(
        string $email,
        string $password,
        string $passwordConfirm,
        string $role,
        string $name
    ): string {
        // CSRF protection
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
            ) {
                die('Invalid CSRF token.');
            }
        }

        if ($password !== $passwordConfirm) {
            return "Passwords do not match!";
        }

        if (!in_array($role, ['customer', 'supplier'], true)) {
            return "Invalid role.";
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            return "User with this email already exists!";
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $isActive = ($role === 'customer') ? 1 : 0;

        $stmt = $this->pdo->prepare("
            INSERT INTO users (email, password_hash, name, is_active)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$email, $hash, $name, $isActive]);
        $userId = $this->pdo->lastInsertId();

        $roleStmt = $this->pdo->prepare("
            INSERT INTO user_role (user_id, role_id)
            SELECT ?, id FROM roles WHERE code = ?
        ");
        $roleStmt->execute([$userId, $role]);

        return $isActive === 1 ? 'registered_active' : 'registered_inactive';
    }

    // ================================================================
    // ADMIN: USER MANAGEMENT
    // ================================================================

    public function getAllUsers(): array {
        $stmt = $this->pdo->query("
            SELECT u.id, u.email, u.is_active, GROUP_CONCAT(r.code) as roles
            FROM users u
            LEFT JOIN user_role ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            GROUP BY u.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approveUser(int $userId): void {
        // CSRF protection
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
            ) {
                die('Invalid CSRF token.');
            }
        }

        $stmt = $this->pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
        $stmt->execute([$userId]);
    }

    public function blockUser(int $userId): void {
        // CSRF protection
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
            ) {
                die('Invalid CSRF token.');
            }
        }

        $stmt = $this->pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        $stmt->execute([$userId]);
    }

    // ================================================================
    // PRIVATE
    // ================================================================

    private function fetchRoles(int $userId): array {
        $roleStmt = $this->pdo->prepare("
            SELECT r.code
            FROM roles r
            JOIN user_role ur ON r.id = ur.role_id
            WHERE ur.user_id = ?
        ");
        $roleStmt->execute([$userId]);
        return $roleStmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
