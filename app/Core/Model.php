<?php
require_once __DIR__ . '/Database.php';

class Model {
    protected $db;

    public function __construct() {
        // Connect to the database via Database.php
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    // Optional: helper for fetching all records from a table
    protected function getAll($table) {
        $stmt = $this->db->query("SELECT * FROM $table");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Optional: helper for fetching one record by id
    protected function getById($table, $id) {
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
