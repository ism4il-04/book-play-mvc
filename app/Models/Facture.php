<?php

require_once __DIR__ . '/../Core/Model.php';

class Facture extends Model {
    protected $table = 'facture';
    protected $reservationTable = 'reservation';
    protected $terrainTable = 'terrain';
    protected $clientTable = 'client';
    protected $utilisateurTable = 'utilisateur';
    protected $optionsTable = 'options';
    protected $reservationOptionsTable = 'reservation_option';
    protected $possederTable = 'posseder';

    /**
     * Génère une nouvelle facture pour une réservation
     */
    public function createFacture($id_reservation, $gestionnaire_id) {
        // Vérifier si la facture existe déjà
        if ($this->factureExists($id_reservation)) {
            throw new Exception("Une facture existe déjà pour cette réservation");
        }

        // Récupérer les données de la réservation
        $reservation = $this->getReservationDetails($id_reservation, $gestionnaire_id);
        if (!$reservation) {
            throw new Exception("Réservation introuvable ou accès non autorisé");
        }

        // Calculer le montant TTC
        $total_options = $this->calculateOptionsTotal($id_reservation);
        $prix_terrain = $reservation['prix_heure'];
        $montant_ttc = $prix_terrain + $total_options;

        // Générer numéro de facture unique
        $num_facture = $this->generateNumFacture();

        // Créer la facture
        $query = "INSERT INTO {$this->table} (num_facture, TTC, date_facturation, id_reservation)
                  VALUES (:num_facture, :ttc, CURDATE(), :id_reservation)";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':num_facture' => $num_facture,
            ':ttc' => $montant_ttc,
            ':id_reservation' => $id_reservation
        ]);

        return $num_facture;
    }

    /**
     * Vérifie si une facture existe pour cette réservation
     */
    public function factureExists($id_reservation) {
        $query = "SELECT num_facture FROM {$this->table} WHERE id_reservation = :id_reservation";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_reservation' => $id_reservation]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['num_facture'] : false;
    }

    /**
     * Récupère toutes les données pour afficher une facture
     */
    public function getFactureDetails($num_facture, $gestionnaire_id = null) {
        // Récupérer la facture
        $query = "SELECT f.*, r.id_reservation, r.date_reservation, r.creneau, r.status, r.type, r.commentaire,
                         r.id_terrain, r.id_client
                  FROM {$this->table} f
                  INNER JOIN {$this->reservationTable} r ON f.id_reservation = r.id_reservation
                  WHERE f.num_facture = :num_facture";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':num_facture' => $num_facture]);
        $facture = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$facture) {
            return null;
        }

        // Vérifier que le gestionnaire a accès à cette facture
        if ($gestionnaire_id && !$this->checkGestionnaireAccess($facture['id_terrain'], $gestionnaire_id)) {
            return null;
        }

        // Récupérer les informations du terrain
        $facture['terrain'] = $this->getTerrainInfo($facture['id_terrain']);

        // Récupérer les informations du client
        $facture['client'] = $this->getClientInfo($facture['id_client']);

        // Récupérer les informations du gestionnaire
        $facture['gestionnaire'] = $this->getGestionnaireInfo($facture['id_terrain']);

        // Récupérer les options
        $facture['options'] = $this->getReservationOptions($facture['id_reservation']);
        $facture['total_options'] = $this->calculateOptionsTotal($facture['id_reservation']);

        return $facture;
    }

    /**
     * Liste les réservations d'un gestionnaire
     */
    public function getReservationsByGestionnaire($gestionnaire_id, $filters = []) {
        $conditions = [];
        $params = [':gestionnaire_id' => $gestionnaire_id];

        // Conditions de base
        $conditions[] = "t.id_gestionnaire = :gestionnaire_id";
        $conditions[] = "t.etat = 'acceptée'"; // Seulement terrains approuvés
        $conditions[] = "t.statut = 'disponible'"; // Seulement terrains disponibles

        // Filtres optionnels
        if (!empty($filters['date_debut'])) {
            $conditions[] = "r.date_reservation >= :date_debut";
            $params[':date_debut'] = $filters['date_debut'];
        }

        if (!empty($filters['date_fin'])) {
            $conditions[] = "r.date_reservation <= :date_fin";
            $params[':date_fin'] = $filters['date_fin'];
        }

        if (!empty($filters['terrain_id'])) {
            $conditions[] = "r.id_terrain = :terrain_id";
            $params[':terrain_id'] = $filters['terrain_id'];
        }

        if (!empty($filters['status'])) {
            $conditions[] = "r.status = :status";
            $params[':status'] = $filters['status'];
        }

        $where_clause = implode(' AND ', $conditions);

        $query = "SELECT r.*, t.nom_terrain, t.prix_heure, t.type_terrain, t.format_terrain,
                         u.prenom as client_prenom, u.nom as client_nom, u.email as client_email, u.num_tel as client_tel,
                         f.num_facture, f.date_facturation
                  FROM {$this->reservationTable} r
                  INNER JOIN {$this->terrainTable} t ON r.id_terrain = t.id_terrain
                  INNER JOIN {$this->clientTable} c ON r.id_client = c.id
                  INNER JOIN {$this->utilisateurTable} u ON c.id = u.id
                  LEFT JOIN {$this->table} f ON r.id_reservation = f.id_reservation
                  WHERE {$where_clause}
                  ORDER BY r.date_reservation DESC, r.creneau DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les détails d'une réservation
     */
    private function getReservationDetails($id_reservation, $gestionnaire_id) {
        $query = "SELECT r.*, t.prix_heure
                  FROM {$this->reservationTable} r
                  INNER JOIN {$this->terrainTable} t ON r.id_terrain = t.id_terrain
                  WHERE r.id_reservation = :id_reservation
                  AND t.id_gestionnaire = :gestionnaire_id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id_reservation' => $id_reservation,
            ':gestionnaire_id' => $gestionnaire_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Calcule le total des options pour une réservation
     */
    private function calculateOptionsTotal($id_reservation) {
        try {
            $query = "SELECT SUM(p.prix_option) as total
                      FROM {$this->reservationOptionsTable} ro
                      INNER JOIN {$this->reservationTable} r ON ro.id_reservation = r.id_reservation
                      INNER JOIN {$this->possederTable} p ON ro.id_option = p.id_option AND r.id_terrain = p.id_terrain
                      WHERE ro.id_reservation = :id_reservation";

            $stmt = $this->db->prepare($query);
            $stmt->execute([':id_reservation' => $id_reservation]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['total'] ?: 0;
        } catch (Exception $e) {
            // If tables don't exist or columns don't exist, return 0
            error_log("Error calculating options total: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupère les options d'une réservation
     */
    private function getReservationOptions($id_reservation) {
        try {
            $query = "SELECT o.nom_option, p.prix_option as prix
                      FROM {$this->reservationOptionsTable} ro
                      INNER JOIN {$this->reservationTable} r ON ro.id_reservation = r.id_reservation
                      INNER JOIN {$this->optionsTable} o ON ro.id_option = o.id_option
                      INNER JOIN {$this->possederTable} p ON ro.id_option = p.id_option AND r.id_terrain = p.id_terrain
                      WHERE ro.id_reservation = :id_reservation";

            $stmt = $this->db->prepare($query);
            $stmt->execute([':id_reservation' => $id_reservation]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // If tables don't exist or columns don't exist, return empty array
            error_log("Error getting reservation options: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les informations du terrain
     */
    private function getTerrainInfo($id_terrain) {
        $query = "SELECT t.*, u.prenom, u.nom, u.email, u.num_tel
                  FROM {$this->terrainTable} t
                  INNER JOIN {$this->utilisateurTable} u ON t.id_gestionnaire = u.id
                  WHERE t.id_terrain = :id_terrain";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_terrain' => $id_terrain]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les informations du client
     */
    private function getClientInfo($id_client) {
        $query = "SELECT u.prenom, u.nom, u.email, u.num_tel
                  FROM {$this->utilisateurTable} u
                  WHERE u.id = :id_client";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_client' => $id_client]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les informations du gestionnaire
     */
    private function getGestionnaireInfo($id_terrain) {
        $query = "SELECT u.prenom, u.nom, u.email, u.num_tel
                  FROM {$this->utilisateurTable} u
                  INNER JOIN {$this->terrainTable} t ON u.id = t.id_gestionnaire
                  WHERE t.id_terrain = :id_terrain";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_terrain' => $id_terrain]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si le gestionnaire a accès au terrain
     */
    public function checkGestionnaireAccess($id_terrain, $gestionnaire_id) {
        $query = "SELECT id_terrain FROM {$this->terrainTable}
                  WHERE id_terrain = :id_terrain AND id_gestionnaire = :gestionnaire_id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id_terrain' => $id_terrain,
            ':gestionnaire_id' => $gestionnaire_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    /**
     * Génère un numéro de facture unique et croissant
     */
    private function generateNumFacture() {
        $query = "SELECT MAX(num_facture) AS max_num FROM {$this->table}";
        $stmt = $this->db->query($query);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $currentMax = ($row && $row['max_num'] !== null) ? (int) $row['max_num'] : 0;

        return $currentMax + 1;
    }

    /**
     * Vérifie si un numéro de facture existe
     */
    private function factureExistsByNum($num_facture) {
        $query = "SELECT num_facture FROM {$this->table} WHERE num_facture = :num_facture";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':num_facture' => $num_facture]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    /**
     * Vérifie l'accès à une réservation et retourne les infos nécessaires
     */
    public function getReservationAccess($id_reservation, $current_user_id) {
        $query = "SELECT r.id_reservation, r.id_terrain, t.id_gestionnaire, t.nom_terrain
                  FROM {$this->reservationTable} r
                  INNER JOIN {$this->terrainTable} t ON r.id_terrain = t.id_terrain
                  WHERE r.id_reservation = :id_reservation";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_reservation' => $id_reservation]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reservation) {
            return null;
        }

        // Vérifier que le gestionnaire actuel possède ce terrain
        if ($reservation['id_gestionnaire'] !== $current_user_id) {
            return null;
        }

        return $reservation;
    }

    /**
     * Met à jour le chemin du PDF pour une facture
     */
    public function updatePdfPath($num_facture, $pdf_path) {
        $query = "UPDATE {$this->table} SET facture_path = :pdf_path WHERE num_facture = :num_facture";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':pdf_path' => $pdf_path,
            ':num_facture' => $num_facture
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     *
     * Optionnellement, ne retourne que celles dont le numéro est supérieur à $afterNumFacture
     */
    public function getFacturesByClient($client_id, $afterNumFacture = null) {
        $params = [':client_id' => $client_id];

        $query = "SELECT f.*, r.id_reservation, r.date_reservation, r.creneau, r.status, r.type, r.commentaire,
                         r.id_terrain, r.id_client,
                         t.nom_terrain, t.type_terrain, t.format_terrain, t.prix_heure
                  FROM {$this->table} f
                  INNER JOIN {$this->reservationTable} r ON f.id_reservation = r.id_reservation
                  INNER JOIN {$this->terrainTable} t ON r.id_terrain = t.id_terrain
                  WHERE r.id_client = :client_id";

        if ($afterNumFacture !== null) {
            $query .= " AND f.num_facture > :after_num";
            $params[':after_num'] = $afterNumFacture;
        }

        $query .= " ORDER BY f.date_facturation DESC, r.date_reservation DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les terrains d'un gestionnaire pour les filtres
     */
    public function getGestionnaireTerrains($gestionnaire_id) {
        $query = "SELECT id_terrain, nom_terrain
                  FROM {$this->terrainTable}
                  WHERE id_gestionnaire = :gestionnaire_id
                  AND etat = 'acceptée'
                  AND statut = 'disponible'
                  ORDER BY nom_terrain";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':gestionnaire_id' => $gestionnaire_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
