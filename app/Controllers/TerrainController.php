<?php

require_once __DIR__ . '/../Models/Terrain.php';

class TerrainController extends Controller{

    private $db;
    private $uploadDir = __DIR__ . '/../../public/images/';
    private $justificatifsDir = __DIR__ . '/../../public/uploads/justificatifs/';

    public function __construct($db)
    {
        $this->db = $db;
        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
        
        // Create justificatifs directory if it doesn't exist
        if (!file_exists($this->justificatifsDir)) {
            mkdir($this->justificatifsDir, 0777, true);
        }
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Default index method - redirects based on user role
     */
    public function index() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $role = $_SESSION['user']['role'] ?? null;
        
        if ($role === 'gestionnaire') {
            header('Location: ' . BASE_URL . 'terrain/gestionnaireTerrains');
        } else {
            header('Location: ' . BASE_URL . 'utilisateur/dashboard');
        }
        exit;
    }

    public function gestionnaireTerrains() {
        // Check if user is logged in and is a manager
        if (!isset($_SESSION['user']) || 'gestionnaire' !== ($_SESSION['user']['role'] ?? null)) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        require_once __DIR__ . '/../Models/Terrain.php';
        $terrainModel = new Terrain($_SESSION['user']['id']);
        $terrains = $terrainModel->getGestionnaireTerrains();
        $this->view('gestionnaire/gestion_terrains', ['terrains' => $terrains, 'user' => $_SESSION['user']]);
    }
    public function create() {
        // Check if user is logged in and is a manager
        if (!isset($_SESSION['user']) || 'gestionnaire' !== ($_SESSION['user']['role'] ?? null)) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Load the view for adding a new terrain
        $this->view('gestionnaire/ajouter_terrain', ['user' => $_SESSION['user']]);
    }

    private function uploadImage($file) {
        $targetFile = $this->uploadDir . basename($file['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is an actual image
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            throw new Exception("File is not an image.");
        }

        // Check file size (max 5MB)
        if ($file['size'] > 5000000) {
            throw new Exception("Sorry, your file is too large. Maximum size is 5MB.");
        }

        // Allow certain file formats
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            throw new Exception("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Generate unique filename
        $newFilename = uniqid() . '.' . $imageFileType;
        $targetFile = $this->uploadDir . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $newFilename; // Return relative path
        } else {
            throw new Exception("Sorry, there was an error uploading your file.");
        }
    }

    private function uploadJustificatifs($files) {
        $uploadedFiles = [];
        
        if (!isset($files['name']) || !is_array($files['name'])) {
            return json_encode([]); // Return empty JSON array if no files
        }
        
        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] !== UPLOAD_ERR_OK) {
                continue; // Skip files with errors
            }
            
            $file = [
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error' => $files['error'][$key],
                'size' => $files['size'][$key]
            ];
            
            $targetFile = $this->justificatifsDir . basename($file['name']);
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            
            // Check file size (max 10MB as per form)
            if ($file['size'] > 10000000) {
                continue; // Skip large files
            }
            
            // Allow certain file formats
            $allowedTypes = ['pdf', 'png', 'jpg', 'jpeg'];
            if (!in_array($fileType, $allowedTypes)) {
                continue; // Skip invalid file types
            }
            
            // Generate unique filename
            $newFilename = uniqid() . '_' . $file['name'];
            $targetFile = $this->justificatifsDir . $newFilename;
            
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                $uploadedFiles[] = $newFilename;
            }
        }
        
        return json_encode($uploadedFiles);
    }


    public function store()
    {
        header('Content-Type: application/json');
        try {
            $imagePath = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->uploadImage($_FILES['image']);
            } else {
                echo json_encode(["success" => false, "message" => "Image is required"]);
                exit;
            }

            // Handle justificatifs upload
            $justificatifsJson = $this->uploadJustificatifs($_FILES['justificatifs'] ?? []);

            // Get posted form data
            $data = [
                'nom_terrain'    => htmlspecialchars($_POST['nom_terrain'] ?? ''),
                'image'          => $imagePath,
                'prix'           => htmlspecialchars($_POST['prix'] ?? ''),
                'localisation'   => htmlspecialchars($_POST['localisation'] ?? ''),
                'type_terrain'   => htmlspecialchars($_POST['type_terrain'] ?? ''),
                'format_terrain' => htmlspecialchars($_POST['format_terrain'] ?? ''),
                'heure_ouverture' => $_POST['heure_ouverture'] ?? null,
                'heure_fermeture' => $_POST['heure_fermeture'] ?? null,
                'options' => $_POST['options'] ?? [],
                'justificatifs' => $justificatifsJson
            ];

            // Validate required fields
            foreach ($data as $key => $value) {
                if (empty($value) && $key !== 'image' && $key !== 'heure_ouverture' && $key !== 'heure_fermeture' && $key !== 'options') {
                    echo json_encode(["success" => false, "message" => "All fields are required"]);
                    exit;
                }
            }

            if (!isset($_SESSION['user']['id'])) {
                echo json_encode(["success" => false, "message" => "User not authenticated"]);
                exit;
            }

            $terrain = new Terrain($_SESSION['user']['id']);

            if ($terrain->create($data)) {
                $newTerrain = $terrain->getLastInserted();
                echo json_encode(["success" => true, "terrain" => $newTerrain]);
            } else {
                echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout"]);
            }

        } catch (Exception $e) {
            error_log("Error in store: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Erreur serveur: " . $e->getMessage()]);
        }

        exit;
    }



    public function delete($id) {
        header('Content-Type: application/json');
        try {
            // Check if user is logged in and is a manager
            if (!isset($_SESSION['user']) || 'gestionnaire' !== ($_SESSION['user']['role'] ?? null)) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }

            $terrainModel = new Terrain($_SESSION['user']['id']);
            $deletedTerrain = $terrainModel->getLastDeleted($id);
            if ($terrainModel->delete($id)) {
                echo json_encode(["success" => true, "terrain" => $deletedTerrain]);
            } else {
                echo json_encode(["success" => false, "message" => "Erreur lors de la supprission"]);
            }
        } catch (Exception $e) {
            error_log("Error in delete: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Erreur serveur: " . $e->getMessage()]);
        }
    }

    public function checkNewTerrains() {
        // Check if user is logged in and is a manager
        if (!isset($_SESSION['user']) || 'gestionnaire' !== ($_SESSION['user']['role'] ?? null)) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        header('Content-Type: application/json');
        
        $terrainModel = new Terrain($_SESSION['user']['id']);
        echo $terrainModel->checkLastId();
        exit;
    }

    /**
     * Check for new available terrains (public endpoint for home page)
     */
    public function checkNewAvailableTerrains() {
        header('Content-Type: application/json');
        
        // Public endpoint - no authentication required
        $terrainModel = new Terrain();
        $result = $terrainModel->checkLastAvailableId();
        echo json_encode($result);
        exit;
    }

    public function getTerrainById($id) {
        if (!isset($_SESSION['user']) || 'gestionnaire' !== ($_SESSION['user']['role'] ?? null)) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        header('Content-Type: application/json');
        
        $terrainModel = new Terrain($_SESSION['user']['id']);
        $terrain = $terrainModel->getTerrainById($id);
        
        if ($terrain) {
            echo json_encode(['success' => true, 'terrain' => $terrain]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Terrain not found', 'removed' => true]);
        }
        exit;
    }
    
    public function getAvailableTerrainById($id) {
        // Public endpoint - no authentication required
        header('Content-Type: application/json');
        
        $terrainModel = new Terrain();
        $terrain = $terrainModel->getTerrainById($id);
        
        // Only return if terrain is available AND accepted
        if ($terrain && $terrain['statut'] === 'disponible' && $terrain['etat'] === 'acceptée') {
            echo json_encode(['success' => true, 'terrain' => $terrain]);
        } else {
            // Terrain not found, not available, or not accepted
            echo json_encode(['success' => false, 'message' => 'Terrain not available', 'removed' => true]);
        }
        exit;
    }

    /**
     * Update terrain information
     */
    public function updateTerrain($terrainId) {
        header('Content-Type: application/json');

        try {
            // Validate terrain ID
            if (!$terrainId || !is_numeric($terrainId)) {
                echo json_encode(['success' => false, 'message' => 'ID terrain invalide']);
                exit;
            }

            // Get posted form data
            $data = [
                'nom_terrain'    => htmlspecialchars($_POST['nom_terrain'] ?? ''),
                'localisation'   => htmlspecialchars($_POST['localisation'] ?? ''),
                'prix'           => htmlspecialchars($_POST['prix'] ?? ''),
                'type_terrain'   => htmlspecialchars($_POST['type_terrain'] ?? ''),
                'format_terrain' => htmlspecialchars($_POST['format_terrain'] ?? ''),
                'statut'         => htmlspecialchars($_POST['statut'] ?? ''),
                'heure_ouverture' => $_POST['heure_ouverture'] ?? null,
                'heure_fermeture' => $_POST['heure_fermeture'] ?? null,
                'options' => $_POST['options'] ?? []
            ];

            // Validate required fields
            foreach ($data as $key => $value) {
                if (empty($value) && $key !== 'heure_ouverture' && $key !== 'heure_fermeture' && $key !== 'options') {
                    echo json_encode(['success' => false, 'message' => "Le champ $key est requis"]);
                    exit;
                }
            }

            if (!isset($_SESSION['user']['id'])) {
                echo json_encode(['success' => false, 'message' => 'Utilisateur non authentifié']);
                exit;
            }

            // Handle image upload if provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $data['image'] = $this->uploadImage($_FILES['image']);
            }

            // Handle justificatifs upload if provided
            if (isset($_FILES['justificatifs']) && !empty($_FILES['justificatifs']['name'][0])) {
                $data['justificatifs'] = $this->uploadJustificatifs($_FILES['justificatifs']);
            }

            $terrain = new Terrain($_SESSION['user']['id']);

            if ($terrain->update($terrainId, $data)) {
                $updatedTerrain = $terrain->getTerrainById($terrainId);
                echo json_encode(['success' => true, 'terrain' => $updatedTerrain]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }

        } catch (Exception $e) {
            error_log("Error in updateTerrain: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Afficher les créneaux disponibles pour un terrain à une date donnée
     * Cette méthode est appelée par la vue et affiche les créneaux dans le modal
     * 
     * @param int $terrainId ID du terrain
     * @param string $date Date au format Y-m-d
     * @return array Données des créneaux
     */
    public function getCreneauxDisponibles($terrainId, $date = null) {
        $terrain = new Terrain();
        return $terrain->getCreneauxDisponibles($terrainId, $date);
    }
    
    /**
     * Point d'entrée pour la route /api/terrain/creneaux
     * Redirige vers la méthode reserver pour éliminer l'utilisation d'API/AJAX
     */
public function creneaux() {
    header('Content-Type: application/json');

    $terrainId = (int)($_GET['id'] ?? 0);
    $date = $_GET['date'] ?? date('Y-m-d');

    if (!$terrainId) {
        echo json_encode([
            'success' => false,
            'message' => 'ID du terrain manquant'
        ]);
        exit;
    }

    try {
        $terrainModel = new Terrain();
        $result = $terrainModel->getCreneauxDisponibles($terrainId, $date);
        
        // Retourner directement le résultat du modèle qui contient déjà
        // success, creneaux, heure_ouverture et heure_fermeture
        echo json_encode($result);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur : ' . $e->getMessage()
        ]);
    }
    exit;
}

    
    /**
     * Point d'entrée pour la route /api/terrain/options
     * Redirige vers la méthode reserver pour éliminer l'utilisation d'API/AJAX
     */
    public function options() {
        header('Content-Type: application/json');
        
        $terrainId = (int)($_GET['id'] ?? 0);
        
        if (!$terrainId) {
            echo json_encode([
                'success' => false,
                'message' => 'Paramètre id manquant'
            ]);
            exit;
        }
        
        try {
            $sql = "SELECT 
                        o.id_option,
                        o.nom_option,
                        o.description,
                        p.prix_option,
                        p.disponible
                    FROM options o
                    INNER JOIN posseder p ON o.id_option = p.id_option
                    WHERE p.id_terrain = :terrain_id
                    AND (p.disponible = 1 OR p.disponible IS NULL)
                    ORDER BY o.nom_option";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':terrain_id', $terrainId, PDO::PARAM_INT);
            $stmt->execute();
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'options' => $options
            ]);
            
        } catch (PDOException $e) {
            error_log("Erreur API options: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Afficher le formulaire de réservation avec les créneaux et options
     * Cette méthode remplace l'ancien système API/AJAX
     * 
     * @param int $terrainId ID du terrain à réserver
     * @param string $date Date sélectionnée (optionnelle)
     */
    public function reserver($terrainId = null, $date = null) {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        
        // Valider les paramètres
        $terrainId = (int)($terrainId ?: ($_GET['id'] ?? 0));
        $date = $date ?: ($_GET['date'] ?? date('Y-m-d'));
        
        if (!$terrainId) {
            $_SESSION['error'] = "ID du terrain manquant";
            header('Location: ' . BASE_URL . 'utilisateur/dashboard');
            exit;
        }
        
        // Récupérer les informations du terrain
        $terrainModel = new Terrain();
        $terrain = $terrainModel->getTerrainById($terrainId);
        
        if (!$terrain || $terrain['statut'] !== 'disponible' || $terrain['etat'] !== 'acceptée') {
            $_SESSION['error'] = "Ce terrain n'est pas disponible";
            header('Location: ' . BASE_URL . 'utilisateur/dashboard');
            exit;
        }
        
        // Récupérer les créneaux disponibles
        $creneaux = array_values($terrainModel->getCreneauxDisponibles($terrainId, $date));
        
        // Récupérer les options disponibles
        $optionsResult = $this->getOptionsForReservation($terrainId);
        $options = [];
        if ($optionsResult && isset($optionsResult['options'])) {
            $options = $optionsResult['options'];
        }
        
        // Stocker les données dans la session pour les récupérer dans la vue
        $_SESSION['reservation_data'] = [
            'terrain' => $terrain,
            'creneaux' => $creneaux,
            'options' => $options
        ];
        
        // Rediriger vers la page dashboard avec les paramètres
        $params = [
            'terrain_id' => $terrainId,
            'date' => $date,
            'open_modal' => '1'
        ];
        header('Location: ' . BASE_URL . 'utilisateur/dashboard?' . http_build_query($params));
        exit;
    }

    /**
     * Alias method for reserver() to match the view route
     * @param int $terrainId ID du terrain à réserver
     */
    public function reserverTerrain($terrainId = null) {
        $this->reserver($terrainId);
    }

    /**
     * Get options for reservation (internal method)
     */
    private function getOptionsForReservation($terrainId) {
        if (!$terrainId) {
            return ['success' => false, 'options' => []];
        }
        
        try {
            $sql = "SELECT 
                        o.id_option,
                        o.nom_option,
                        o.description,
                        p.prix_option,
                        p.disponible
                    FROM options o
                    INNER JOIN posseder p ON o.id_option = p.id_option
                    WHERE p.id_terrain = :terrain_id
                    AND (p.disponible = 1 OR p.disponible IS NULL)
                    ORDER BY o.nom_option";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':terrain_id', $terrainId, PDO::PARAM_INT);
            $stmt->execute();
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'options' => $options];
        } catch (PDOException $e) {
            error_log("Erreur getOptionsForReservation: " . $e->getMessage());
            return ['success' => false, 'options' => []];
        }
    }
}
