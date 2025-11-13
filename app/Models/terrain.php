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


    public function delete($id) {
        if (!$this->gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        try {
            // Start transaction
            $this->db->beginTransaction();

            // First, get all reservation IDs for this terrain to delete related records
            $getReservations = "SELECT id_reservation FROM reservation WHERE id_terrain = :id";
            $stmtGetReservations = $this->db->prepare($getReservations);
            $stmtGetReservations->execute([':id' => $id]);
            $reservations = $stmtGetReservations->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($reservations)) {
                // Delete facture records related to these reservations
                $reservationIds = implode(',', array_fill(0, count($reservations), '?'));
                $deleteFactures = "DELETE FROM facture WHERE id_reservation IN ($reservationIds)";
                $stmtFactures = $this->db->prepare($deleteFactures);
                $stmtFactures->execute($reservations);

                // Delete reservation_option records related to these reservations
                $deleteReservationOptions = "DELETE FROM reservation_option WHERE id_reservation IN ($reservationIds)";
                $stmtReservationOptions = $this->db->prepare($deleteReservationOptions);
                $stmtReservationOptions->execute($reservations);
            }

            // Delete all related reservations
            $deleteReservations = "DELETE FROM reservation WHERE id_terrain = :id";
            $stmtReservations = $this->db->prepare($deleteReservations);
            $stmtReservations->execute([':id' => $id]);

            // Delete all related options (posseder table)
            $deleteOptions = "DELETE FROM posseder WHERE id_terrain = :id";
            $stmtOptions = $this->db->prepare($deleteOptions);
            $stmtOptions->execute([':id' => $id]);

            // Delete all related horaires
            $deleteHoraires = "DELETE FROM horaires WHERE id_terrain = :id";
            $stmtHoraires = $this->db->prepare($deleteHoraires);
            $stmtHoraires->execute([':id' => $id]);

            // Finally delete the terrain
            $query = "DELETE FROM {$this->table} WHERE id_terrain = :id AND id_gestionnaire = :id_gestionnaire";
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                ':id' => $id,
                ':id_gestionnaire' => $this->gestionnaireId
            ]);

            // Commit transaction
            $this->db->commit();

            return $result;
        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollBack();
            throw $e;
        }
    }

    public function create($data) {
        if (!$this->gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        try {
            $this->db->beginTransaction();

            $query = "INSERT INTO {$this->table} (nom_terrain, image, localisation, type_terrain, format_terrain, prix_heure, statut, etat, id_gestionnaire) 
                     VALUES (:nom_terrain, :image, :localisation, :type_terrain, :format_terrain, :prix, 'disponible', 'acceptée', :id_gestionnaire)";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':nom_terrain' => $data['nom_terrain'],
                ':image' => $data['image'],
                ':prix' => $data['prix'],
                ':localisation' => $data['localisation'],
                ':type_terrain' => $data['type_terrain'],
                ':format_terrain' => $data['format_terrain'],
                ':id_gestionnaire' => $this->gestionnaireId
            ]);

            $terrainId = $this->db->lastInsertId();

            // Insert horaires if provided
            if (!empty($data['heure_ouverture']) && !empty($data['heure_fermeture'])) {
                $query = "INSERT INTO horaires (id_terrain, heure_ouverture, heure_fermeture) 
                          VALUES (:id_terrain, :heure_ouverture, :heure_fermeture)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':id_terrain' => $terrainId,
                    ':heure_ouverture' => $data['heure_ouverture'],
                    ':heure_fermeture' => $data['heure_fermeture']
                ]);
            }

            // Insert options if provided
            if (!empty($data['options'])) {
                foreach ($data['options'] as $optionId => $optionData) {
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

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($terrainId, $data) {
        if (!$this->gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        try {
            $this->db->beginTransaction();

            // Update terrain basic info
            $query = "UPDATE {$this->table} SET 
                     nom_terrain = :nom_terrain, 
                     localisation = :localisation, 
                     prix_heure = :prix, 
                     type_terrain = :type_terrain, 
                     format_terrain = :format_terrain, 
                     statut = :statut" . 
                     (isset($data['image']) ? ", image = :image" : "") . 
                     " WHERE id_terrain = :id_terrain AND id_gestionnaire = :id_gestionnaire";
            
            $stmt = $this->db->prepare($query);
            $params = [
                ':nom_terrain' => $data['nom_terrain'],
                ':localisation' => $data['localisation'],
                ':prix' => $data['prix'],
                ':type_terrain' => $data['type_terrain'],
                ':format_terrain' => $data['format_terrain'],
                ':statut' => $data['statut'],
                ':id_terrain' => $terrainId,
                ':id_gestionnaire' => $this->gestionnaireId
            ];

            if (isset($data['image'])) {
                $params[':image'] = $data['image'];
            }

            $stmt->execute($params);

            // Update horaires if provided
            if (!empty($data['heure_ouverture']) && !empty($data['heure_fermeture'])) {
                // Check if horaires already exist
                $stmt = $this->db->prepare("SELECT id_horaires FROM horaires WHERE id_terrain = ?");
                $stmt->execute([$terrainId]);
                $existingHoraires = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingHoraires) {
                    // Update existing horaires
                    $stmt = $this->db->prepare("UPDATE horaires SET heure_ouverture = ?, heure_fermeture = ? WHERE id_terrain = ?");
                    $stmt->execute([$data['heure_ouverture'], $data['heure_fermeture'], $terrainId]);
                } else {
                    // Insert new horaires
                    $stmt = $this->db->prepare("INSERT INTO horaires (id_terrain, heure_ouverture, heure_fermeture) VALUES (?, ?, ?)");
                    $stmt->execute([$terrainId, $data['heure_ouverture'], $data['heure_fermeture']]);
                }
            }

            // Update options - first remove all existing options for this terrain
            $stmt = $this->db->prepare("DELETE FROM posseder WHERE id_terrain = ?");
            $stmt->execute([$terrainId]);

            // Insert selected options
            if (!empty($data['options'])) {
                foreach ($data['options'] as $optionId => $optionData) {
                    if (isset($optionData['selected']) && $optionData['selected'] == '1' && !empty($optionData['prix'])) {
                        $query = "INSERT INTO posseder (id_option, id_terrain, prix_option, disponible) 
                                  VALUES (?, ?, ?, 1)";
                        $stmt = $this->db->prepare($query);
                        $stmt->execute([$optionId, $terrainId, floatval($optionData['prix'])]);
                    }
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    public function getLastInserted() {
        if (!$this->gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_gestionnaire = ? ORDER BY id_terrain DESC LIMIT 1");
        $stmt->execute([$this->gestionnaireId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getLastUpdated($id)
    {
        if (!$this->gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_gestionnaire = ? and id_terrain = ?");
        $stmt->execute([$this->gestionnaireId,$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }
    public function getLastDeleted($id)
    {
        if (!$this->gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_gestionnaire = ? and id_terrain = ?");
        $stmt->execute([$this->gestionnaireId,$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAllTerrains() {
        return $this->getAll($this->table);
    }

    public function getTerrainById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_terrain = ?");
        $stmt->execute([$id]);
        $terrain = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($terrain) {
            // Get horaires
            $stmt = $this->db->prepare("SELECT * FROM horaires WHERE id_terrain = ?");
            $stmt->execute([$id]);
            $terrain['horaires'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get options
            $stmt = $this->db->prepare("
                SELECT o.*, p.prix_option, p.disponible 
                FROM options o 
                LEFT JOIN posseder p ON o.id_option = p.id_option AND p.id_terrain = ?
                WHERE p.id_terrain IS NOT NULL
            ");
            $stmt->execute([$id]);
            $terrain['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $terrain;
    }

    public function getAvailableTerrains() {
        $stmt = $this->db->prepare("SELECT * FROM terrain WHERE statut = 'disponible' AND etat = 'acceptée'");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableTerrainsFiltered(?string $search, ?string $taille, ?string $type) {
        $conditions = ["statut = 'disponible'", "etat = 'acceptée'"];
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
    




    public function getGestionnaireTerrains() {
        if (!$this->gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_gestionnaire = ? ORDER BY id_terrain DESC");
        $stmt->execute([$this->gestionnaireId]);
        $terrains = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add options and horaires for each terrain
        foreach ($terrains as &$terrain) {
            // Get horaires
            $stmt = $this->db->prepare("SELECT * FROM horaires WHERE id_terrain = ?");
            $stmt->execute([$terrain['id_terrain']]);
            $terrain['horaires'] = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get options
            $stmt = $this->db->prepare("
                SELECT o.*, p.prix_option, p.disponible 
                FROM options o 
                LEFT JOIN posseder p ON o.id_option = p.id_option AND p.id_terrain = ?
                WHERE p.id_terrain IS NOT NULL
            ");
            $stmt->execute([$terrain['id_terrain']]);
            $terrain['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $terrains;
    }

    public function checkLastId()
    {
        $stmt = $this->db->query("SELECT MAX(id_terrain) AS lastId FROM {$this->table}");
        $lastId = $stmt->fetch(PDO::FETCH_ASSOC);

        return json_encode($lastId ?: ['lastId' => 0]);
    }
    public function getRecentTerrains($limit = 5) {
        try {
            $sql = "SELECT t.*, u.nom, u.prenom 
                FROM terrain t
                LEFT JOIN gestionnaire g ON t.id_gestionnaire = g.id
                LEFT JOIN utilisateur u ON g.id = u.id
                WHERE t.statut = 'disponible'
                ORDER BY t.id_terrain DESC
                LIMIT ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getRecentTerrains: " . $e->getMessage());
            return [];
        }
    }

// Ajoutez cette méthode dans votre classe Terrain (Terrain.php)

/**
 * Obtenir les créneaux horaires disponibles pour un terrain à une date donnée
 * @param int $terrainId ID du terrain
 * @param string $date Date au format Y-m-d (optionnel)
 * @return array Tableau avec les informations des créneaux
 */
public function getCreneauxDisponibles($terrainId, $date = null) {
    try {
        // Récupérer les horaires du terrain
        $stmt = $this->db->prepare("SELECT heure_ouverture, heure_fermeture FROM horaires WHERE id_terrain = ?");
        $stmt->execute([$terrainId]);
        $horaires = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$horaires) {
            return [
                'success' => false,
                'message' => 'Aucun horaire défini pour ce terrain'
            ];
        }
        
        $heureOuverture = $horaires['heure_ouverture'];
        $heureFermeture = $horaires['heure_fermeture'];
        
        // Générer les créneaux d'une heure
        $creneaux = [];
        $currentTime = strtotime($heureOuverture);
        $endTime = strtotime($heureFermeture);
        
        while ($currentTime < $endTime) {
            $nextTime = strtotime('+1 hour', $currentTime);
            
            // Heures formatées pour l'affichage et les calculs
            $heureDebut = date('H:i', $currentTime);
            $heureFin = date('H:i', $nextTime);
            
            // Vérifier si le créneau est disponible
            $disponible = 1;
            
            if ($date) {
                // Dans notre schéma, reservation.creneau stocke "HH:MM-HH:MM"
                $creneauStr = $heureDebut . '-' . $heureFin;
                $stmtCheck = $this->db->prepare(
                    "SELECT COUNT(*) as count 
                     FROM reservation 
                     WHERE id_terrain = ? 
                       AND date_reservation = ? 
                       AND creneau = ?
                       AND status IN ('accepté','en attente')"
                );
                $stmtCheck->execute([$terrainId, $date, $creneauStr]);
                $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                
                if ($result['count'] > 0) {
                    $disponible = 0;
                }
            }
            
            $creneaux[] = [
                'heure_ouverture' => $heureDebut,
                'heure_fermeture' => $heureFin,
                'disponible' => $disponible
            ];
            
            $currentTime = $nextTime;
        }
        
        return [
            'success' => true,
            'heure_ouverture' => date('H:i', strtotime($heureOuverture)),
            'heure_fermeture' => date('H:i', strtotime($heureFermeture)),
            'creneaux' => $creneaux
        ];
        
    } catch (Exception $e) {
        error_log("Erreur getCreneauxDisponibles: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Erreur lors de la récupération des créneaux: ' . $e->getMessage()
        ];
    }
}

/**
 * Récupérer les options disponibles pour un terrain
 * @param int $terrainId ID du terrain
 * @return array Tableau des options disponibles pour ce terrain
 */
public function getTerrainOptions($terrainId) {
    try {
        $sql = "SELECT o.*, p.prix_option, p.disponible 
                FROM options o 
                LEFT JOIN posseder p ON o.id_option = p.id_option AND p.id_terrain = :terrain_id
                WHERE p.id_terrain IS NOT NULL AND p.disponible = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':terrain_id', $terrainId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erreur getTerrainOptions: " . $e->getMessage());
        return [];
    }
}

}
