<?php

require_once __DIR__ . '/../Core/Model.php';

class User extends Model {
    protected $table = 'utilisateur';

    /**
     * Try to authenticate a user by email and password.
     * Returns user array with role on success or false on failure.
     *
     * SÉCURITÉ AMÉLIORÉE :
     * - Accepte uniquement les mots de passe hachés avec password_verify()
     * - Migration automatique des anciens mots de passe en clair (si activée)
     *
     * @param mixed $email
     * @param mixed $password
     */
    public function authenticate($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        // Vérifier si le mot de passe existe
        if (!isset($user['password']) || empty($user['password'])) {
            return false;
        }

        $passwordValid = false;
        $needsRehash = false;

        // Vérification avec password_verify
        if (password_verify($password, $user['password'])) {
            $passwordValid = true;

            // Vérifier si le hash doit être mis à jour (algorithme obsolète)
            if (password_needs_rehash($user['password'], PASSWORD_BCRYPT)) {
                $needsRehash = true;
            }
        }

        if (!$passwordValid) {
            return false;
        }

        // Si le mot de passe doit être re-haché, le mettre à jour automatiquement
        if ($needsRehash) {
            $this->updatePasswordHash($user['id'], $password);
        }

        // Ajouter le rôle à l'utilisateur
        $user['role'] = $this->determineRole($user['id']);

        return $user;
    }

    /**
     * Récupérer un utilisateur par son ID avec son rôle.
     *
     * @param mixed $id
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

    /**
     * Enregistrer un nouvel utilisateur.
     * Le mot de passe est automatiquement haché avec PASSWORD_BCRYPT.
     */
    public function register(string $nom, string $prenom, string $email, string $password, string $num_tel = ''): bool {
        // Vérifier si l'email existe déjà
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return false; // email déjà pris
        }

        // Hacher le mot de passe avec PASSWORD_BCRYPT
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        // Insérer le nouvel utilisateur avec le numéro de téléphone
        $insert = $this->db->prepare(
            "INSERT INTO {$this->table} (nom, prenom, email, password, num_tel) VALUES (?, ?, ?, ?, ?)"
        );

        try {
            return (bool) $insert->execute([$nom, $prenom, $email, $hashed, $num_tel]);
        } catch (PDOException $e) {
            // Log de l'erreur
            error_log("Erreur lors de l'enregistrement de l'utilisateur : " . $e->getMessage());

            return false;
        }
    }

    /**
     * Mettre à jour le profil utilisateur
     * 
     * @param array $data Tableau contenant id, prenom, nom, email, num_tel
     * @return bool
     */
    public function updateProfile($data) {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} 
             SET prenom = ?, nom = ?, email = ?, num_tel = ? 
             WHERE id = ?"
        );

        try {
            return $stmt->execute([
                $data['prenom'],
                $data['nom'],
                $data['email'],
                $data['num_tel'],
                $data['id']
            ]);
        } catch (PDOException $e) {
            error_log("Erreur mise à jour profil pour user ID {$data['id']}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier si un email existe déjà (sauf pour l'utilisateur spécifié)
     * 
     * @param string $email
     * @param int|null $excludeUserId ID de l'utilisateur à exclure de la recherche
     * @return bool
     */
    public function emailExists($email, $excludeUserId = null) {
        if ($excludeUserId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE email = ? AND id != ?");
            $stmt->execute([$email, $excludeUserId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE email = ?");
            $stmt->execute([$email]);
        }
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Mettre à jour le mot de passe d'un utilisateur
     * 
     * @param int $userId
     * @param string $newPassword Mot de passe en clair (sera haché automatiquement)
     * @return bool
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ? WHERE id = ?");

        try {
            return $stmt->execute([$hashedPassword, $userId]);
        } catch (PDOException $e) {
            error_log("Erreur changement password pour user ID {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Changer le mot de passe d'un utilisateur (alias de updatePassword pour compatibilité)
     *
     * @param mixed $userId
     * @param mixed $newPassword
     */
    public function changePassword($userId, $newPassword) {
        return $this->updatePassword($userId, $newPassword);
    }

    /**
     * Met à jour le hash du mot de passe (pour migration automatique)
     * Utilisé en interne par authenticate()
     *
     * @param mixed $userId
     * @param mixed $plainPassword
     */
    private function updatePasswordHash($userId, $plainPassword) {
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ? WHERE id = ?");

        try {
            $stmt->execute([$hashedPassword, $userId]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur mise à jour password hash pour user ID {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer un utilisateur par email
     * 
     * @param string $email
     * @return array|false
     */
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user['role'] = $this->determineRole($user['id']);
        }

        return $user;
    }

    /**
     * Détermine le rôle d'un utilisateur en vérifiant les tables d'héritage.
     *
     * @param mixed $userId
     */
    private function determineRole($userId) {
        // Vérifier si c'est un administrateur
        $stmt = $this->db->prepare('SELECT id FROM administrateur WHERE id = ? LIMIT 1');
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
        $stmt = $this->db->prepare('SELECT id FROM client WHERE id = ? LIMIT 1');
        $stmt->execute([$userId]);

        if ($stmt->fetch()) {
            return 'utilisateur';
        }

        // Par défaut, utilisateur simple
        return 'utilisateur';
    }
}