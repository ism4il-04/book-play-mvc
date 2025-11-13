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
                        g.date_validation,
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
            error_log("updateGestionnaireStatus appelée avec: ID=$id, status=$status, etatterrain=$etatterrain, idTerrain=$idTerrain");
            
            // Vérifier d'abord si le gestionnaire existe
            $checkSql = "SELECT COUNT(*) as count FROM gestionnaire WHERE id = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$id]);
            $exists = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($exists['count'] == 0) {
                error_log("Gestionnaire avec ID $id n'existe pas");
                return false;
            }
            
            // Vérifier d'abord le statut actuel du gestionnaire
            $checkGestSql = "SELECT status FROM gestionnaire WHERE id = ?";
            $checkGestStmt = $this->db->prepare($checkGestSql);
            $checkGestStmt->execute([$id]);
            $gestionnaireStatus = $checkGestStmt->fetch(PDO::FETCH_ASSOC);
            
            $gestionnaireDejaAccepte = ($gestionnaireStatus && $gestionnaireStatus['status'] === 'accepté');
            
            // Mettre à jour le statut du gestionnaire seulement s'il n'est pas déjà accepté
            if (!$gestionnaireDejaAccepte) {
                $sqlGestionnaire = "
                    UPDATE gestionnaire 
                    SET status = ?, date_validation = NOW() 
                    WHERE id = ?
                ";
                $stmtGestionnaire = $this->db->prepare($sqlGestionnaire);
                $resultGest = $stmtGestionnaire->execute([$status, $id]);
                
                if (!$resultGest) {
                    error_log("Échec de la mise à jour du gestionnaire");
                    return false;
                }
                
                $rowsAffectedGest = $stmtGestionnaire->rowCount();
                error_log("Premier terrain accepté - Gestionnaire $id mis à jour: $rowsAffectedGest lignes affectées");
            } else {
                error_log("Terrain supplémentaire accepté - Gestionnaire $id déjà accepté, pas de mise à jour");
            }

            // Mettre à jour l'état du terrain spécifique
            if ($idTerrain !== null) {
                
                // Mettre à jour le terrain spécifique
                $sqlTerrain = "
                    UPDATE terrain
                    SET etat = ?
                    WHERE id_terrain = ? AND id_gestionnaire = ?
                ";
                $stmtTerrain = $this->db->prepare($sqlTerrain);
                $resultTerrain = $stmtTerrain->execute([$etatterrain, $idTerrain, $id]);
                $rowsAffectedTerrain = $stmtTerrain->rowCount();
                error_log("Terrain spécifique $idTerrain mis à jour: $rowsAffectedTerrain lignes affectées");
            } else {
                // Mettre à jour tous les terrains du gestionnaire (cas rare)
                $sqlTerrain = "
                    UPDATE terrain
                    SET etat = ?
                    WHERE id_gestionnaire = ?
                ";
                $stmtTerrain = $this->db->prepare($sqlTerrain);
                $resultTerrain = $stmtTerrain->execute([$etatterrain, $id]);
                $rowsAffectedTerrain = $stmtTerrain->rowCount();
                error_log("Tous les terrains mis à jour: $rowsAffectedTerrain lignes affectées");
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Erreur updateGestionnaireStatus: " . $e->getMessage());
            error_log("SQL Error Info: " . print_r($e->errorInfo, true));
            return false;
        }
    }

    // Récupérer les détails complets d'un gestionnaire par son ID
    public function getGestionnaireDetailsById($id) {
        try {
            $sql = "
                SELECT 
                    g.id AS id_gestionnaire,
                    u.prenom, 
                    u.nom, 
                    u.email, 
                    u.num_tel, 
                    g.RIB,
                    g.status, 
                    g.date_demande, 
                    t.id_terrain,
                    t.nom_terrain, 
                    t.localisation, 
                    t.format_terrain,
                    t.type_terrain,
                    t.prix_heure,
                    t.etat AS etat_terrain,
                    t.statut AS statut_terrain,
                    t.justificatif,
                    h.heure_ouverture,
                    h.heure_fermeture
                FROM gestionnaire g
                INNER JOIN utilisateur u ON g.id = u.id
                LEFT JOIN terrain t ON g.id = t.id_gestionnaire
                LEFT JOIN horaires h ON t.id_terrain = h.id_terrain
                WHERE g.id = ?
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $gestionnaire = $stmt->fetch(PDO::FETCH_ASSOC);

            // --- Si on a trouvé un terrain, récupérer ses options ---
            if ($gestionnaire && !empty($gestionnaire['id_terrain'])) {
                $sqlOptions = "
                    SELECT 
                        o.id_option,
                        o.nom_option,
                        o.description AS description_option,
                        p.prix_option,
                        p.disponible
                    FROM posseder p
                    INNER JOIN options o ON p.id_option = o.id_option
                    WHERE p.id_terrain = ?
                ";

                $stmtOptions = $this->db->prepare($sqlOptions);
                $stmtOptions->execute([$gestionnaire['id_terrain']]);
                $options = $stmtOptions->fetchAll(PDO::FETCH_ASSOC);

                // Ajouter les options au tableau final
                $gestionnaire['options'] = $options;
            } else {
                $gestionnaire['options'] = [];
            }
            return $gestionnaire;
        } catch (PDOException $e) {
            error_log("Erreur getGestionnaireDetailsById: " . $e->getMessage());
            return false;
        }
    }

    // Supprimer un gestionnaire et toutes ses données associées
    public function supprimerGestionnaire($id) {
        try {
            // Commencer une transaction
            $this->db->beginTransaction();

            //1.Récupérer l'ID du terrain associé
            $sqlTerrain = "SELECT id_terrain FROM terrain WHERE id_gestionnaire = ?";
            $stmtTerrain = $this->db->prepare($sqlTerrain);
            $stmtTerrain->execute([$id]);
            $terrain = $stmtTerrain->fetch(PDO::FETCH_ASSOC);

            if ($terrain) {
                $idTerrain = $terrain['id_terrain'];

                //2.Récupérer toutes les réservations liées à ce terrain
                $sqlRes = "SELECT id_reservation FROM reservation WHERE id_terrain = ?";
                $stmtRes = $this->db->prepare($sqlRes);
                $stmtRes->execute([$idTerrain]);
                $reservations = $stmtRes->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($reservations)) {
                    //3.Supprimer les options associées à ces réservations
                    $sqlResOptions = "DELETE FROM reservation_option WHERE id_reservation IN (" . str_repeat('?,', count($reservations) - 1) . "?)";
                    $stmtResOptions = $this->db->prepare($sqlResOptions);
                    $stmtResOptions->execute($reservations);

                    //4.Supprimer les réservations elles-mêmes
                    $sqlReservations = "DELETE FROM reservation WHERE id_terrain = ?";
                    $stmtReservations = $this->db->prepare($sqlReservations);
                    $stmtReservations->execute([$idTerrain]);
                }

                //5.Supprimer les relations posseder (terrain-options)
                $sqlPosseder = "DELETE FROM posseder WHERE id_terrain = ?";
                $stmtPosseder = $this->db->prepare($sqlPosseder);
                $stmtPosseder->execute([$idTerrain]);

                //6.Supprimer les horaires du terrain
                $sqlHoraires = "DELETE FROM horaires WHERE id_terrain = ?";
                $stmtHoraires = $this->db->prepare($sqlHoraires);
                $stmtHoraires->execute([$idTerrain]);

                //7.Supprimer le terrain
                $sqlDeleteTerrain = "DELETE FROM terrain WHERE id_gestionnaire = ?";
                $stmtDeleteTerrain = $this->db->prepare($sqlDeleteTerrain);
                $stmtDeleteTerrain->execute([$id]);
            }

            //8.Supprimer le gestionnaire
            $sqlDeleteGestionnaire = "DELETE FROM gestionnaire WHERE id = ?";
            $stmtDeleteGestionnaire = $this->db->prepare($sqlDeleteGestionnaire);
            $stmtDeleteGestionnaire->execute([$id]);

            // Valider la transaction
            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            //Annuler en cas d’erreur
            $this->db->rollBack();
            error_log("Erreur supprimerGestionnaire: " . $e->getMessage());
            return false;
        }
    }


    // Récupérer l'ID du dernier gestionnaire accepté (pour le système temps réel)
    public function getLastAcceptedGestionnaireId() {
        try {
            $sql = "SELECT MAX(g.id) as last_id FROM gestionnaire g WHERE g.status = 'accepté'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['last_id'] ?? 0);
        } catch (PDOException $e) {
            error_log("Erreur getLastAcceptedGestionnaireId: " . $e->getMessage());
            return 0;
        }
    }

    // Récupérer tous les gestionnaires acceptés (pour comparaison temps réel)
    public function getRecentlyAcceptedGestionnaires($minutes = 5) {
        try {
            // On récupère TOUS les gestionnaires acceptés
            // La logique JavaScript se chargera de détecter les nouveaux
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
                    t.nom_terrain,
                    t.id_terrain,
                    t.statut as statut_terrain
                FROM gestionnaire g
                INNER JOIN utilisateur u ON g.id = u.id
                LEFT JOIN terrain t ON g.id = t.id_gestionnaire
                WHERE g.status = 'accepté'
                ORDER BY g.date_demande DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $results;
        } catch (PDOException $e) {
            error_log("Erreur getRecentlyAcceptedGestionnaires: " . $e->getMessage());
            return [];
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