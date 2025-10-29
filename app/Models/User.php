<?php

require_once __DIR__ . '/../Core/Model.php';

class User extends Model {
    protected $table = 'utilisateur';

    /**
     * Try to authenticate a user by email and password.
     * Returns user array with role on success or false on failure.
     */
    public function authenticate($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        // Vérifier le mot de passe
        $passwordValid = false;
        
        // Si le mot de passe ressemble à un hash, utiliser password_verify
        if (isset($user['password']) && password_verify($password, $user['password'])) {
            $passwordValid = true;
        }
        
        // Sinon, comparaison en clair (pas recommandé en production)
        if (!$passwordValid && isset($user['password']) && $user['password'] === $password) {
            $passwordValid = true;
        }

        if (!$passwordValid) {
            return false;
        }

        // Ajouter le rôle à l'utilisateur
        $user['role'] = $this->determineRole($user['id']);

        return $user;
    }

    /**
     * Détermine le rôle d'un utilisateur en vérifiant les tables d'héritage
     */
    private function determineRole($userId) {
        // Vérifier si c'est un administrateur
        $stmt = $this->db->prepare("SELECT id FROM administrateur WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        if ($stmt->fetch()) {
            return 'administrateur';
        }
        
        // Vérifier si c'est un gestionnaire (avec status accepté)
        $stmt = $this->db->prepare("SELECT id FROM gestionnaire WHERE id = ? AND status = 'accepté' LIMIT 1");
        $stmt->execute([$userId]);
        if ($stmt->fetch()) {
            return 'gestionnaire';
        }
        
        // Vérifier si c'est un client
        $stmt = $this->db->prepare("SELECT id FROM client WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        if ($stmt->fetch()) {
            return 'utilisateur';
        }
        
        // Par défaut, utilisateur simple
        return 'utilisateur';
    }

    /**
     * Récupérer un utilisateur par son ID avec son rôle
     */
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user['role'] = $this->determineRole($user['id']);
        }

        return $user;
    }
}