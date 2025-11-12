<?php

require_once __DIR__ . '/../Core/Model.php';

class Gestionnaire extends Model {
    protected $table = 'gestionnaire';
    protected $userTable = 'utilisateur';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Create a new gestionnaire demand with terrains
     */
    public function createDemand($data, $terrains) {
        try {
            $this->db->beginTransaction();
            
            // First, insert into utilisateur table
            $query = "INSERT INTO {$this->userTable} (nom, prenom, email, password, num_tel) 
                      VALUES (:nom, :prenom, :email, :password, :num_tel)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':nom' => $data['nom'],
                ':prenom' => $data['prenom'],
                ':email' => $data['email'],
                ':password' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT), // Temporary password
                ':num_tel' => $data['telephone'] ?? null
            ]);
            
            $userId = $this->db->lastInsertId();
            
            // Then, insert into gestionnaire table
            $query = "INSERT INTO {$this->table} (id, status, date_validation) 
                      VALUES (:id, 'en attente', NULL)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $userId]);
            
            // Insert terrains associated with this demand
            foreach ($terrains as $terrain) {
                $this->insertTerrainForDemand($userId, $terrain);
            }
            
            $this->db->commit();
            return $userId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Insert terrain for gestionnaire demand
     */
    private function insertTerrainForDemand($gestionnaireId, $terrainData) {
        // Prepare justificatif as JSON array
        $justificatifJson = !empty($terrainData['justificatif']) ? 
            json_encode(explode(',', $terrainData['justificatif'])) : '[]';
        
        $query = "INSERT INTO terrain (
                    id_gestionnaire, nom_terrain, localisation, type_terrain, 
                    format_terrain, prix_heure, justificatif, statut, etat, image
                  ) VALUES (
                    :id_gestionnaire, :nom_terrain, :localisation, :type_terrain,
                    :format_terrain, :prix_heure, :justificatif, 'non disponible', 'en attente', ''
                  )";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id_gestionnaire' => $gestionnaireId,
            ':nom_terrain' => $terrainData['nom_terrain'],
            ':localisation' => $terrainData['localisation'],
            ':type_terrain' => $terrainData['type_terrain'],
            ':format_terrain' => $terrainData['format_terrain'],
            ':prix_heure' => $terrainData['prix_heure'],
            ':justificatif' => $justificatifJson
        ]);
        
        $terrainId = $this->db->lastInsertId();
        
        // Insert horaires if provided
        if (!empty($terrainData['heure_ouverture']) && !empty($terrainData['heure_fermeture'])) {
            $query = "INSERT INTO horaires (id_terrain, heure_ouverture, heure_fermeture) 
                      VALUES (:id_terrain, :heure_ouverture, :heure_fermeture)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id_terrain' => $terrainId,
                ':heure_ouverture' => $terrainData['heure_ouverture'],
                ':heure_fermeture' => $terrainData['heure_fermeture']
            ]);
        }
        
        // Insert options if provided
        if (!empty($terrainData['options'])) {
            foreach ($terrainData['options'] as $optionId => $optionData) {
                if (isset($optionData['selected']) && $optionData['selected'] == '1' && !empty($optionData['prix'])) {
                    $query = "INSERT INTO posseder (id_option, id_terrain, prix_option, disponible) 
                              VALUES (:id_option, :id_terrain, :prix_option, 1)";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([
                        ':id_option' => $optionId,
                        ':id_terrain' => $terrainId,
                        ':prix_option' => floatval($optionData['prix'])
                    ]);
                }
            }
        }
        
        return $terrainId;
    }

    /**
     * Get all pending demands
     */
    public function getPendingDemands() {
        $query = "SELECT u.*, g.*, COUNT(t.id_terrain) as terrain_count 
                  FROM {$this->table} g
                  INNER JOIN {$this->userTable} u ON g.id = u.id
                  LEFT JOIN terrain t ON g.id = t.id_gestionnaire AND t.etat = 'en attente'
                  WHERE g.status = 'en attente'
                  GROUP BY g.id
                  ORDER BY g.date_validation DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get demand details with terrains
     */
    public function getDemandById($id) {
        // Get gestionnaire info with user data
        $query = "SELECT u.*, g.* 
                  FROM {$this->table} g
                  INNER JOIN {$this->userTable} u ON g.id = u.id
                  WHERE g.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        $gestionnaire = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$gestionnaire) {
            return null;
        }
        
        // Get associated terrains
        $query = "SELECT * FROM terrain WHERE id_gestionnaire = :id AND etat = 'en attente'";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        $gestionnaire['terrains'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $gestionnaire;
    }

    /**
     * Approve gestionnaire demand
     */
    public function approveDemand($id, $password) {
        try {
            $this->db->beginTransaction();
            
            // Update utilisateur password
            $query = "UPDATE {$this->userTable} 
                      SET password = :password
                      WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id' => $id,
                ':password' => password_hash($password, PASSWORD_DEFAULT)
            ]);
            
            // Update gestionnaire status
            $query = "UPDATE {$this->table} 
                      SET status = 'accepté', 
                          date_validation = NOW()
                      WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            
            // Update all associated terrains to 'acceptée' and 'disponible'
            $query = "UPDATE terrain 
                      SET etat = 'acceptée', statut = 'disponible'
                      WHERE id_gestionnaire = :id AND etat = 'en attente'";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Reject gestionnaire demand
     */
    public function rejectDemand($id, $reason = null) {
        try {
            $this->db->beginTransaction();
            
            // Update gestionnaire status
            $query = "UPDATE {$this->table} 
                      SET status = 'refusé'
                      WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            
            // Update associated terrains to 'refusée'
            $query = "UPDATE terrain 
                      SET etat = 'refusée'
                      WHERE id_gestionnaire = :id AND etat = 'en attente'";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Get all available options
     */
    public function getAllOptions() {
        $query = "SELECT * FROM options ORDER BY nom_option";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if email already exists
     */
    public function emailExists($email) {
        $query = "SELECT id FROM {$this->userTable} WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
