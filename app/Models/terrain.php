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

    public function getAvailableTerrainsFiltered(?string $search, ?string $taille, ?string $type) {
        $conditions = ["statut = 'disponible'"];
        $params = [];

        if (null !== $search && '' !== $search) {
            $conditions[] = '(localisation LIKE :search OR type_terrain LIKE :search OR format_terrain LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        if (null !== $taille && '' !== $taille) {
            $conditions[] = 'format_terrain = :taille';
            $params[':taille'] = $taille;
        }

        if (null !== $type && '' !== $type) {
            $conditions[] = 'type_terrain = :type';
            $params[':type'] = $type;
        }

        $whereSql = implode(' AND ', $conditions);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereSql} ORDER BY localisation ASC";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
