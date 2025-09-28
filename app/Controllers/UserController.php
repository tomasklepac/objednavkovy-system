<?php
require_once __DIR__ . '/../Models/User.php';

class UserController {
    private $pdo;

    // === Konstruktor ===
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // -------------------------------------------------
    // LOGIN
    // -------------------------------------------------
    // Pokusí se přihlásit uživatele podle emailu a hesla
    // Vrací: 'ok' | 'inactive' | 'invalid'
    public function login($email, $password) {
        // 1. najdeme uživatele podle emailu
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return 'invalid'; // email v DB není
        }

        // 2. kontrola, jestli je účet aktivní
        if ((int)$user['is_active'] !== 1) {
            return 'inactive'; // dodavatel ještě neschválený adminem
        }

        // 3. ověření hesla
        if (!password_verify($password, $user['password_hash'])) {
            return 'invalid'; // heslo nesedí
        }

        // 4. přihlášení proběhlo úspěšně → uložíme do session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['roles'] = $this->fetchRoles($user['id']);

        return 'ok';
    }

    // -------------------------------------------------
    // REGISTRACE
    // -------------------------------------------------
    // Vytvoří nového uživatele a přiřadí mu roli
    // Vrací: 'registered_active' | 'registered_inactive' nebo text chyby
    public function register($email, $password, $passwordConfirm, $role) {
        // 1. kontrola hesel
        if ($password !== $passwordConfirm) {
            return "Hesla se neshodují!";
        }

        // 2. povolené role
        if (!in_array($role, ['customer', 'supplier'], true)) {
            return "Neplatná role.";
        }

        // 3. kontrola duplicity emailu
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            return "Uživatel s tímto emailem už existuje!";
        }

        // 4. hashování hesla
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // 5. nastavení aktivace účtu
        $isActive = ($role === 'customer') ? 1 : 0;

        // 6. vložíme uživatele do DB
        $stmt = $this->pdo->prepare("
            INSERT INTO users (email, password_hash, name, is_active)
            VALUES (?, ?, '', ?)
        ");
        $stmt->execute([$email, $hash, $isActive]);
        $userId = $this->pdo->lastInsertId();

        // 7. přiřadíme roli
        $roleStmt = $this->pdo->prepare("
            INSERT INTO user_role (user_id, role_id)
            SELECT ?, id FROM roles WHERE code = ?
        ");
        $roleStmt->execute([$userId, $role]);

        // 8. vrátíme výsledek
        return $isActive === 1 ? 'registered_active' : 'registered_inactive';
    }

    // -------------------------------------------------
    // Vrátí všechny uživatele + jejich role
    // -------------------------------------------------
    public function getAllUsers() {
        $stmt = $this->pdo->query("
            SELECT u.id, u.email, u.is_active, GROUP_CONCAT(r.code) as roles
            FROM users u
            LEFT JOIN user_role ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            GROUP BY u.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // Schválí uživatele (aktivuje)
    // -------------------------------------------------
    public function approveUser($userId) {
        $stmt = $this->pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
        $stmt->execute([$userId]);
    }

    // -------------------------------------------------
    // Zablokuje uživatele
    // -------------------------------------------------
    public function blockUser($userId) {
        $stmt = $this->pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        $stmt->execute([$userId]);
    }

    // -------------------------------------------------
    // PRIVATE: načtení rolí uživatele
    // -------------------------------------------------
    private function fetchRoles($userId) {
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
