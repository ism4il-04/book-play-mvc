<?php

require_once __DIR__ . '/../Core/Model.php';

class Tournoi extends Model {
    protected $table = 'tournoi';

    public function getAllTournois() {
        return $this->getAll($this->table);
    }

    public function getTournoiById($id) {
        return $this->getById($this->table, $id);
    }

    public function existedTournoi() {
        $stmt = $this->db->prepare('SELECT * FROM tournoi WHERE id_gestionnaire is not null ');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// À ajouter dans app/Models/Tournoi.php

/**
 * Récupérer les tournois à venir pour la newsletter
 */
public function getUpcomingTournois($limit = 3) {
    try {
        $sql = "SELECT t.*, ter.nom_terrain, ter.localisation
                FROM tournoi t
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
}