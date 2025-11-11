<?php

class UtilisateurController extends Controller {
    
    // Méthode par défaut - redirige vers dashboard
    public function index() {
        $this->dashboard();
    }
    
    // Dashboard - affiche les terrains disponibles pour l'utilisateur connecté
    public function dashboard() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        
        require_once __DIR__ . '/../Models/terrain.php';
        $terrainModel = new Terrain();
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $taille = isset($_GET['taille']) ? trim($_GET['taille']) : '';
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';

        if ('' !== $search || '' !== $taille || '' !== $type) {
            $terrains = $terrainModel->getAvailableTerrainsFiltered($search, $taille, $type);
        } else {
            $terrains = $terrainModel->getAvailableTerrains();
        }

        $this->view('utilisateur/dashboard', [
            'terrains' => $terrains,
            'filters' => [
                'search' => $search,
                'taille' => $taille,
                'type' => $type,
            ],
            'user' => $_SESSION['user'],
            'currentUser' => $_SESSION['user']
        ]);
    }

    // Afficher les réservations de l'utilisateur (ATTENTION: fichier = mesResevations.php)
    public function mesResevations() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        
        // Récupérer les réservations de l'utilisateur
        require_once __DIR__ . '/../Models/reservationTerrain.php';
        $reservationModel = new Reservation();
        $userId = $_SESSION['user']['id'];
        
        // Récupérer les réservations
        $reservations = $reservationModel->getReservationsByUser($userId);
        
        // Récupérer les statistiques
        $stats = $reservationModel->getUserReservationStats($userId);
        
        $this->view('utilisateur/mesReservations', [
            'reservations' => $reservations,
            'stats' => $stats,
            'user' => $_SESSION['user']
        ]);
    }

    // Alias pour compatibilité (si vous corrigez le nom du fichier plus tard)
    public function mesReservations() {
        $this->mesResevations();
    }

    // Créer une nouvelle réservation
    public function creerReservation() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../Models/Reservation.php';
            $reservationModel = new Reservation();

            // Récupérer les données du formulaire
            $data = [
                'user_id' => $_SESSION['user']['id'],
                'terrain_id' => $_POST['terrain_id'] ?? null,
                'date_reservation' => $_POST['date_reservation'] ?? null,
                'creneau' => $_POST['creneau'] ?? null, // Format: HH:MM:SS
                'commentaire' => $_POST['commentaire'] ?? '',
                'status' => 'en attente',
                'type' => 'normal'
            ];

            // Vérifier que les champs obligatoires sont remplis
            if (!$data['terrain_id'] || !$data['date_reservation'] || !$data['creneau']) {
                $_SESSION['error'] = "Tous les champs obligatoires doivent être remplis.";
                header('Location: ' . BASE_URL . 'utilisateur/dashboard');
                exit;
            }

            // Vérifier la disponibilité
            if (!$reservationModel->checkAvailability($data['terrain_id'], $data['date_reservation'], $data['creneau'])) {
                $_SESSION['error'] = "Ce créneau n'est pas disponible.";
                header('Location: ' . BASE_URL . 'utilisateur/dashboard');
                exit;
            }

            // Créer la réservation
            if ($reservationModel->createReservation($data)) {
                $_SESSION['success'] = "Réservation créée avec succès !";
                header('Location: ' . BASE_URL . 'utilisateur/mesResevations');
            } else {
                $_SESSION['error'] = "Erreur lors de la création de la réservation.";
                header('Location: ' . BASE_URL . 'utilisateur/dashboard');
            }
            exit;
        }
    }

    // Annuler une réservation
    public function annulerReservation() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../Models/Reservation.php';
            $reservationModel = new Reservation();

            $reservationId = $_POST['reservation_id'] ?? null;
            $userId = $_SESSION['user']['id'];

            if (!$reservationId) {
                $_SESSION['error'] = "ID de réservation manquant.";
                header('Location: ' . BASE_URL . 'utilisateur/mesResevations');
                exit;
            }

            // Annuler la réservation
            if ($reservationModel->cancelReservation($reservationId, $userId)) {
                $_SESSION['success'] = "Réservation annulée avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de l'annulation de la réservation.";
            }

            header('Location: ' . BASE_URL . 'utilisateur/mesResevations');
            exit;
        }
    }

    // Profil de l'utilisateur - Afficher et modifier
    public function profil() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Si le formulaire de mise à jour est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../Models/User.php';
            $userModel = new User();

            $data = [
                'id' => $_SESSION['user']['id'],
                'prenom' => trim($_POST['prenom'] ?? ''),
                'nom' => trim($_POST['nom'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'num_tel' => trim($_POST['num_tel'] ?? '')
            ];

            // Validation
            if (empty($data['prenom']) || empty($data['nom']) || empty($data['email'])) {
                $_SESSION['error'] = "Tous les champs obligatoires doivent être remplis.";
                header('Location: ' . BASE_URL . 'utilisateur/profil');
                exit;
            }

            // Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
            if ($userModel->emailExists($data['email'], $data['id'])) {
                $_SESSION['error'] = "Cet email est déjà utilisé par un autre compte.";
                header('Location: ' . BASE_URL . 'utilisateur/profil');
                exit;
            }

            // Mettre à jour le profil
            if ($userModel->updateProfile($data)) {
                // Mettre à jour la session
                $_SESSION['user']['prenom'] = $data['prenom'];
                $_SESSION['user']['nom'] = $data['nom'];
                $_SESSION['user']['email'] = $data['email'];
                $_SESSION['user']['num_tel'] = $data['num_tel'];

                $_SESSION['success'] = "Profil mis à jour avec succès !";
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour du profil.";
            }

            header('Location: ' . BASE_URL . 'utilisateur/profil');
            exit;
        }
        
        // Afficher la page de profil
        $this->view('utilisateur/profil', [
            'user' => $_SESSION['user']
        ]);
    }

    // Changer le mot de passe
    public function changePassword() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../Models/User.php';
            $userModel = new User();

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validation
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $_SESSION['error'] = "Tous les champs sont obligatoires.";
                header('Location: ' . BASE_URL . 'utilisateur/changePassword');
                exit;
            }

            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = "Les nouveaux mots de passe ne correspondent pas.";
                header('Location: ' . BASE_URL . 'utilisateur/changePassword');
                exit;
            }

            if (strlen($newPassword) < 6) {
                $_SESSION['error'] = "Le mot de passe doit contenir au moins 6 caractères.";
                header('Location: ' . BASE_URL . 'utilisateur/changePassword');
                exit;
            }

            // Vérifier l'ancien mot de passe
            $user = $userModel->getUserById($_SESSION['user']['id']);
            if (!$user || !password_verify($currentPassword, $user['password'])) {
                $_SESSION['error'] = "Le mot de passe actuel est incorrect.";
                header('Location: ' . BASE_URL . 'utilisateur/changePassword');
                exit;
            }

            // Mettre à jour le mot de passe
            if ($userModel->updatePassword($_SESSION['user']['id'], $newPassword)) {
                $_SESSION['success'] = "Mot de passe changé avec succès !";
                header('Location: ' . BASE_URL . 'utilisateur/dashboard');
            } else {
                $_SESSION['error'] = "Erreur lors du changement de mot de passe.";
                header('Location: ' . BASE_URL . 'utilisateur/changePassword');
            }
            exit;
        }

        // Afficher la page de changement de mot de passe
        $this->view('utilisateur/changePassword', [
            'user' => $_SESSION['user']
        ]);
    }
}