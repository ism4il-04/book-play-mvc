<?php

require_once __DIR__ . '/../Core/Model.php';

class Tournoi extends Model {
    protected $table = 'tournoi';
    
    private $gestionnaireId;

    public function __construct($gestionnaireId = null) {
        parent::__construct();
        $this->gestionnaireId = $gestionnaireId;
    }

    /**
     * Setter explicite si nécessaire après instanciation
     */
    public function setGestionnaireId($gestionnaireId) {
        $this->gestionnaireId = $gestionnaireId;
    }

    /**
     * Récupérer tous les tournois
     */
    public function getAllTournois() {
        return $this->getAll($this->table);
    }

    /**
     * Récupérer un tournoi par son identifiant
     */
    public function getTournoiById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_tournoi = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les tournois existants (avec gestionnaire associé)
     */
    public function existedTournoi() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_gestionnaire IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les tournois à venir (utilisé pour la newsletter)
     */
    public function getUpcomingTournois($limit = 3) {
        try {
            $sql = "SELECT t.*, ter.nom_terrain, ter.localisation
                    FROM {$this->table} t
                    LEFT JOIN terrain ter ON t.id_terrain = ter.id_terrain
                    WHERE t.date_debut >= CURDATE()
                    AND t.id_gestionnaire IS NOT NULL
                    ORDER BY t.date_debut ASC
                    LIMIT ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUpcomingTournois: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les tournois d'un gestionnaire
     */
    public function getForGestionnaire($gestionnaireId = null) {
        $gestionnaireId = $gestionnaireId ?? $this->gestionnaireId;

        if (!$gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_gestionnaire = ? ORDER BY date_debut DESC");
        $stmt->execute([$gestionnaireId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDemandesForGestionnaire($gestionnaireId = null) {
        $gestionnaireId = $gestionnaireId ?? $this->gestionnaireId;

        if (!$gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        try {
            $sql = "SELECT DISTINCT
                        t.*, 
                        u.nom AS client_nom,
                        u.prenom AS client_prenom,
                        u.email AS client_email,
                        u.num_tel AS client_tel,
                        COUNT(DISTINCT r.id_terrain) AS nombre_terrains,
                        GROUP_CONCAT(DISTINCT r.id_terrain) AS terrain_ids
                    FROM {$this->table} t
                    INNER JOIN demande d ON t.id_tournoi = d.id_tournoi
                    INNER JOIN utilisateur u ON d.id_client = u.id
                    LEFT JOIN reservation r ON t.id_tournoi = r.id_tournoi
                    WHERE t.id_gestionnaire = ?
                    GROUP BY t.id_tournoi
                    ORDER BY 
                        CASE t.status 
                            WHEN 'en attente' THEN 1 
                            WHEN 'accepté' THEN 2 
                            WHEN 'refusé' THEN 3 
                            ELSE 4
                        END,
                        t.date_debut DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$gestionnaireId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getDemandesForGestionnaire: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Créer un tournoi pour un gestionnaire
     */
    public function create($data) {
        $gestionnaireId = $this->gestionnaireId;

        if (!$gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        $sql = "INSERT INTO {$this->table} 
                (nom_tournoi, slogan, date_debut, date_fin, nb_equipes, prixPremiere, prixDeuxieme, prixTroisieme, status, id_gestionnaire) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'en attente', ?)";

        $stmt = $this->db->prepare($sql);
        if ($stmt->execute([
            $data['nom_tournoi'],
            $data['slogan'] ?? null,
            $data['date_debut'],
            $data['date_fin'],
            $data['nb_equipes'],
            $data['prixPremiere'] ?? null,
            $data['prixDeuxieme'] ?? null,
            $data['prixTroisieme'] ?? null,
            $gestionnaireId
        ])) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Récupérer un tournoi d'un gestionnaire par id
     */
    public function getByIdForGestionnaire($tournoiId, $gestionnaireId = null) {
        $gestionnaireId = $gestionnaireId ?? $this->gestionnaireId;

        if (!$gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_tournoi = ? AND id_gestionnaire = ?");
        $stmt->execute([$tournoiId, $gestionnaireId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les réservations (matchs) d'un tournoi
     */
    public function getReservationsForTournoi($tournoiId) {
        $sql = "SELECT 
                    r.id_reservation,
                    r.date_reservation,
                    r.creneau,
                    r.status,
                    r.commentaire,
                    t.id_terrain,
                    t.nom_terrain,
                    t.localisation
                FROM reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                WHERE r.id_tournoi = ? AND r.type = 'tournoi'
                ORDER BY r.date_reservation ASC, r.creneau ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tournoiId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Créer une réservation (match) pour un tournoi
     */
    public function createReservationForTournoi($tournoiId, $data) {
        $gestionnaireId = $this->gestionnaireId;

        if (!$gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        // Vérifier que le terrain appartient au gestionnaire
        $terrainStmt = $this->db->prepare("SELECT id_terrain FROM terrain WHERE id_terrain = ? AND id_gestionnaire = ?");
        $terrainStmt->execute([$data['id_terrain'], $gestionnaireId]);
        if (!$terrainStmt->fetch()) {
            throw new Exception("Terrain not found or does not belong to this gestionnaire");
        }

        // Vérifier que le tournoi appartient au gestionnaire
        $tournoi = $this->getByIdForGestionnaire($tournoiId, $gestionnaireId);
        if (!$tournoi) {
            throw new Exception("Tournament not found or does not belong to this gestionnaire");
        }

        $sql = "INSERT INTO reservation 
                (date_reservation, creneau, status, type, id_tournoi, id_terrain, commentaire) 
                VALUES (?, ?, 'accepté', 'tournoi', ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['date_reservation'],
            $data['creneau'],
            $tournoiId,
            $data['id_terrain'],
            $data['commentaire'] ?? ''
        ]);
    }

    /**
     * Supprimer une réservation (match) d'un tournoi
     */
    public function deleteReservationForTournoi($reservationId, $tournoiId) {
        $gestionnaireId = $this->gestionnaireId;

        if (!$gestionnaireId) {
            throw new Exception("Gestionnaire ID is required");
        }

        $sql = "DELETE r FROM reservation r
                INNER JOIN terrain t ON r.id_terrain = t.id_terrain
                WHERE r.id_reservation = ? 
                AND r.id_tournoi = ? 
                AND r.type = 'tournoi'
                AND t.id_gestionnaire = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$reservationId, $tournoiId, $gestionnaireId]);
    }
}