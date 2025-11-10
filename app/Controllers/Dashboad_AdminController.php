<?php
require_once __DIR__ . '/../core/Controller.php';

class Dashboad_AdminController extends Controller {
    public function index() {
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
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

        // Afficher la vue avec TOUTES les données
        $this->view('administrateur/dashboard', $viewData);
    }
}