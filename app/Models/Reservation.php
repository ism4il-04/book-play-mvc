<?php

require_once __DIR__ . '/../Core/Model.php';

class Reservation extends Model {
    protected $table = 'reservation';

    private function getGestionnaireIdFromUserId($userId) {
        $sql = "SELECT id FROM gestionnaire WHERE id = ? AND status = 'accepté' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'] ?? null;
    }

    public function getForGestionnaire($userId, $status = null) {
        $gestionnaireId = $this->getGestionnaireIdFromUserId($userId);
        if (!$gestionnaireId) {
            return [];
        }

        $baseSql = "SELECT 
                r.id_reservation,
                r.id_terrain,
                r.id_client,
                r.date_reservation,
                r.creneau,
                r.status,
                r.commentaire,
                t.nom_terrain,
                t.localisation,
                u.nom AS client_nom,
                u.prenom AS client_prenom,
                u.email AS client_email,
                GROUP_CONCAT(DISTINCT CONCAT(o.nom_option, ' (', COALESCE(p.prix_option, 0), ' DH)') SEPARATOR ', ') AS options_selectionnees
            FROM reservation r
            INNER JOIN terrain t ON r.id_terrain = t.id_terrain
            INNER JOIN utilisateur u ON r.id_client = u.id
            LEFT JOIN reservation_option ro ON ro.id_reservation = r.id_reservation
            LEFT JOIN options o ON o.id_option = ro.id_option
            LEFT JOIN posseder p ON p.id_option = ro.id_option AND p.id_terrain = r.id_terrain
            WHERE t.id_gestionnaire = ?";

        $params = [$gestionnaireId];

        if ($status) {
            $baseSql .= " AND r.status = ?";
            $params[] = $status;
        }

        $baseSql .= " GROUP BY r.id_reservation
                      ORDER BY r.date_reservation DESC, r.creneau DESC";

        $stmt = $this->db->prepare($baseSql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatusForGestionnaire($userId, $reservationId, $newStatus) {
        $allowed = ['accepté', 'refusé', 'en attente', 'annulé'];
        if (!in_array($newStatus, $allowed, true)) {
            return false;
        }
        $gestionnaireId = $this->getGestionnaireIdFromUserId($userId);
        if (!$gestionnaireId) {
            return false;
        }

        // Only allow update if the reservation belongs to a terrain of this gestionnaire
        $sql = "UPDATE reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                SET r.status = ?
                WHERE r.id_reservation = ?
                AND t.id_gestionnaire = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$newStatus, $reservationId, $gestionnaireId]);
    }
    
    /**
     * Récupérer les détails d'une réservation pour un gestionnaire
     */
    public function getReservationDetailsForGestionnaire($userId, $reservationId) {
        $gestionnaireId = $this->getGestionnaireIdFromUserId($userId);
        if (!$gestionnaireId) {
            return null;
        }
        
        $sql = "SELECT r.id_reservation, r.date_reservation, r.creneau, r.id_terrain, r.status
                FROM reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                WHERE r.id_reservation = ?
                AND t.id_gestionnaire = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reservationId, $gestionnaireId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Refuser automatiquement les réservations conflictuelles
     * (même date, même créneau, même terrain)
     */
    public function refuseConflictingReservations($userId, $reservationId, $dateReservation, $creneau, $terrainId) {
        $gestionnaireId = $this->getGestionnaireIdFromUserId($userId);
        if (!$gestionnaireId) {
            return false;
        }
        
        // Refuser toutes les autres réservations avec la même date, créneau et terrain
        // qui sont encore en attente ou acceptées (sauf celle qu'on vient de modifier)
        $sql = "UPDATE reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                SET r.status = 'refusé'
                WHERE r.date_reservation = ?
                AND r.creneau = ?
                AND r.id_terrain = ?
                AND r.id_reservation != ?
                AND r.status IN ('en attente', 'accepté')
                AND t.id_gestionnaire = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$dateReservation, $creneau, $terrainId, $reservationId, $gestionnaireId]);
    }
}


