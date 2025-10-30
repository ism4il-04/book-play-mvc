<?php

require_once __DIR__ . '/../Core/Model.php';

class Terrain extends Model {
    protected $table = 'terrain';

    public function getAllTerrains() {
        return $this->getAll($this->table);
    }

    public function getTerrainById($id) {
        return $this->getById($this->table, $id);
    }

    public function getAvailableTerrains() {
        $stmt = $this->db->prepare("SELECT * FROM terrain WHERE statut = 'disponible'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
