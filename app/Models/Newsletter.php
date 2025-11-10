<?php
// app/Models/Newsletter.php

require_once __DIR__ . '/../Core/Model.php';

class Newsletter extends Model {
    
    /**
     * Récupérer les statistiques de la newsletter
     */
    public function getStats() {
        try {
            // Total d'abonnés
            $sql = "SELECT COUNT(*) as total FROM utilisateur WHERE email IS NOT NULL AND email != ''";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $totalSubscribers = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Clients
            $sql = "SELECT COUNT(*) as total FROM client c 
                    INNER JOIN utilisateur u ON c.id = u.id 
                    WHERE u.email IS NOT NULL AND u.email != ''";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $totalClients = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Gestionnaires
            $sql = "SELECT COUNT(*) as total FROM gestionnaire g 
                    INNER JOIN utilisateur u ON g.id = u.id 
                    WHERE u.email IS NOT NULL AND u.email != '' AND g.status = 'accepté'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $totalManagers = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Newsletters envoyées
            $sql = "SELECT COUNT(*) as total FROM newsletter_history";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $totalSent = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            return [
                'total_subscribers' => $totalSubscribers,
                'total_clients' => $totalClients,
                'total_managers' => $totalManagers,
                'total_sent' => $totalSent
            ];
        } catch (PDOException $e) {
            error_log("Erreur getStats: " . $e->getMessage());
            return [
                'total_subscribers' => 0,
                'total_clients' => 0,
                'total_managers' => 0,
                'total_sent' => 0
            ];
        }
    }

    /**
     * Récupérer les destinataires selon le type
     */
    public function getRecipients($type = 'all') {
        try {
            switch ($type) {
                case 'clients':
                    $sql = "SELECT u.id, u.nom, u.prenom, u.email 
                            FROM client c 
                            INNER JOIN utilisateur u ON c.id = u.id 
                            WHERE u.email IS NOT NULL AND u.email != ''";
                    break;
                
                case 'managers':
                    $sql = "SELECT u.id, u.nom, u.prenom, u.email 
                            FROM gestionnaire g 
                            INNER JOIN utilisateur u ON g.id = u.id 
                            WHERE u.email IS NOT NULL AND u.email != '' AND g.status = 'accepté'";
                    break;
                
                case 'all':
                default:
                    $sql = "SELECT id, nom, prenom, email 
                            FROM utilisateur 
                            WHERE email IS NOT NULL AND email != ''";
                    break;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getRecipients: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Enregistrer l'historique d'envoi
     */
    public function saveHistory($subject, $message, $recipientType, $sent, $failed) {
        try {
            // Créer la table si elle n'existe pas
            $this->createHistoryTableIfNotExists();

            $sql = "INSERT INTO newsletter_history (subject, message, recipient_type, sent_count, failed_count, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$subject, $message, $recipientType, $sent, $failed]);
        } catch (PDOException $e) {
            error_log("Erreur saveHistory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer l'historique des newsletters
     */
    public function getHistory($limit = 10) {
        try {
            $this->createHistoryTableIfNotExists();

            $sql = "SELECT * FROM newsletter_history ORDER BY created_at DESC LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getHistory: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Créer la table d'historique si elle n'existe pas
     */
    private function createHistoryTableIfNotExists() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS newsletter_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                subject VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                recipient_type VARCHAR(50) NOT NULL,
                sent_count INT DEFAULT 0,
                failed_count INT DEFAULT 0,
                created_at DATETIME NOT NULL,
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->db->exec($sql);
        } catch (PDOException $e) {
            error_log("Erreur createHistoryTableIfNotExists: " . $e->getMessage());
        }
    }
}