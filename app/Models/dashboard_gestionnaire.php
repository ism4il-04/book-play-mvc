<?php
// app/Models/Dashboard.php

require_once __DIR__ . '/../Core/Model.php';

class Dashboard extends Model {
    
    /**
     * Récupère toutes les statistiques pour un gestionnaire
     */
    public function getManagerStats($gestionnaireId) {
        $stats = [];
        
        // 1. Nombre de terrains créés
        $stats['terrains_count'] = $this->getTerrainsCount($gestionnaireId);
        
        // 2. Réservations en attente
        $stats['reservations_en_attente'] = $this->getReservationsEnAttente($gestionnaireId);
        
        // 3. Réservations confirmées
        $stats['reservations_confirmees'] = $this->getReservationsConfirmees($gestionnaireId);
        
        // 4. Nombre de clients uniques
        $stats['clients_count'] = $this->getClientsCount($gestionnaireId);
        
        // 5. Réservations aujourd'hui
        $stats['reservations_today'] = $this->getReservationsToday($gestionnaireId);
        
        // 6. Notifications non lues
        $stats['notifications_count'] = $this->getNotificationsCount($gestionnaireId);
        
        // 7. Activités récentes (terrains)
        $stats['recent_activities'] = $this->getRecentActivities($gestionnaireId);
        
        return $stats;
    }
    
    /**
     * Compte le nombre de terrains d'un gestionnaire
     */
    private function getTerrainsCount($gestionnaireId) {
        $sql = "SELECT COUNT(*) as count 
                FROM terrain 
                WHERE id_gestionnaire = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$gestionnaireId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Compte les réservations en attente
     */
    private function getReservationsEnAttente($gestionnaireId) {
        $sql = "SELECT COUNT(*) as count 
                FROM reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                WHERE t.id_gestionnaire = ? 
                AND r.status = 'en attente'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$gestionnaireId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Compte les réservations confirmées
     */
    private function getReservationsConfirmees($gestionnaireId) {
        $sql = "SELECT COUNT(*) as count 
                FROM reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                WHERE t.id_gestionnaire = ? 
                AND r.status = 'accepté'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$gestionnaireId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Compte le nombre de clients uniques
     */
    private function getClientsCount($gestionnaireId) {
        $sql = "SELECT COUNT(DISTINCT r.id_client) as count 
                FROM reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                WHERE t.id_gestionnaire = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$gestionnaireId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Compte les réservations d'aujourd'hui
     */
    private function getReservationsToday($gestionnaireId) {
        $sql = "SELECT COUNT(*) as count 
                FROM reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                WHERE t.id_gestionnaire = ? 
                AND DATE(r.date_reservation) = CURDATE()";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$gestionnaireId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Compte les notifications (réservations en attente)
     */
    private function getNotificationsCount($gestionnaireId) {
        return $this->getReservationsEnAttente($gestionnaireId);
    }
    
    /**
     * Récupère la liste des terrains du gestionnaire avec leurs statistiques
     */
    private function getRecentActivities($gestionnaireId, $limit = 10) {
        // Requête simplifiée pour éviter les erreurs de colonnes
        $sql = "SELECT 
                    t.*,
                    COUNT(r.id_reservation) as total_reservations,
                    SUM(CASE WHEN r.status = 'en attente' THEN 1 ELSE 0 END) as reservations_en_attente,
                    SUM(CASE WHEN r.status = 'accepté' THEN 1 ELSE 0 END) as reservations_acceptees
                FROM terrain t
                LEFT JOIN reservation r ON t.id_terrain = r.id_terrain
                WHERE t.id_gestionnaire = ?
                GROUP BY t.id_terrain
                ORDER BY t.id_terrain DESC
                LIMIT ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $gestionnaireId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $terrains = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug : afficher les données brutes
            error_log("Terrains trouvés pour gestionnaire $gestionnaireId : " . count($terrains));
            
            if (empty($terrains)) {
                error_log("Aucun terrain trouvé pour le gestionnaire $gestionnaireId");
                return [];
            }
            
            // Formatage des terrains
            $formattedTerrains = [];
            $colors = ['#3498db', '#e74c3c', '#27ae60', '#f39c12', '#9b59b6'];
            
            foreach ($terrains as $index => $terrain) {
                // Déterminer le statut
                $statut = $terrain['statut'] ?? 'disponible';
                $statusIcon = $statut === 'disponible' ? 'fas fa-check-circle' : 'fas fa-times-circle';
                $statusColor = $statut === 'disponible' ? '#27ae60' : '#e74c3c';
                
                // Construire le nom du terrain
                $nomTerrain = '';
                if (isset($terrain['type_terrain'])) {
                    $nomTerrain .= $terrain['type_terrain'];
                }
                if (isset($terrain['format_terrain'])) {
                    $nomTerrain .= ' ' . $terrain['format_terrain'];
                }
                if (empty($nomTerrain)) {
                    $nomTerrain = 'Terrain #' . $terrain['id_terrain'];
                }
                
                // Localisation
                $localisation = $terrain['localisation'] ?? $terrain['ville'] ?? 'Non spécifié';
                
                // Prix
                $prix = isset($terrain['prix_heure']) ? number_format($terrain['prix_heure'], 2) . ' DH/h' : 'N/A';
                
                // Horaires
                $horaires = 'N/A';
                if (isset($terrain['heure_ouverture']) && isset($terrain['heure_fermeture'])) {
                    $horaires = substr($terrain['heure_ouverture'], 0, 5) . ' - ' . substr($terrain['heure_fermeture'], 0, 5);
                }
                
                $formattedTerrains[] = [
                    'id' => $terrain['id_terrain'],
                    'nom' => trim($nomTerrain),
                    'localisation' => $localisation,
                    'statut' => ucfirst($statut),
                    'prix' => $prix,
                    'horaires' => $horaires,
                    'total_reservations' => $terrain['total_reservations'] ?? 0,
                    'reservations_en_attente' => $terrain['reservations_en_attente'] ?? 0,
                    'reservations_acceptees' => $terrain['reservations_acceptees'] ?? 0,
                    'status_icon' => $statusIcon,
                    'terrain_icon' => 'fas fa-futbol',
                    'color' => $colors[$index % count($colors)],
                    'status_color' => $statusColor
                ];
            }

            error_log("Terrains formatés : " . count($formattedTerrains));
            return $formattedTerrains;
            
        } catch (PDOException $e) {
            error_log("Erreur getRecentActivities: " . $e->getMessage());
            error_log("SQL Error: " . print_r($stmt->errorInfo(), true));
            return [];
        }
    }
}