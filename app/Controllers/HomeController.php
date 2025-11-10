<?php

class HomeController extends Controller {
    public function index() {
        require_once __DIR__ . '/../Models/terrain.php';
        require_once __DIR__ . '/../Models/Tournoi.php';
        $terrainModel = new Terrain();
        $tournoiModel = new Tournoi();
        $terrains = $terrainModel->getAvailableTerrains();
        $tournois = $tournoiModel->existedTournoi();
        
        // Récupérer le message de succès
        $subscribed = isset($_GET['subscribed']) && $_GET['subscribed'] == 1;
        $error = isset($_GET['error']) ? $_GET['error'] : null;
        
        $this->view('home/index', [
            'terrains' => $terrains,
            'tournois' => $tournois,
            'subscribed' => $subscribed,
            'error' => $error
        ]);
    }
    
    public function availableTerrains()
    {

    }
    
    public function terrains() {
        require_once __DIR__ . '/../Models/terrain.php';
        $terrainModel = new Terrain();
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $taille = isset($_GET['taille']) ? trim($_GET['taille']) : '';
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';

        if ($search !== '' || $taille !== '' || $type !== '') {
            $terrains = $terrainModel->getAvailableTerrainsFiltered($search, $taille, $type);
        } else {
            $terrains = $terrainModel->getAvailableTerrains();
        }

        $this->view('home/terrains', [
            'terrains' => $terrains,
            'filters' => [
                'search' => $search,
                'taille' => $taille,
                'type' => $type,
            ],
        ]);
    }
    
    public function tournois() {
        require_once __DIR__ . '/../Models/Tournoi.php';
        $tournoiModel = new Tournoi();
        $tournois = $tournoiModel->existedTournoi();
        $this->view('home/tournois', ['tournois' => $tournois]);
    }
    
    public function subscribe() {
        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'home?error=invalid_request');
            exit;
        }

        // Vérifier que l'email est présent
        if (!isset($_POST['email']) || empty(trim($_POST['email']))) {
            header('Location: ' . BASE_URL . 'home?error=email_required');
            exit;
        }

        // Nettoyer et valider l'email
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: ' . BASE_URL . 'home?error=invalid_email');
            exit;
        }

        // TODO: Sauvegarder l'email dans la base de données
        // Exemple:
        // require_once __DIR__ . '/../Models/Newsletter.php';
        // $newsletterModel = new Newsletter();
        // $result = $newsletterModel->subscribe($email);
        // if (!$result) {
        //     header('Location: ' . BASE_URL . 'home?error=db_error');
        //     exit;
        // }

        // Redirection avec message de succès
        header('Location: ' . BASE_URL . 'home?subscribed=1');
        exit;
    }
}