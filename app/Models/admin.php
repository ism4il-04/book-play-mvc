<?php

require_once __DIR__ . '/../Core/Model.php';

class Admin extends Model {
    protected $table = 'administrateur';

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

    // Gestionnaires actifs (acceptés)
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

    // Demandes en attente
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

    // Demandes refusées
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
    public function getAllGestionnairesAcceptes() {
        try {
            $sql = "
                SELECT 
                    g.id,
                    g.RIB,
                    g.status,
                    u.nom,
                    u.prenom,
                    u.email,
                    u.num_tel
                FROM gestionnaire g
                INNER JOIN utilisateur u ON g.id = u.id
                WHERE g.status = 'accepté'
                ORDER BY u.nom, u.prenom
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $results;
        } catch (PDOException $e) {
            error_log("Erreur getAllGestionnairesAcceptes: " . $e->getMessage());
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

    // Mettre à jour le statut d'un gestionnaire
    public function updateGestionnaireStatus($id, $status) {
        try {
            $stmt = $this->db->prepare("
                UPDATE gestionnaire 
                SET status = ?, date_validation = CURDATE() 
                WHERE id = ?
            ");
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("Erreur updateGestionnaireStatus: " . $e->getMessage());
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