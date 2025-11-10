<?php

// app/Models/Dashboard_gestionnaire.php

require_once __DIR__ . '/../Core/Model.php';

class Dashboard extends Model {
    /**
     * Récupère toutes les statistiques pour un gestionnaire.
     *
     * @param mixed $userId
     */
    public function getManagerStats($userId) {
        // CORRECTION : Récupérer d'abord l'id_gestionnaire depuis l'id utilisateur
        $gestionnaireId = $this->getGestionnaireIdFromUserId($userId);

        if (!$gestionnaireId) {
            error_log("Aucun gestionnaire trouvé pour l'utilisateur ID: {$userId}");

            return $this->getEmptyStats();
        }

        error_log("ID Gestionnaire trouvé: {$gestionnaireId} pour utilisateur: {$userId}");

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
     * Récupère les statistiques de revenus.
     *
     * @param mixed $gestionnaireId
     */
    public function getRevenusStats($gestionnaireId) {
        $sql = "SELECT 
                    SUM(f.TTC) as total_revenus,
                    COUNT(f.num_facture) as nombre_factures,
                    AVG(f.TTC) as revenu_moyen
                FROM facture f
                INNER JOIN reservation r ON f.id_reservation = r.id_reservation
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                WHERE t.id_gestionnaire = ?
                AND r.status = 'accepté'";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$gestionnaireId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'total_revenus' => $result['total_revenus'] ?? 0,
                'nombre_factures' => $result['nombre_factures'] ?? 0,
                'revenu_moyen' => $result['revenu_moyen'] ?? 0,
            ];
        } catch (PDOException $e) {
            error_log('Erreur getRevenusStats: ' . $e->getMessage());

            return [
                'total_revenus' => 0,
                'nombre_factures' => 0,
                'revenu_moyen' => 0,
            ];
        }
    }

    /**
     * Récupère les réservations récentes.
     *
     * @param mixed $gestionnaireId
     * @param mixed $limit
     */
    public function getRecentReservations($gestionnaireId, $limit = 10) {
        $sql = 'SELECT 
                    r.*,
                    t.nom_terrain,
                    t.type_terrain,
                    t.format_terrain,
                    u.prenom,
                    u.nom,
                    u.email
                FROM reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                LEFT JOIN client c ON r.id_client = c.id
                LEFT JOIN utilisateur u ON c.id = u.id
                WHERE t.id_gestionnaire = ?
                ORDER BY r.date_reservation DESC, r.heure_depart DESC
                LIMIT ?';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $gestionnaireId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur getRecentReservations: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Récupère les données pour les graphiques.
     *
     * @param mixed $gestionnaireId
     * @param mixed $period
     */
    public function getChartData($gestionnaireId, $period = '7days') {
        $dateCondition = '';

        switch ($period) {
            case '7days':
                $dateCondition = 'AND r.date_reservation >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
                break;
            case '30days':
                $dateCondition = 'AND r.date_reservation >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
                break;
            case '3months':
                $dateCondition = 'AND r.date_reservation >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)';
                break;
            case '1year':
                $dateCondition = 'AND r.date_reservation >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)';
                break;
        }

        $sql = "SELECT 
                    DATE(r.date_reservation) as date,
                    COUNT(*) as nombre_reservations,
                    SUM(CASE WHEN r.status = 'accepté' THEN 1 ELSE 0 END) as acceptees,
                    SUM(CASE WHEN r.status = 'en attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(CASE WHEN r.status = 'refusé' THEN 1 ELSE 0 END) as refusees
                FROM reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                WHERE t.id_gestionnaire = ?
                {$dateCondition}
                GROUP BY DATE(r.date_reservation)
                ORDER BY date ASC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$gestionnaireId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur getChartData: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Récupère le top 5 des terrains les plus réservés.
     *
     * @param mixed $gestionnaireId
     * @param mixed $limit
     */
    public function getTopTerrains($gestionnaireId, $limit = 5) {
        $sql = "SELECT 
                    t.id_terrain,
                    t.nom_terrain,
                    t.type_terrain,
                    t.format_terrain,
                    t.image,
                    COUNT(r.id_reservation) as nombre_reservations,
                    SUM(CASE WHEN r.status = 'accepté' THEN 1 ELSE 0 END) as reservations_acceptees
                FROM terrain t
                LEFT JOIN reservation r ON t.id_terrain = r.id_terrain
                WHERE t.id_gestionnaire = ?
                GROUP BY t.id_terrain
                ORDER BY nombre_reservations DESC
                LIMIT ?";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $gestionnaireId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur getTopTerrains: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * NOUVELLE MÉTHODE : Récupère l'ID gestionnaire depuis l'ID utilisateur.
     *
     * @param mixed $userId
     */
    private function getGestionnaireIdFromUserId($userId) {
        $sql = 'SELECT id FROM gestionnaire WHERE id = ?';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['id'] ?? null;
        } catch (PDOException $e) {
            error_log('Erreur getGestionnaireIdFromUserId: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Retourne des statistiques vides en cas d'erreur.
     */
    private function getEmptyStats() {
        return [
            'terrains_count' => 0,
            'reservations_en_attente' => 0,
            'reservations_confirmees' => 0,
            'clients_count' => 0,
            'reservations_today' => 0,
            'notifications_count' => 0,
            'recent_activities' => [],
        ];
    }

    /**
     * Compte le nombre de terrains d'un gestionnaire.
     *
     * @param mixed $gestionnaireId
     */
    private function getTerrainsCount($gestionnaireId) {
        $sql = 'SELECT COUNT(*) as count 
                FROM terrain 
                WHERE id_gestionnaire = ?';

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$gestionnaireId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $count = $result['count'] ?? 0;
            error_log("Nombre de terrains pour gestionnaire {$gestionnaireId} : {$count}");

            return $count;
        } catch (PDOException $e) {
            error_log('Erreur getTerrainsCount: ' . $e->getMessage());

            return 0;
        }
    }

    /**
     * Compte les réservations en attente.
     *
     * @param mixed $gestionnaireId
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
     * Compte les réservations confirmées.
     *
     * @param mixed $gestionnaireId
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
     * Compte le nombre de clients uniques.
     *
     * @param mixed $gestionnaireId
     */
    private function getClientsCount($gestionnaireId) {
        $sql = 'SELECT COUNT(DISTINCT r.id_client) as count 
                FROM reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                WHERE t.id_gestionnaire = ?';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$gestionnaireId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] ?? 0;
    }

    /**
     * Compte les réservations d'aujourd'hui.
     *
     * @param mixed $gestionnaireId
     */
    private function getReservationsToday($gestionnaireId) {
        $sql = 'SELECT COUNT(*) as count 
                FROM reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                WHERE t.id_gestionnaire = ? 
                AND DATE(r.date_reservation) = CURDATE()';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$gestionnaireId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] ?? 0;
    }

    /**
     * Compte les notifications (réservations en attente).
     *
     * @param mixed $gestionnaireId
     */
    private function getNotificationsCount($gestionnaireId) {
        return $this->getReservationsEnAttente($gestionnaireId);
    }

    /**
     * Récupère la liste des terrains du gestionnaire avec leurs statistiques.
     *
     * @param mixed $gestionnaireId
     * @param mixed $limit
     */
    private function getRecentActivities($gestionnaireId, $limit = 10) {
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

            error_log("Terrains trouvés pour gestionnaire {$gestionnaireId} : " . count($terrains));

            if (empty($terrains)) {
                error_log("Aucun terrain trouvé pour le gestionnaire {$gestionnaireId}");

                return [];
            }

            // Formatage des terrains
            $formattedTerrains = [];
            $colors = ['#3498db', '#e74c3c', '#27ae60', '#f39c12', '#9b59b6'];

            foreach ($terrains as $index => $terrain) {
                // Debug de l'image
                error_log("Terrain #{$terrain['id_terrain']} - Image: " . ($terrain['image'] ?? 'NULL'));

                // Déterminer le statut
                $statut = $terrain['statut'] ?? 'disponible';
                $statusIcon = 'disponible' === $statut ? 'fas fa-check-circle' : 'fas fa-times-circle';
                $statusColor = 'disponible' === $statut ? '#27ae60' : '#e74c3c';

                // Construire le nom du terrain
                $nomTerrain = '';

                if (!empty($terrain['nom_terrain'])) {
                    $nomTerrain = $terrain['nom_terrain'];
                } else {
                    if (isset($terrain['type_terrain'])) {
                        $nomTerrain .= $terrain['type_terrain'];
                    }

                    if (isset($terrain['format_terrain'])) {
                        $nomTerrain .= ' ' . $terrain['format_terrain'];
                    }

                    if (empty($nomTerrain)) {
                        $nomTerrain = 'Terrain #' . $terrain['id_terrain'];
                    }
                }

                // Localisation
                $localisation = $terrain['localisation'] ?? 'Non spécifié';

                // Prix
                $prix = isset($terrain['prix_heure']) ? number_format($terrain['prix_heure'], 2) . ' DH/h' : 'N/A';

                // Horaires
                $horaires = 'N/A';

                if (isset($terrain['heure_ouverture'], $terrain['heure_fermeture'])) {
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
                    'status_color' => $statusColor,
                    'image' => $terrain['image'] ?? '',
                ];
            }

            error_log('Terrains formatés : ' . count($formattedTerrains));

            return $formattedTerrains;
        } catch (PDOException $e) {
            error_log('Erreur getRecentActivities: ' . $e->getMessage());
            error_log('SQL Error: ' . print_r($stmt->errorInfo(), true));

            return [];
        }
    }
}
