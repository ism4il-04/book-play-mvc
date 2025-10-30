<?php

class HomeController extends Controller {
    public function index() {
        require_once __DIR__ . '/../Models/terrain.php';
        require_once __DIR__ . '/../Models/Tournoi.php';
        $terrainModel = new Terrain();
        $tournoiModel = new Tournoi();
        $terrains = $terrainModel->getAvailableTerrains();
        $tournois = $tournoiModel->existedTournoi();
        $this->view('home/index'
//            , [
//            'terrains' => $terrains,
//            'tournois' => $tournois
//        ]
        );
    }
    public function availableTerrains()
    {

    }
    public function terrains() {
        require_once __DIR__ . '/../Models/terrain.php';
        $terrainModel = new Terrain();
        $terrains = $terrainModel->getAvailableTerrains();
        $this->view('home/terrains', ['terrains' => $terrains]);
    }
    public function tournois() {
        require_once __DIR__ . '/../Models/Tournoi.php';
        $tournoiModel = new Tournoi();
        $tournois = $tournoiModel->existedTournoi();
        $this->view('home/tournois', ['tournois' => $tournois]);
    }
    public function subscribe() {
        if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['email'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            // Here you can save the email to database if needed
            // For now, just redirect back with success message

            header('Location: ' . BASE_URL . 'home?subscribed=1');
            exit;
        }

        header('Location: ' . BASE_URL . 'home');
        exit;
    }
}
