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
                
                // Sauvegarder les options si elles existent
                if (!empty($data['options']) && is_array($data['options'])) {
                    error_log("Tentative de sauvegarde de " . count($data['options']) . " option(s) pour réservation $reservationId");
                    $optionsSaved = $this->addReservationOptions($reservationId, $data['options']);
                    if (!$optionsSaved) {
                        error_log("ATTENTION: Les options n'ont pas pu être sauvegardées pour la réservation $reservationId");
                    }
                } else {
                    error_log("Aucune option à sauvegarder pour la réservation $reservationId");
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
    public function addReservationOptions($reservationId, $options, $commentaires = []) {
        if (!is_array($options) || empty($options)) {
            error_log("addReservationOptions: Options vides ou non-array. Reservation ID: $reservationId");
            return false;
        }

        // Vérifier si la table a une colonne commentaire
        try {
            $checkColumn = $this->db->query("SHOW COLUMNS FROM reservation_option LIKE 'commentaire'");
            $hasCommentaire = $checkColumn->rowCount() > 0;
        } catch (PDOException $e) {
            $hasCommentaire = false;
        }

        if ($hasCommentaire) {
            $sql = "INSERT INTO reservation_option (id_reservation, id_option, commentaire) 
                    VALUES (:reservation_id, :id_option, :commentaire)";
        } else {
            $sql = "INSERT INTO reservation_option (id_reservation, id_option) 
                    VALUES (:reservation_id, :id_option)";
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $inserted = 0;
            
            foreach ($options as $optionId) {
                $optionId = (int)$optionId;
                if ($optionId <= 0) {
                    error_log("addReservationOptions: Option ID invalide: $optionId");
                    continue;
                }
                
                $stmt->bindValue(':reservation_id', $reservationId, PDO::PARAM_INT);
                $stmt->bindValue(':id_option', $optionId, PDO::PARAM_INT);
                
                if ($hasCommentaire) {
                    $commentaire = isset($commentaires[$optionId]) ? $commentaires[$optionId] : '';
                    $stmt->bindValue(':commentaire', $commentaire, PDO::PARAM_STR);
                }
                
                if ($stmt->execute()) {
                    $inserted++;
                } else {
                    error_log("addReservationOptions: Échec insertion option $optionId pour réservation $reservationId");
                }
            }
            
            error_log("addReservationOptions: $inserted option(s) insérée(s) pour réservation $reservationId");
            return $inserted > 0;
        } catch (PDOException $e) {
            error_log("Erreur addReservationOptions: " . $e->getMessage());
            error_log("SQL: $sql");
            error_log("Options: " . print_r($options, true));
            return false;
        }
    }
    
    /**
     * Supprimer toutes les options d'une réservation
     */
    public function deleteReservationOptions($reservationId) {
        $sql = "DELETE FROM reservation_option WHERE id_reservation = :reservation_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':reservation_id', $reservationId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur deleteReservationOptions: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les options d'une réservation
     */
    public function getOptionsForReservation($reservationId) {
        // Ajouter un log pour déboguer
        error_log("Début getOptionsForReservation pour reservation ID: " . $reservationId);
        
        // Vérifier si la table a une colonne commentaire
        try {
            $checkColumn = $this->db->query("SHOW COLUMNS FROM reservation_option LIKE 'commentaire'");
            $hasCommentaire = $checkColumn->rowCount() > 0;
        } catch (PDOException $e) {
            $hasCommentaire = false;
        }
        
        // Requête simplifiée pour récupérer les options d'une réservation
        if ($hasCommentaire) {
            $sql = "SELECT o.id_option, o.nom_option, COALESCE(p.prix_option, o.prix_option, 0) as prix_option, ro.commentaire 
                    FROM reservation_option ro
                    JOIN options o ON ro.id_option = o.id_option
                    LEFT JOIN reservation r ON ro.id_reservation = r.id_reservation
                    LEFT JOIN posseder p ON p.id_option = o.id_option AND p.id_terrain = r.id_terrain
                    WHERE ro.id_reservation = :id_reservation";
        } else {
            $sql = "SELECT o.id_option, o.nom_option, COALESCE(p.prix_option, o.prix_option, 0) as prix_option, '' as commentaire 
                    FROM reservation_option ro
                    JOIN options o ON ro.id_option = o.id_option
                    LEFT JOIN reservation r ON ro.id_reservation = r.id_reservation
                    LEFT JOIN posseder p ON p.id_option = o.id_option AND p.id_terrain = r.id_terrain
                    WHERE ro.id_reservation = :id_reservation";
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_reservation', $reservationId, PDO::PARAM_INT);
            $stmt->execute();
            
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Options trouvées pour reservation ID $reservationId: " . print_r($options, true));
            return $options;
        } catch (PDOException $e) {
            error_log("Erreur getOptionsForReservation: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Mettre à jour une réservation
     */
    public function updateReservation($reservationId, $userId, $data) {
        $sql = "UPDATE reservation 
                SET date_reservation = :date_reservation,
                    creneau = :creneau,
                    commentaire = :commentaire,
                    status = 'en attente'
                WHERE id_reservation = :id 
                AND id_client = :user_id
                AND status IN ('en attente','accepté')";
        
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
                    r.id_terrain,
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
            
            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($reservation) {
                // Extraire heure_debut et heure_fin du créneau
                $creneau = $reservation['creneau'] ?? '';
                if (strpos($creneau, '-') !== false) {
                    list($heureDebut, $heureFin) = explode('-', $creneau);
                    $reservation['heure_debut'] = trim($heureDebut);
                    $reservation['heure_fin'] = trim($heureFin);
                } else {
                    $reservation['heure_debut'] = '';
                    $reservation['heure_fin'] = '';
                }
                
                // Normaliser le statut
                $reservation['statut'] = $this->normalizeStatus($reservation['status']);
            }
            
            return $reservation;
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
     * La modification est possible jusqu'à 48 heures avant le début du match
     */
    public function canModifyReservation($reservationId, $userId) {
        $reservation = $this->getReservationById($reservationId, $userId);
        
        if (!$reservation) {
            error_log("Réservation $reservationId non trouvée pour l'utilisateur $userId");
            return false;
        }
        
        // Vérifier si la réservation est en attente ou acceptée
        if (!in_array($reservation['status'], ['en attente', 'accepté'])) {
            error_log("Réservation $reservationId a un statut qui ne permet pas la modification: {$reservation['status']}");
            return false;
        }
        
        // Vérifier si la date de réservation est dans le futur
        $dateReservation = $reservation['date_reservation'];
        if ($dateReservation < date('Y-m-d')) {
            error_log("Réservation $reservationId est dans le passé: $dateReservation");
            return false;
        }
        
        // Vérifier la règle des 48 heures
        $dateHeure = $dateReservation . ' ' . $reservation['heure_debut'];
        $timestamp = strtotime($dateHeure);
        $now = time();
        $diff = $timestamp - $now;
        $heures = $diff / 3600; // Convertir en heures
        
        $canModify = $heures >= 48;
        
        error_log("Réservation $reservationId: différence de $heures heures, modification " . ($canModify ? 'autorisée' : 'refusée'));
        
        return $canModify;
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