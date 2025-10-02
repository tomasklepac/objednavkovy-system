<?php

// Controller pro uživatele – zajišťuje registraci, login/logout, správu uživatelů a role.
require_once __DIR__ . '/../Models/user_model.php';

class user_controller {
    /** @var PDO */
    private $pdo; // Připojení k databázi

    // ================================================================
    // KONSTRUKTOR
    // ================================================================

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ================================================================
    // LOGIN / LOGOUT
    // ================================================================

    /**
     * Přihlásí uživatele podle emailu a hesla.
     *
     * Návratové hodnoty:
     * - 'ok'        = přihlášení proběhlo
     * - 'inactive'  = účet není aktivní (dodavatel čekající na schválení)
     * - 'invalid'   = špatný email nebo heslo
     */
    public function login(string $email, string $password): string {
        // Najdeme uživatele podle emailu
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return 'invalid'; // email není v DB
        }

        // Účet musí být aktivní
        if ((int)$user['is_active'] !== 1) {
            return 'inactive';
        }

        // Ověření hesla proti hashovanému heslu v DB
        if (!password_verify($password, $user['password_hash'])) {
            return 'invalid';
        }

        // Uložení údajů do session
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['roles']      = $this->fetchRoles($user['id']);

        return 'ok';
    }

    /**
     * Odhlášení uživatele – smaže session a ukončí relaci.
     */
    public function logout(): void {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    // ================================================================
    // REGISTRACE
    // ================================================================

    /**
     * Registrace nového uživatele.
     *
     * Návratové hodnoty:
     * - 'registered_active'   = zákazník → ihned aktivní
     * - 'registered_inactive' = dodavatel → čeká na schválení
     * - string                = chybová hláška
     */
    public function register(
        string $email,
        string $password,
        string $passwordConfirm,
        string $role,
        string $name
    ): string {
        // Hesla se musí shodovat
        if ($password !== $passwordConfirm) {
            return "Hesla se neshodují!";
        }

        // Povoleny jen role customer / supplier
        if (!in_array($role, ['customer', 'supplier'], true)) {
            return "Neplatná role.";
        }

        // Kontrola duplicity emailu
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            return "Uživatel s tímto emailem už existuje!";
        }

        // Hash hesla
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Aktivní = customer, neaktivní = supplier
        $isActive = ($role === 'customer') ? 1 : 0;

        // Vložení uživatele
        $stmt = $this->pdo->prepare("
            INSERT INTO users (email, password_hash, name, is_active)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$email, $hash, $name, $isActive]);
        $userId = $this->pdo->lastInsertId();

        // Přiřazení role
        $roleStmt = $this->pdo->prepare("
            INSERT INTO user_role (user_id, role_id)
            SELECT ?, id FROM roles WHERE code = ?
        ");
        $roleStmt->execute([$userId, $role]);

        return $isActive === 1 ? 'registered_active' : 'registered_inactive';
    }

    // ================================================================
    // ADMIN: SPRÁVA UŽIVATELŮ
    // ================================================================

    /**
     * Vrátí seznam všech uživatelů včetně jejich rolí.
     */
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

    /**
     * Aktivuje uživatele (schválení účtu adminem).
     */
    public function approveUser(int $userId): void {
        $stmt = $this->pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
        $stmt->execute([$userId]);
    }

    /**
     * Deaktivuje uživatele (zablokování účtu adminem).
     */
    public function blockUser(int $userId): void {
        $stmt = $this->pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        $stmt->execute([$userId]);
    }

    // ================================================================
    // PRIVATE
    // ================================================================

    /**
     * Vrátí pole rolí (např. ['customer', 'supplier']) pro konkrétního uživatele.
     */
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
