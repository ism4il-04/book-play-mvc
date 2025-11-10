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
            'user' => $_SESSION['user']
        ]);
    }

    // Afficher les réservations de l'utilisateur
    public function mesReservations() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        
        // Récupérer les réservations de l'utilisateur
        require_once __DIR__ . '/../Models/reservationTerrain.php';
        $reservationModel = new Reservation();
        $userId = $_SESSION['user']['id'];
        $reservations = $reservationModel->getReservationsByUser($userId);
        
        $this->view('utilisateur/mesReservations', [
            'reservations' => $reservations,
            'user' => $_SESSION['user']
        ]);
    }

    // Profil de l'utilisateur
    public function profil() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        
        $this->view('utilisateur/profil', [
            'user' => $_SESSION['user']
        ]);
    }
}