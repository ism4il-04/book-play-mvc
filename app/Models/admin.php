<?php

require_once __DIR__ . '/../Core/Model.php';

class Admin extends Model {
    //protected $table = 'administrateur';

    // Total gestionnaires
    private function getTotal() {
        try {
            $sql = "SELECT COUNT(*) as total FROM gestionnaire";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Erreur getTotal: " . $e->getMessage());
            return 0;
        }
    }

    // nbr Gestionnaires acceptés
    private function getActifs() {
        try {
            $sql = "SELECT COUNT(*) as actif FROM gestionnaire WHERE status = 'accepté'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['actif'] ?? 0);
        } catch (PDOException $e) {
            error_log("Erreur getActifs: " . $e->getMessage());
            return 0;
        }
    }

    // nbr Demandes en attente
    private function getDemandesEnAttente() {
        try {
            $sql = "SELECT COUNT(*) as attente FROM gestionnaire WHERE status = 'en attente'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['attente'] ?? 0);
        } catch (PDOException $e) {
            error_log("Erreur getDemandesEnAttente: " . $e->getMessage());
            return 0;
        }
    }

    //nbr Demandes refusées
    private function getDemandesRefusees() {
        try {
            $sql = "SELECT COUNT(*) as refuse FROM gestionnaire WHERE status = 'refusé'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['refuse'] ?? 0);
        } catch (PDOException $e) {
            error_log("Erreur getDemandesRefusees: " . $e->getMessage());
            return 0;
        }
    }

    // Récupérer tous les gestionnaires acceptés
    public function getAllGestionnaires() {
        try {
            $sql = "
                    SELECT 
                        g.id,
                        g.RIB,
                        g.status AS statut_gestionnaire,
                        g.date_demande,
                        u.nom,
                        u.prenom,
                        u.email,
                        u.num_tel
                    FROM gestionnaire g
                    INNER JOIN utilisateur u ON g.id = u.id
                    WHERE g.status = 'accepté'
                    ORDER BY g.date_demande DESC
                ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $results;
        } catch (PDOException $e) {
            error_log("Erreur getAllGestionnaires: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer tous les gestionnaires en attente
    public function getAllGestionnairesEnAttente() {
        try {
             $sql = "
                    SELECT 
                        g.id,
                        g.RIB,
                        g.status AS statut_gestionnaire,
                        g.date_demande,
                        u.nom,
                        u.prenom,
                        u.email,
                        u.num_tel,

                        -- Informations terrain
                        t.id_terrain,
                        t.nom_terrain,
                        t.statut AS statut_terrain,
                        t.etat AS etat_demande_terrain,
                        t.type_terrain,
                        t.format_terrain,
                        t.prix_heure,
                        t.localisation,
                        t.image,
                        t.justificatif

                    FROM gestionnaire g

                    INNER JOIN utilisateur u 
                        ON g.id = u.id

                    LEFT JOIN terrain t
                        ON t.id_gestionnaire = g.id

                    WHERE g.status = 'en attente'
                    ORDER BY g.date_demande DESC
                ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $e) {
            error_log("Erreur getAllGestionnairesEnAttente: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer tous les demandes gestionnaires aceeptés
    public function getAllGestionnairesAccepte() {
        try {
            $sql = "
                    SELECT 
                        g.id,
                        g.RIB,
                        g.status AS statut_gestionnaire,
                        g.date_demande,
                        u.nom,
                        u.prenom,
                        u.email,
                        u.num_tel,

                        -- Informations terrain
                        t.id_terrain,
                        t.nom_terrain,
                        t.statut AS statut_terrain,
                        t.etat AS etat_demande_terrain,
                        t.type_terrain,
                        t.format_terrain,
                        t.prix_heure,
                        t.localisation,
                        t.image,
                        t.justificatif

                    FROM gestionnaire g

                    INNER JOIN utilisateur u 
                        ON g.id = u.id

                    LEFT JOIN terrain t
                        ON t.id_gestionnaire = g.id

                    WHERE g.status = 'accepté'
                    ORDER BY g.date_demande DESC
                ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $e) {
            error_log("Erreur getAllGestionnairesAccepte: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer tous les demandes gestionnaires refusés
    public function getAllGestionnairesRefuse() {
        try {
            $sql = "
                    SELECT 
                        g.id,
                        g.RIB,
                        g.status AS statut_gestionnaire,
                        g.date_demande,
                        u.nom,
                        u.prenom,
                        u.email,
                        u.num_tel,

                        -- Informations terrain
                        t.id_terrain,
                        t.nom_terrain,
                        t.statut AS statut_terrain,
                        t.etat AS etat_demande_terrain,
                        t.type_terrain,
                        t.format_terrain,
                        t.prix_heure,
                        t.localisation,
                        t.image,
                        t.justificatif

                    FROM gestionnaire g

                    INNER JOIN utilisateur u 
                        ON g.id = u.id

                    LEFT JOIN terrain t
                        ON t.id_gestionnaire = g.id

                    WHERE g.status = 'refusé'
                    ORDER BY g.date_demande DESC
                ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $e) {
            error_log("Erreur getAllGestionnairesRefuse: " . $e->getMessage());
            return [];
        }
    }
    

    // Récupérer un gestionnaire par son ID
    public function getGestionnaireById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    g.id,
                    g.RIB,
                    g.status,
                    g.justificatif,
                    g.date_validation,
                    g.date_demande,
                    u.nom,
                    u.prenom,
                    u.email,
                    u.num_tel
                FROM gestionnaire g
                INNER JOIN utilisateur u ON g.id = u.id
                WHERE g.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getGestionnaireById: " . $e->getMessage());
            return null;
        }
    }

    // Mettre à jour le statut d'un gestionnaire et terrains associés a la demande pour devenir un gestionnaire
    public function updateGestionnaireStatus($id, $status, $etatterrain, $idTerrain = null) {
        try {
            // Mettre à jour le statut du gestionnaire
            $sqlGestionnaire = "
                UPDATE gestionnaire 
                SET status = ?, date_validation = CURDATE() 
                WHERE id = ?
            ";
            $stmtGestionnaire = $this->db->prepare($sqlGestionnaire);
            $stmtGestionnaire->execute([$status, $id]);

            // Mettre à jour l'état du terrain spécifique ou tous les terrains du gestionnaire
            if ($idTerrain !== null) {
                // Mettre à jour uniquement le terrain spécifique
                $sqlTerrain = "
                    UPDATE terrain
                    SET etat = ?
                    WHERE id_terrain = ? AND id_gestionnaire = ?
                ";
                $stmtTerrain = $this->db->prepare($sqlTerrain);
                $stmtTerrain->execute([$etatterrain, $idTerrain, $id]);
            } else {
                // Mettre à jour tous les terrains du gestionnaire
                $sqlTerrain = "
                    UPDATE terrain
                    SET etat = ?
                    WHERE id_gestionnaire = ?
                ";
                $stmtTerrain = $this->db->prepare($sqlTerrain);
                $stmtTerrain->execute([$etatterrain, $id]);
            }

            return true;
        } catch (PDOException $e) {
            error_log("Erreur updateGestionnaireStatus: " . $e->getMessage());
            return false;
        }
    }

    // Récupérer les détails complets d'un gestionnaire par son ID
    public function getGestionnaireDetailsById($id) {
        try {
            $sql = "
                SELECT 
                    g.id, 
                    u.prenom, 
                    u.nom, 
                    u.email, 
                    u.num_tel, 
                    g.RIB,
                    g.status, 
                    g.date_demande, 

                    -- Informations du terrain
                    t.id_terrain,
                    t.nom_terrain, 
                    t.localisation, 
                    t.format_terrain,
                    t.type_terrain,
                    t.prix_heure,
                    t.etat AS etat_terrain,
                    t.statut AS statut_terrain,
                    t.justificatif
                FROM gestionnaire g
                INNER JOIN utilisateur u ON g.id = u.id
                LEFT JOIN terrain t ON g.id = t.id_gestionnaire 
                WHERE g.id = ?
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $gestionnaire = $stmt->fetch(PDO::FETCH_ASSOC);
            return $gestionnaire;
        } catch (PDOException $e) {
            error_log("Erreur getGestionnaireDetailsById: " . $e->getMessage());
            return false;
        }
    }

    

    // Récupérer les statistiques globales
    public function getStats() {
        return [
            'total' => $this->getTotal(),
            'actifs' => $this->getActifs(),
            'en_attente' => $this->getDemandesEnAttente(),
            'refuses' => $this->getDemandesRefusees()
        ];
    }
}