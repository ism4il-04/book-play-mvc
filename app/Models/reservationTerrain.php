<?php

require_once __DIR__ . '/../Core/Database.php';

class Reservation {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Récupérer toutes les réservations d'un utilisateur (client)
     */
    public function getReservationsByUser($userId) {
        $sql = "SELECT 
                    r.id_reservation as id,
                    r.date_reservation,
                    r.creneau,
                    r.status as statut,
                    r.type,
                    r.commentaire,
                    t.id_terrain,
                    t.nom_terrain,
                    t.localisation,
                    t.type_terrain,
                    t.format_terrain,
                    t.prix_heure,
                    t.image,
                    CONCAT(u.prenom, ' ', u.nom) as gestionnaire_nom,
                    u.email as gestionnaire_email,
                    u.num_tel as gestionnaire_tel
                FROM reservation r 
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain 
                LEFT JOIN gestionnaire g ON t.id_gestionnaire = g.id
                LEFT JOIN utilisateur u ON g.id = u.id
                WHERE r.id_client = :user_id 
                ORDER BY r.date_reservation DESC, r.creneau DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return array_map(function($reservation) {
                // Extraire heure début et fin du créneau
                $creneau = $reservation['creneau'];
                if (strpos($creneau, '-') !== false) {
                    list($heureDebut, $heureFin) = explode('-', $creneau);
                } else {
                    $heureDebut = $creneau;
                    $heureFin = date('H:i:s', strtotime($creneau) + 3600);
                }
                
                return [
                    'id' => $reservation['id'],
                    'terrain_id' => $reservation['id_terrain'],
                    'terrain_nom' => $reservation['nom_terrain'] ?: $reservation['localisation'],
                    'localisation' => $reservation['localisation'],
                    'format_terrain' => $reservation['format_terrain'],
                    'type_terrain' => $reservation['type_terrain'],
                    'date_reservation' => $reservation['date_reservation'],
                    'heure_debut' => substr($heureDebut, 0, 5),
                    'heure_fin' => substr($heureFin, 0, 5),
                    'prix_heure' => $reservation['prix_heure'],
                    'statut' => $this->normalizeStatus($reservation['statut']),
                    'type' => $reservation['type'],
                    'image' => $reservation['image'],
                    'gestionnaire_nom' => $reservation['gestionnaire_nom'],
                    'gestionnaire_email' => $reservation['gestionnaire_email'] ?? 'contact@bookplay.ma',
                    'gestionnaire_tel' => $reservation['gestionnaire_tel'],
                    'commentaire' => $reservation['commentaire']
                ];
            }, $reservations);
            
        } catch (PDOException $e) {
            error_log("Erreur getReservationsByUser: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Créer une nouvelle réservation
     */
    public function createReservation($data) {
        error_log("Début createReservation avec données: " . print_r($data, true));
        
        // Validation des données requises
        $required = ['user_id', 'terrain_id', 'date_reservation', 'heure_debut', 'heure_fin'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                error_log("Champ requis manquant: $field");
                return false;
            }
        }

        // Construire le créneau
        $creneau = $data['heure_debut'] . '-' . $data['heure_fin'];
        error_log("Créneau construit: " . $creneau);
        
        // Vérifier la disponibilité
        if (!$this->checkAvailability($data['terrain_id'], $data['date_reservation'], $creneau)) {
            error_log("Créneau non disponible");
            return false;
        }

        $sql = "INSERT INTO reservation (
                    id_client, 
                    id_terrain, 
                    date_reservation, 
                    creneau, 
                    status, 
                    type, 
                    commentaire
                ) VALUES (
                    :id_client, 
                    :terrain_id, 
                    :date_reservation, 
                    :creneau, 
                    :status, 
                    :type, 
                    :commentaire
                )";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_client', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':terrain_id', $data['terrain_id'], PDO::PARAM_INT);
            $stmt->bindValue(':date_reservation', $data['date_reservation']);
            $stmt->bindValue(':creneau', $creneau);
            $stmt->bindValue(':status', $data['status'] ?? 'en attente');
            $stmt->bindValue(':type', $data['type'] ?? 'normal');
            $stmt->bindValue(':commentaire', $data['commentaire'] ?? '');
            
            $success = $stmt->execute();
            error_log("Exécution SQL réussie: " . ($success ? "OUI" : "NON"));
            
            if ($success) {
                $reservationId = $this->db->lastInsertId();
                error_log("ID de la nouvelle réservation: " . $reservationId);
                
                if (!empty($data['options'])) {
                    $this->addReservationOptions($reservationId, $data['options']);
                }
            }
            
            return $success;
        } catch (PDOException $e) {
            error_log("Erreur createReservation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ajouter des options à une réservation
     */
    private function addReservationOptions($reservationId, $options) {
        if (!is_array($options) || empty($options)) {
            return false;
        }

        $sql = "INSERT INTO reservation_option (id_reservation, id_option) 
                SELECT :reservation_id, id_option 
                FROM options 
                WHERE nom_option = :nom_option";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            foreach ($options as $optionName) {
                $stmt->bindValue(':reservation_id', $reservationId, PDO::PARAM_INT);
                $stmt->bindValue(':nom_option', $optionName);
                $stmt->execute();
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Erreur addReservationOptions: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour une réservation
     */
    public function updateReservation($reservationId, $userId, $data) {
        $sql = "UPDATE reservation 
                SET date_reservation = :date_reservation,
                    creneau = :creneau,
                    commentaire = :commentaire
                WHERE id_reservation = :id 
                AND id_client = :user_id
                AND status = 'en attente'";
        
        try {
            $creneau = $data['heure_debut'] . '-' . $data['heure_fin'];
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $reservationId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':date_reservation', $data['date_reservation']);
            $stmt->bindValue(':creneau', $creneau);
            $stmt->bindValue(':commentaire', $data['commentaire'] ?? '');
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur updateReservation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Annuler une réservation
     */
    public function cancelReservation($reservationId, $userId) {
        $sql = "UPDATE reservation 
                SET status = 'refusé' 
                WHERE id_reservation = :id 
                AND id_client = :user_id
                AND status IN ('en attente', 'accepté')";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $reservationId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur cancelReservation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier la disponibilité d'un terrain
     */
public function checkAvailability($terrainId, $date, $creneau) {
    error_log("Vérification disponibilité: terrain=$terrainId, date=$date, creneau=$creneau");
    
    $sql = "SELECT COUNT(*) as count 
            FROM reservation 
            WHERE id_terrain = :terrain_id 
            AND date_reservation = :date 
            AND creneau = :creneau
            AND status IN ('accepté', 'en attente')";
    
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':terrain_id', $terrainId, PDO::PARAM_INT);
        $stmt->bindValue(':date', $date);
        $stmt->bindValue(':creneau', $creneau);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $disponible = ($result['count'] == 0);
        error_log("Résultat disponibilité: " . ($disponible ? "Disponible" : "Non disponible"));
        return $disponible;
    } catch (PDOException $e) {
        error_log("Erreur checkAvailability: " . $e->getMessage());
        return false;
    }
}

    /**
     * Récupérer une réservation par ID
     */
    public function getReservationById($reservationId, $userId = null) {
        $sql = "SELECT 
                    r.*,
                    t.nom_terrain,
                    t.localisation, 
                    t.type_terrain, 
                    t.format_terrain, 
                    t.prix_heure, 
                    t.image,
                    CONCAT(u.prenom, ' ', u.nom) as gestionnaire_nom,
                    u.email as gestionnaire_email
                FROM reservation r 
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain 
                LEFT JOIN gestionnaire g ON t.id_gestionnaire = g.id
                LEFT JOIN utilisateur u ON g.id = u.id
                WHERE r.id_reservation = :id";
        
        if ($userId !== null) {
            $sql .= " AND r.id_client = :user_id";
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $reservationId, PDO::PARAM_INT);
            if ($userId !== null) {
                $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            }
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getReservationById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Statistiques de réservation pour un utilisateur
     */
    public function getUserReservationStats($userId) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'accepté' THEN 1 ELSE 0 END) as acceptees,
                    SUM(CASE WHEN status = 'en attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(CASE WHEN status = 'refusé' THEN 1 ELSE 0 END) as refusees
                FROM reservation 
                WHERE id_client = :user_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUserReservationStats: " . $e->getMessage());
            return [
                'total' => 0,
                'acceptees' => 0,
                'en_attente' => 0,
                'refusees' => 0
            ];
        }
    }

    /**
     * Réservations à venir pour un utilisateur
     */
    public function getUpcomingReservations($userId) {
        $sql = "SELECT 
                    r.*,
                    t.nom_terrain,
                    t.localisation, 
                    t.type_terrain, 
                    t.format_terrain, 
                    t.prix_heure,
                    t.image
                FROM reservation r 
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain 
                WHERE r.id_client = :user_id 
                AND r.date_reservation >= CURDATE()
                AND r.status IN ('accepté', 'en attente')
                ORDER BY r.date_reservation ASC, r.creneau ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUpcomingReservations: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculer le prix total avec options
     */
    public function calculateTotalPrice($prixHeure, $heureDebut, $heureFin, $options = []) {
        $debut = strtotime($heureDebut);
        $fin = strtotime($heureFin);
        $duree = ($fin - $debut) / 3600;
        
        $total = $prixHeure * $duree;
        
        // Prix des options
        $prixOptions = [
            'ballon' => 20,
            'maillots' => 50,
            'douche' => 30,
            'bouteille_eau' => 10
        ];
        
        foreach ($options as $option) {
            if (isset($prixOptions[$option])) {
                $total += $prixOptions[$option];
            }
        }
        
        return $total;
    }

    /**
     * Normaliser le statut pour l'affichage
     */
    private function normalizeStatus($status) {
        $statusMap = [
            'accepté' => 'Acceptée',
            'accepte' => 'Acceptée',
            'en attente' => 'En attente',
            'refusé' => 'Refusée',
            'refuse' => 'Refusée'
        ];
        
        $statusLower = strtolower(trim($status));
        return $statusMap[$statusLower] ?? ucfirst($status);
    }

    /**
     * Vérifier si une réservation peut être modifiée
     */
    public function canModifyReservation($reservationId, $userId) {
        $reservation = $this->getReservationById($reservationId, $userId);
        
        if (!$reservation) {
            return false;
        }
        
        return $reservation['status'] === 'en attente' 
               && $reservation['date_reservation'] >= date('Y-m-d');
    }

    /**
     * Vérifier si une réservation peut être annulée
     */
    public function canCancelReservation($reservationId, $userId) {
        $reservation = $this->getReservationById($reservationId, $userId);
        
        if (!$reservation) {
            return false;
        }
        
        return in_array($reservation['status'], ['accepté', 'en attente'])
               && $reservation['date_reservation'] >= date('Y-m-d');
    }

    /**
     * Récupérer les options d'une réservation
     */
    public function getReservationOptions($reservationId) {
        $sql = "SELECT o.* 
                FROM options o
                INNER JOIN reservation_option ro ON o.id_option = ro.id_option
                WHERE ro.id_reservation = :reservation_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':reservation_id', $reservationId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getReservationOptions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les réservations d'un terrain (pour le gestionnaire)
     */
    public function getReservationsByTerrain($terrainId, $startDate = null, $endDate = null) {
        $sql = "SELECT 
                    r.*,
                    CONCAT(u.prenom, ' ', u.nom) as client_nom,
                    u.email as client_email,
                    u.num_tel as client_tel
                FROM reservation r 
                INNER JOIN client c ON r.id_client = c.id
                INNER JOIN utilisateur u ON c.id = u.id
                WHERE r.id_terrain = :terrain_id";
        
        if ($startDate) {
            $sql .= " AND r.date_reservation >= :start_date";
        }
        if ($endDate) {
            $sql .= " AND r.date_reservation <= :end_date";
        }
        
        $sql .= " ORDER BY r.date_reservation DESC, r.creneau DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':terrain_id', $terrainId, PDO::PARAM_INT);
            if ($startDate) {
                $stmt->bindValue(':start_date', $startDate);
            }
            if ($endDate) {
                $stmt->bindValue(':end_date', $endDate);
            }
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getReservationsByTerrain: " . $e->getMessage());
            return [];
        }
    }
}