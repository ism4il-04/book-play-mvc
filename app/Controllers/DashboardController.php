<?php

class DashboardController extends Controller {
    public function utilisateur() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->view('utilisateur/dashboard', ['user' => $_SESSION['user']]);
    }

    public function gestionnaire() {
        // Check if user is logged in and is a manager
        if (!isset($_SESSION['user']) || 'gestionnaire' !== $_SESSION['user']['role']) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->view('gestionnaire/dashboard', ['user' => $_SESSION['user']]);
    }

    public function administrateur() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user']) || 'administrateur' !== $_SESSION['user']['role']) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Charger le modèle Admin
        $adminModel = $this->model('Admin');

        // Récupérer les statistiques
        $stats = $adminModel->getStats();
        
        // Récupérer les gestionnaires acceptés
        $gestionnaires = $adminModel->getAllGestionnairesAcceptes();

        // Préparer les données pour la vue
        $viewData = [
            'user' => $_SESSION['user'],
            'total' => $stats['total'] ?? 0,
            'actifs' => $stats['actifs'] ?? 0,
            'en_attente' => $stats['en_attente'] ?? 0,
            'refusees' => $stats['refuses'] ?? 0,
            'gestionnaires' => $gestionnaires,
            'error' => null,
        ];

        $this->view('administrateur/dashboard', $viewData);
    }
}