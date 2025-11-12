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
        // Prevent all forms of caching
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        
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
    try {
        $db = Database::getInstance()->getConnection();
        
        // Récupérer UNIQUEMENT les tournois créés par les gestionnaires
        // (ceux qui n'ont PAS d'entrée dans la table demande)
        $sql = "SELECT 
                    t.id_tournoi,
                    t.nom_tournoi,
                    t.slogan,
                    t.date_debut,
                    t.date_fin,
                    t.nb_equipes,
                    t.prixPremiere,
                    t.prixDeuxieme,
                    t.prixTroisieme,
                    t.status,
                    u.nom AS gestionnaire_nom,
                    u.prenom AS gestionnaire_prenom,
                    ter.nom_terrain,
                    ter.localisation,
                    ter.image,
                    ter.type_terrain,
                    ter.format_terrain,
                    COALESCE(pi.equipes_inscrites, 0) AS equipes_inscrites,
                    CASE 
                        WHEN COALESCE(pi.equipes_inscrites, 0) >= t.nb_equipes THEN 'complet'
                        ELSE 'disponible'
                    END AS statut_inscription
                FROM tournoi t
                INNER JOIN gestionnaire g ON t.id_gestionnaire = g.id
                INNER JOIN utilisateur u ON g.id = u.id
                LEFT JOIN demande d ON t.id_tournoi = d.id_tournoi
                LEFT JOIN reservation r ON t.id_tournoi = r.id_tournoi
                LEFT JOIN terrain ter ON r.id_terrain = ter.id_terrain
                LEFT JOIN (
                    SELECT p.id_tournoi, COUNT(DISTINCT p.id_equipe) AS equipes_inscrites
                    FROM participation p
                    GROUP BY p.id_tournoi
                ) pi ON pi.id_tournoi = t.id_tournoi
                WHERE t.date_debut >= CURDATE()
                AND g.status = 'accepté'
                AND d.id_tournoi IS NULL
                GROUP BY t.id_tournoi
                ORDER BY t.date_debut ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'tournois' => $tournois]);
            exit;
        }

        $this->view('home/tournois', [
            'user' => $_SESSION['user'] ?? null,
            'tournois' => $tournois
        ]);

    } catch (PDOException $e) {
        error_log("Erreur tournois home: " . $e->getMessage());
        $this->view('home/tournois', [
            'user' => $_SESSION['user'] ?? null,
            'tournois' => []
        ]);
    }
}    public function subscribe() {
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