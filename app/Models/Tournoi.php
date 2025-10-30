<?php

class Tournoi extends Model {
    protected $table = 'tournoi';
    public function getAllTournois()
    {
        return $this->getAll($this->table);
    }

    public function getTournoiById($id){
        return $this->getById($this->table, $id);
    }
    public function existedTournoi() {
        $stmt = $this->db->prepare("SELECT * FROM tournoi WHERE id_gestionnaire is not null ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}