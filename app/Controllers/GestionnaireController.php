<?php

require_once __DIR__ . '/../Models/Gestionnaire.php';

class GestionnaireController extends Controller {
    
    /**
     * Submit gestionnaire demand with multiple terrains
     */
    public function submitDemand() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        header('Content-Type: application/json');
        
        try {
            // Validate personal information
            $requiredFields = ['nom', 'prenom', 'email', 'password'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['success' => false, 'message' => "Le champ $field est requis"]);
                    exit;
                }
            }
            
            // Validate password length
            if (strlen($_POST['password']) < 6) {
                echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères']);
                exit;
            }
            
            // Validate password confirmation
            if ($_POST['password'] !== $_POST['confirm_password']) {
                echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas']);
                exit;
            }
            
            // Check if email already exists
            $gestionnaireModel = new Gestionnaire();
            if ($gestionnaireModel->emailExists($_POST['email'])) {
                echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
                exit;
            }
            
            // Validate terrains data
            if (empty($_POST['terrains']) || !is_array($_POST['terrains'])) {
                echo json_encode(['success' => false, 'message' => 'Au moins un terrain est requis']);
                exit;
            }
            
            // Prepare gestionnaire data
            $gestionnaireData = [
                'nom' => trim($_POST['nom']),
                'prenom' => trim($_POST['prenom']),
                'email' => trim($_POST['email']),
                'telephone' => trim($_POST['telephone'] ?? ''),
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
            ];
            
            // Process terrains and upload justificatifs
            $terrains = [];
            foreach ($_POST['terrains'] as $index => $terrainData) {
                // Validate terrain required fields
                $requiredTerrainFields = ['nom_terrain', 'localisation', 'type_terrain', 'format_terrain', 'prix_heure'];
                foreach ($requiredTerrainFields as $field) {
                    if (empty($terrainData[$field])) {
                        echo json_encode(['success' => false, 'message' => "Terrain " . ($index + 1) . ": Le champ $field est requis"]);
                        exit;
                    }
                }
                
                // Handle file uploads for this terrain
                $justificatifPath = $this->uploadJustificatifs($index);
                
                if (!$justificatifPath) {
                    echo json_encode(['success' => false, 'message' => "Terrain " . ($index + 1) . ": Justificatifs requis"]);
                    exit;
                }
                
                $terrains[] = [
                    'nom_terrain' => trim($terrainData['nom_terrain']),
                    'localisation' => trim($terrainData['localisation']),
                    'type_terrain' => trim($terrainData['type_terrain']),
                    'format_terrain' => trim($terrainData['format_terrain']),
                    'prix_heure' => floatval($terrainData['prix_heure']),
                    'justificatif' => $justificatifPath,
                    'heure_ouverture' => $terrainData['heure_ouverture'] ?? null,
                    'heure_fermeture' => $terrainData['heure_fermeture'] ?? null,
                    'options' => $terrainData['options'] ?? []
                ];
            }
            
            // Create demand
            $demandId = $gestionnaireModel->createDemand($gestionnaireData, $terrains);
            
            // Send email notification to admin (optional)
            $this->sendAdminNotification($gestionnaireData, count($terrains));
            
            echo json_encode([
                'success' => true,
                'message' => 'Demande envoyée avec succès',
                'demand_id' => $demandId
            ]);
            
        } catch (Exception $e) {
            error_log("Error in submitDemand: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode([
                'success' => false, 
                'message' => 'Erreur serveur: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
        exit;
    }
    
    /**
     * Upload justificatifs for a terrain
     */
    private function uploadJustificatifs($terrainIndex) {
        $uploadDir = __DIR__ . '/../../public/uploads/justificatifs/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Check if files were uploaded for this terrain
        if (empty($_FILES['terrains']['name'][$terrainIndex]['justificatifs'])) {
            return null;
        }
        
        $uploadedFiles = [];
        $files = $_FILES['terrains'];
        
        // Handle multiple files for this terrain
        foreach ($files['name'][$terrainIndex]['justificatifs'] as $key => $filename) {
            if ($files['error'][$terrainIndex]['justificatifs'][$key] === UPLOAD_ERR_OK) {
                $tmpName = $files['tmp_name'][$terrainIndex]['justificatifs'][$key];
                $fileSize = $files['size'][$terrainIndex]['justificatifs'][$key];
                
                // Validate file size (10MB max)
                if ($fileSize > 10 * 1024 * 1024) {
                    continue;
                }
                
                // Validate file type
                $allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $tmpName);
                finfo_close($finfo);
                
                if (!in_array($mimeType, $allowedTypes)) {
                    continue;
                }
                
                // Generate unique filename
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $newFilename = uniqid('justif_' . $terrainIndex . '_') . '.' . $extension;
                $destination = $uploadDir . $newFilename;
                
                if (move_uploaded_file($tmpName, $destination)) {
                    $uploadedFiles[] = $newFilename;
                }
            }
        }
        
        // Return comma-separated list of uploaded files
        return !empty($uploadedFiles) ? implode(',', $uploadedFiles) : null;
    }
    
    /**
     * Send email notification to admin
     */
    private function sendAdminNotification($gestionnaireData, $terrainCount) {
        // TODO: Implement email sending
        // For now, just log it
        error_log("New gestionnaire demand: {$gestionnaireData['email']} with $terrainCount terrain(s)");
    }
    
    /**
     * Get all pending demands (Admin only)
     */
    public function getPendingDemands() {
        // Check if user is admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        
        $gestionnaireModel = new Gestionnaire();
        $demands = $gestionnaireModel->getPendingDemands();
        
        $this->view('admin/gestionnaire_demands', ['demands' => $demands]);
    }
    
    /**
     * View demand details (Admin only)
     */
    public function viewDemand($id) {
        // Check if user is admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        
        $gestionnaireModel = new Gestionnaire();
        $demand = $gestionnaireModel->getDemandById($id);
        
        if (!$demand) {
            $_SESSION['error'] = 'Demande introuvable';
            header('Location: ' . BASE_URL . 'gestionnaire/getPendingDemands');
            exit;
        }
        
        $this->view('admin/gestionnaire_demand_detail', ['demand' => $demand]);
    }
    
    /**
     * Approve demand (Admin only)
     */
    public function approveDemand($id) {
        header('Content-Type: application/json');
        
        // Check if user is admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }
        
        try {
            $gestionnaireModel = new Gestionnaire();
            $gestionnaireModel->approveDemand($id);
            
            // TODO: Send email notification to gestionnaire
            
            echo json_encode([
                'success' => true,
                'message' => 'Demande approuvée avec succès'
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Get all available options (AJAX endpoint)
     */
    public function getOptions() {
        header('Content-Type: application/json');
        
        try {
            $gestionnaireModel = new Gestionnaire();
            $options = $gestionnaireModel->getAllOptions();
            
            echo json_encode([
                'success' => true,
                'options' => $options
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la récupération des options'
            ]);
        }
        exit;
    }
}
