<?php
// Natáhneme model User (teď ho moc nepoužíváme, ale máme připravený)
require_once __DIR__ . '/../Models/User.php';

class UserController {
    private $pdo; // připojení k databázi (PDO objekt)

    // -------------------------------------------------
    // KONSTRUKTOR
    // -------------------------------------------------
    // Vytvoří instanci UserControlleru a uloží připojení k DB
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // -------------------------------------------------
    // LOGIN (přihlášení uživatele)
    // -------------------------------------------------
    // Vrací:
    //   'ok'        = přihlášení proběhlo
    //   'inactive'  = účet ještě není schválen (dodavatel)
    //   'invalid'   = špatný email nebo heslo
    public function login($email, $password) {
        // 1. najdeme uživatele podle emailu
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return 'invalid'; // email není v DB
        }

        // 2. kontrola aktivace účtu
        if ((int)$user['is_active'] !== 1) {
            return 'inactive';
        }

        // 3. kontrola hesla – ověříme proti hashovanému heslu v DB
        if (!password_verify($password, $user['password_hash'])) {
            return 'invalid';
        }

        // 4. uložíme údaje do session (uživatel je přihlášen)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['roles'] = $this->fetchRoles($user['id']);

        return 'ok';
    }

    // -------------------------------------------------
    // REGISTRACE (vytvoření účtu)
    // -------------------------------------------------
    // Vrací:
    //   'registered_active'   = zákazník → hned aktivní
    //   'registered_inactive' = dodavatel → čeká na schválení
    //   text                  = chybová hláška
    public function register($email, $password, $passwordConfirm, $role, $name) {
        // 1. hesla se musí shodovat
        if ($password !== $passwordConfirm) {
            return "Hesla se neshodují!";
        }

        // 2. povolíme jen role customer / supplier
        if (!in_array($role, ['customer', 'supplier'], true)) {
            return "Neplatná role.";
        }

        // 3. kontrola duplicity emailu
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            return "Uživatel s tímto emailem už existuje!";
        }

        // 4. zahashujeme heslo (bezpečné uložení)
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // 5. nastavení aktivace
        $isActive = ($role === 'customer') ? 1 : 0;

        // 6. vložíme uživatele do DB
        $stmt = $this->pdo->prepare("
            INSERT INTO users (email, password_hash, name, is_active)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$email, $hash, $name, $isActive]);
        $userId = $this->pdo->lastInsertId();

        // 7. přiřazení role
        $roleStmt = $this->pdo->prepare("
            INSERT INTO user_role (user_id, role_id)
            SELECT ?, id FROM roles WHERE code = ?
        ");
        $roleStmt->execute([$userId, $role]);

        return $isActive === 1 ? 'registered_active' : 'registered_inactive';
    }

    // -------------------------------------------------
    // ADMIN: seznam všech uživatelů
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
    // ADMIN: schválí uživatele (aktivuje účet)
    // -------------------------------------------------
    public function approveUser($userId) {
        $stmt = $this->pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
        $stmt->execute([$userId]);
    }

    // -------------------------------------------------
    // ADMIN: zablokuje uživatele (deaktivuje účet)
    // -------------------------------------------------
    public function blockUser($userId) {
        $stmt = $this->pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        $stmt->execute([$userId]);
    }

    // -------------------------------------------------
    // PRIVATE: načte role jednoho uživatele
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
