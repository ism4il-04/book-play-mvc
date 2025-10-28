<?php
// Database.php - Singleton pattern for database connection

use Dotenv\Dotenv;

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            // Load environment variables if not already loaded
            if (!isset($_ENV['DB_HOST'])) {
                $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
                $dotenv->load();
            }
            
            // Create PDO connection
            $this->conn = new PDO(
                'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8',
                $_ENV['DB_USER'],
                $_ENV['DB_PASS']
            );
            
            // Set error mode and fetch mode
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Database connection error: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
    
    // Magic method to call PDO methods directly on Database instance
    public function __call($method, $args) {
        return call_user_func_array([$this->conn, $method], $args);
    }
}
?>