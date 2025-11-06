<?php

require_once __DIR__ . '/../Core/Model.php';
require_once __DIR__ . '/User.php';

class Terrain extends Model {
    protected $table = 'terrain';

    private $gestionnaireId;

    public function __construct($gestionnaireId = null) {
        parent::__construct();
        $this->gestionnaireId = $gestionnaireId;
    }

    public function getGestionnaireTerrains()
    {
        if (!$this->gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }
        $stmt = $this->db->prepare("SELECT * FROM terrain WHERE id_gestionnaire = ? ");
        $stmt->execute([$this->gestionnaireId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        if (!$this->gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        $query = "UPDATE {$this->table} SET 
                  nom_terrain = :nom_terrain,
                  localisation = :localisation,
                  type_terrain = :type_terrain,
                  format_terrain = :format_terrain,
                  prix_heure = :prix,
                  statut = :statut";
        
        $params = [
            ':nom_terrain' => $data['nom_terrain'],
            ':localisation' => $data['localisation'],
            ':type_terrain' => $data['type_terrain'],
            ':format_terrain' => $data['format_terrain'],
            ':prix' => $data['prix'],
            ':statut' => $data['statut'] ?? 'disponible',
            ':id' => $id,
            ':id_gestionnaire' => $this->gestionnaireId
        ];

        // Add image update if provided
        if (!empty($data['image'])) {
            $query .= ", image = :image";
            $params[':image'] = $data['image'];
        }

        $query .= " WHERE id_terrain = :id AND id_gestionnaire = :id_gestionnaire";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function delete($id) {
        if (!$this->gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        $query = "DELETE FROM {$this->table} WHERE id_terrain = :id AND id_gestionnaire = :id_gestionnaire";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':id_gestionnaire' => $this->gestionnaireId
        ]);
    }

    public function create($data) {
        if (!$this->gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        $query = "INSERT INTO {$this->table} (nom_terrain, image, localisation, type_terrain, format_terrain, prix_heure, statut, id_gestionnaire) 
                 VALUES (:nom_terrain, :image, :localisation, :type_terrain, :format_terrain, :prix, 'disponible', :id_gestionnaire)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':nom_terrain' => $data['nom_terrain'],
            ':image' => $data['image'],
            ':prix' => $data['prix'],
            ':localisation' => $data['localisation'],
            ':type_terrain' => $data['type_terrain'],
            ':format_terrain' => $data['format_terrain'],
            ':id_gestionnaire' => $this->gestionnaireId
        ]);
    }
    
    public function getAllTerrains() {
        return $this->getAll($this->table);
    }

    public function getTerrainById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_terrain = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAvailableTerrains() {
        $stmt = $this->db->prepare("SELECT * FROM terrain WHERE statut = 'disponible'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableTerrainsFiltered(?string $search, ?string $taille, ?string $type) {
        $conditions = ["statut = 'disponible'"];
        $params = [];

        if ($search !== null && $search !== '') {
            $conditions[] = '(localisation LIKE :search OR type_terrain LIKE :search OR format_terrain LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        if ($taille !== null && $taille !== '') {
            $conditions[] = 'format_terrain = :taille';
            $params[':taille'] = $taille;
        }

        if ($type !== null && $type !== '') {
            $conditions[] = 'type_terrain = :type';
            $params[':type'] = $type;
        }

        $whereSql = implode(' AND ', $conditions);
        $sql = "SELECT * FROM {$this->table} WHERE $whereSql ORDER BY localisation ASC";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}
