<?php

require_once __DIR__ . '/../Core/Model.php';

class Terrain extends Model
{
    protected $table = 'terrain';

    public function getAllTerrains()
    {
        return $this->getAll($this->table);
    }

    public function getTerrainById($id)
    {
        return $this->getById($this->table, $id);
    }

    public function getAvailableTerrains($date, $time)
    {
        // Custom query for available terrains
        $stmt = $this->db->prepare("
            SELECT t.* FROM terrain t
            WHERE t.id NOT IN (
                SELECT terrain_id FROM reservations 
                WHERE date = ? AND time = ? AND status = 'confirmed'
            )
        ");
        $stmt->execute([$date, $time]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
