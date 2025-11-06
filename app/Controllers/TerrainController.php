<?php

require_once __DIR__ . '/../Models/Terrain.php';

class TerrainController extends Controller{

    private $db;
    private $uploadDir = __DIR__ . '/../../public/images/';

    public function __construct($db)
    {
        $this->db = $db;
        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

//    public function create()
//    {
//        require __DIR__ . '/../Views/gestionnaire/ajouter_terrain.php';
//    }

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

    public function store()
    {
        try {
            // Handle file upload
            $imagePath = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->uploadImage($_FILES['image']);
            } else {
                throw new Exception("Image is required");
            }

            // Get posted form data
            $data = [
                'nom_terrain'    => htmlspecialchars($_POST['nom_terrain'] ?? ''),
                'image'          => $imagePath,
                'prix'           => htmlspecialchars($_POST['prix'] ?? ''),
                'localisation'   => htmlspecialchars($_POST['localisation'] ?? ''),
                'type_terrain'   => htmlspecialchars($_POST['type_terrain'] ?? ''),
                'format_terrain' => htmlspecialchars($_POST['format_terrain'] ?? ''),
            ];

            // Validate required fields
            foreach ($data as $key => $value) {
                if (empty($value) && $key !== 'image') {
                    throw new Exception("All fields are required");
                }
            }

            // Get the logged-in user ID from session
            if (!isset($_SESSION['user']['id'])) {
                throw new Exception("User not authenticated");
            }
            
            $terrain = new Terrain($_SESSION['user']['id']);

            if ($terrain->create($data)) {
                $_SESSION['success'] = 'Terrain ajouté avec succès!';
                header('Location: ' . BASE_URL . 'dashboad_gestionnaire/index');
                exit();
            } else {
                throw new Exception("Failed to save terrain");
            }
        } catch (Exception $e) {
            // Handle error - you might want to pass the error to the view
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . 'terrain/create');
            exit();
        }
    }

    public function edit($id) {
        // Check if user is logged in and is a manager
        if (!isset($_SESSION['user']) || 'gestionnaire' !== ($_SESSION['user']['role'] ?? null)) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $terrainModel = new Terrain($_SESSION['user']['id']);
        $terrain = $terrainModel->getTerrainById($id);

        if (!$terrain) {
            $_SESSION['error'] = 'Terrain non trouvé';
            header('Location: ' . BASE_URL . 'terrain/gestionnaireTerrains');
            exit;
        }

        $this->view('gestionnaire/modifier_terrain', ['terrain' => $terrain, 'user' => $_SESSION['user']]);
    }

    public function updateTerrain($id) {
        // Check if user is logged in and is a manager
        if (!isset($_SESSION['user']) || 'gestionnaire' !== ($_SESSION['user']['role'] ?? null)) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        try {
            $data = [
                'nom_terrain'    => htmlspecialchars($_POST['nom_terrain'] ?? ''),
                'prix'           => htmlspecialchars($_POST['prix'] ?? ''),
                'localisation'   => htmlspecialchars($_POST['localisation'] ?? ''),
                'type_terrain'   => htmlspecialchars($_POST['type_terrain'] ?? ''),
                'format_terrain' => htmlspecialchars($_POST['format_terrain'] ?? ''),
                'statut'         => htmlspecialchars($_POST['statut'] ?? 'disponible'),
            ];

            // Handle file upload if new image is provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $data['image'] = $this->uploadImage($_FILES['image']);
            }

            // Validate required fields
            $required = ['nom_terrain', 'prix', 'localisation', 'type_terrain', 'format_terrain'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Tous les champs sont obligatoires.");
                }
            }

            $terrainModel = new Terrain($_SESSION['user']['id']);
            
            if ($terrainModel->update($id, $data)) {
                $_SESSION['success'] = 'Terrain modifié avec succès!';
                header('Location: ' . BASE_URL . 'terrain/gestionnaireTerrains');
                exit();
            } else {
                throw new Exception("Échec de la modification du terrain");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . 'terrain/edit/' . $id);
            exit();
        }
    }

    public function delete($id) {
        // Check if user is logged in and is a manager
        if (!isset($_SESSION['user']) || 'gestionnaire' !== ($_SESSION['user']['role'] ?? null)) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        try {
            $terrainModel = new Terrain($_SESSION['user']['id']);
            
            if ($terrainModel->delete($id)) {
                $_SESSION['success'] = 'Terrain supprimé avec succès!';
            } else {
                $_SESSION['error'] = 'Échec de la suppression du terrain';
            }
        } catch (PDOException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == '23000') {
                $_SESSION['error'] = 'Impossible de supprimer ce terrain car il a des réservations associées. Veuillez d\'abord supprimer ou annuler toutes les réservations.';
            } else {
                $_SESSION['error'] = 'Erreur lors de la suppression: ' . $e->getMessage();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ' . BASE_URL . 'terrain/gestionnaireTerrains');
        exit();
    }
}
