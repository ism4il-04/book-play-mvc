<?php
// app/Models/AutoNewsletter.php

require_once __DIR__ . '/../Core/Model.php';

class AutoNewsletter extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->createTables();
        $this->addNewsletterColumnIfNotExists();
    }

    /**
     * Ajouter la colonne newsletter_subscribed si elle n'existe pas
     */
    private function addNewsletterColumnIfNotExists() {
        try {
            // Vérifier si la colonne existe
            $sql = "SHOW COLUMNS FROM utilisateur LIKE 'newsletter_subscribed'";
            $stmt = $this->db->query($sql);
            
            if ($stmt->rowCount() == 0) {
                // Ajouter les colonnes
                $sql = "ALTER TABLE utilisateur 
                        ADD COLUMN newsletter_subscribed TINYINT(1) DEFAULT 0,
                        ADD COLUMN newsletter_subscribed_at DATETIME NULL";
                $this->db->exec($sql);
                error_log("✅ Colonnes newsletter ajoutées avec succès");
            }
        } catch (PDOException $e) {
            error_log("Erreur ajout colonnes newsletter: " . $e->getMessage());
        }
    }

    /**
     * Créer les tables nécessaires
     */
    private function createTables() {
        try {
            // Table de configuration
            $sql = "CREATE TABLE IF NOT EXISTS auto_newsletter_config (
                id INT AUTO_INCREMENT PRIMARY KEY,
                enabled TINYINT(1) DEFAULT 1,
                frequency VARCHAR(20) DEFAULT 'weekly',
                day_of_week VARCHAR(20) DEFAULT 'monday',
                send_time TIME DEFAULT '09:00:00',
                include_new_terrains TINYINT(1) DEFAULT 1,
                include_tournaments TINYINT(1) DEFAULT 1,
                include_promotions TINYINT(1) DEFAULT 1,
                include_statistics TINYINT(1) DEFAULT 1,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $this->db->exec($sql);

            // Insérer configuration par défaut si vide
            $check = $this->db->query("SELECT COUNT(*) as count FROM auto_newsletter_config")->fetch();
            if ($check['count'] == 0) {
                $this->db->exec("INSERT INTO auto_newsletter_config (id) VALUES (1)");
            }

            // Table d'historique
            $sql = "CREATE TABLE IF NOT EXISTS auto_newsletter_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                subject VARCHAR(255) NOT NULL,
                sent_count INT DEFAULT 0,
                failed_count INT DEFAULT 0,
                sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_sent_at (sent_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $this->db->exec($sql);

        } catch (PDOException $e) {
            error_log("Erreur création tables: " . $e->getMessage());
        }
    }

    /**
     * Récupérer la configuration
     */
    public function getConfig() {
        try {
            $sql = "SELECT * FROM auto_newsletter_config WHERE id = 1";
            $stmt = $this->db->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getConfig: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Sauvegarder la configuration
     */
    public function saveConfig($config) {
        try {
            $sql = "UPDATE auto_newsletter_config SET
                    enabled = :enabled,
                    frequency = :frequency,
                    day_of_week = :day_of_week,
                    send_time = :send_time,
                    include_new_terrains = :include_new_terrains,
                    include_tournaments = :include_tournaments,
                    include_promotions = :include_promotions,
                    include_statistics = :include_statistics
                    WHERE id = 1";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($config);
        } catch (PDOException $e) {
            error_log("Erreur saveConfig: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer UNIQUEMENT les abonnés à la newsletter
     */
    public function getSubscribers() {
        try {
            $sql = "SELECT id, nom, prenom, email 
                    FROM utilisateur 
                    WHERE email IS NOT NULL 
                    AND email != ''
                    AND newsletter_subscribed = 1
                    ORDER BY id DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getSubscribers: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Désabonner par email
     */
    public function unsubscribeByEmail($email) {
        try {
            $sql = "UPDATE utilisateur 
                    SET newsletter_subscribed = 0,
                        newsletter_subscribed_at = NULL
                    WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$email]);
        } catch (PDOException $e) {
            error_log("Erreur unsubscribeByEmail: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enregistrer un envoi dans l'historique
     */
    public function logSend($subject, $sent, $failed) {
        try {
            $sql = "INSERT INTO auto_newsletter_history (subject, sent_count, failed_count) 
                    VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$subject, $sent, $failed]);
        } catch (PDOException $e) {
            error_log("Erreur logSend: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer l'historique des envois
     */
    public function getHistory($limit = 10) {
        try {
            $sql = "SELECT * FROM auto_newsletter_history 
                    ORDER BY sent_at DESC LIMIT ?";
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
     * Récupérer les statistiques
     */
    public function getStats() {
        try {
            // Total d'abonnés (UNIQUEMENT ceux abonnés)
            $sql = "SELECT COUNT(*) as total FROM utilisateur 
                    WHERE email IS NOT NULL 
                    AND email != '' 
                    AND newsletter_subscribed = 1";
            $stmt = $this->db->query($sql);
            $totalSubscribers = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Total envoyés
            $sql = "SELECT SUM(sent_count) as total FROM auto_newsletter_history";
            $stmt = $this->db->query($sql);
            $totalSent = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Dernier envoi
            $sql = "SELECT sent_at FROM auto_newsletter_history ORDER BY sent_at DESC LIMIT 1";
            $stmt = $this->db->query($sql);
            $lastSent = $stmt->fetch(PDO::FETCH_ASSOC)['sent_at'] ?? null;

            // Prochain envoi (calculé selon la config)
            $config = $this->getConfig();
            $nextSend = $this->calculateNextSend($config);

            return [
                'total_subscribers' => $totalSubscribers,
                'total_sent' => $totalSent,
                'last_sent' => $lastSent,
                'next_send' => $nextSend
            ];
        } catch (PDOException $e) {
            error_log("Erreur getStats: " . $e->getMessage());
            return [
                'total_subscribers' => 0,
                'total_sent' => 0,
                'last_sent' => null,
                'next_send' => null
            ];
        }
    }

    /**
     * Calculer la date du prochain envoi
     */
    private function calculateNextSend($config) {
        if (!$config || !$config['enabled']) {
            return null;
        }

        $dayMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 0
        ];

        $targetDay = $dayMap[$config['day_of_week']] ?? 1;
        $currentDay = date('w');
        
        $daysUntil = ($targetDay - $currentDay + 7) % 7;
        if ($daysUntil == 0) {
            $daysUntil = 7; // Semaine prochaine
        }

        $nextDate = date('Y-m-d', strtotime("+$daysUntil days"));
        return $nextDate . ' ' . $config['send_time'];
    }
}