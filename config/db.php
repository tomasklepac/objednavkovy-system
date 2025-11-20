<?php
/**
 * Database Class
 * ------------------
 * Provides database connection using PDO.
 * Implements the "Singleton" design pattern – the connection is created only once
 * and the same instance is used throughout the application.
 */
class Database {
    // Single instance of this class (singleton)
    private static $instance = null;

    // PDO object (actual database connection)
    private $pdo;

    /**
     * Constructor is private → cannot create instance using new from outside.
     * Connects to the database and stores the PDO object.
     */
    private function __construct() {
        // Database credentials
        $host    = "127.0.0.1";           // database server
        $db      = "objednavkovy_system"; // database name
        $user    = "root";                // default user in XAMPP
        $pass    = "";                    // password (empty by default)
        $charset = "utf8mb4";             // proper encoding for Czech and emoji

        // DSN = Data Source Name (string for PDO)
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        // PDO options
        $options = [
            // Errors will be thrown as exceptions (better for debugging)
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Results will be returned as associative arrays
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        // Create the PDO object (database connection)
        $this->pdo = new PDO($dsn, $user, $pass, $options);
        
        // Set character set for the connection
        $this->pdo->exec("SET NAMES utf8mb4");
    }

    /**
     * Static method to get the single PDO instance
     *
     * @return PDO
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}
