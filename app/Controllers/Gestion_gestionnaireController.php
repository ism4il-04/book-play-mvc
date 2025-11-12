<?php

require_once __DIR__ . '/../Core/Controller.php';

class Gestion_gestionnaireController extends Controller {
    public function index() {
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Charger le modèle Admin
        $adminModel = $this->model('Admin');
        
        // Récupérer les gestionnaires en attente
        $gestionnairesEnAttente = $adminModel->getAllGestionnairesEnAttente();

        // Récupérer les gestionnaires accepté
        $gestionnaires_accepte = $adminModel->getAllGestionnairesAccepte();

        // Récupérer les gestionnaires refusé
        $gestionnaires_refuse = $adminModel->getAllGestionnairesRefuse();

        $stats = $adminModel->getStats();



        // Préparer les données pour la vue
        $viewData = [
            'gestionnaires_en_attente' => $gestionnairesEnAttente,
            'gestionnaires_accepte' => $gestionnaires_accepte,
            'gestionnaires_refuse' => $gestionnaires_refuse,
            'nbrAccepte' => $stats['actifs'] ?? 0,
            'nbrEnAttente' => $stats['en_attente'] ?? 0,
            'nbrRefuse' => $stats['refuses'] ?? 0,
            'error' => null,
        ];

        // Afficher la vue avec TOUTES les données
        $this->view('administrateur/Gestion_gestionnaire', $viewData);
    }

    // Méthode pour accepter un gestionnaire
    public function accepter() {
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        // Récupérer l'ID du gestionnaire et du terrain
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $idTerrain = $data['id_terrain'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }

        // Charger le modèle et mettre à jour le statut
        $adminModel = $this->model('Admin');
        // Quand on accepte un gestionnaire, son terrain passe de 'en attente' à 'accepté'
        $result = $adminModel->updateGestionnaireStatus($id, 'accepté', 'acceptée', $idTerrain);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Gestionnaire accepté avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'acceptation']);
        }
        exit;
    }

    // Méthode pour refuser un gestionnaire
    public function refuser() {
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        // Récupérer l'ID du gestionnaire et du terrain
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $idTerrain = $data['id_terrain'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }

        // Charger le modèle et mettre à jour le statut
        $adminModel = $this->model('Admin');
        // Quand on refuse un gestionnaire, son terrain passe à 'refusé'
        $result = $adminModel->updateGestionnaireStatus($id, 'refusé', 'refusée', $idTerrain);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Gestionnaire refusé']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors du refus']);
        }
        exit;
    }

    // Méthode pour remettre en attente un gestionnaire refusé
    public function remettreEnAttente() {
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        // Récupérer l'ID du gestionnaire et du terrain
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $idTerrain = $data['id_terrain'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }

        // Charger le modèle et mettre à jour le statut
        $adminModel = $this->model('Admin');
        // Remettre le gestionnaire en attente et son terrain en attente
        $result = $adminModel->updateGestionnaireStatus($id, 'en attente', 'en attente', $idTerrain);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Demande remise en attente avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la remise en attente']);
        }
        exit;
    }

    // Méthode pour récupérer les détails d'un gestionnaire
    public function getDetails() {
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        // Récupérer l'ID du gestionnaire
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }

        // Charger le modèle et récupérer les détails
        $adminModel = $this->model('Admin');
        $gestionnaire = $adminModel->getGestionnaireDetailsById($id);

        if ($gestionnaire) {
            echo json_encode([
                'success' => true, 
                'gestionnaire' => $gestionnaire
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gestionnaire non trouvé']);
        }
        exit;
    }
}