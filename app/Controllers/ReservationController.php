<?php
// Démarrer la mise en tampon de sortie pour éviter les problèmes d'envoi d'en-têtes
ob_start();

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/reservationTerrain.php';

class ReservationController {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../Core/Database.php';
        $this->db = \Database::getInstance()->getConnection();
    }

    public function index() {
        header('Location: ' . BASE_URL . 'utilisateur/mesReservations');
        exit;
    }

    // POST /reservation/create
public function create() {
    if (!isset($_SESSION['user'])) {
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . 'utilisateur/dashboard');
        exit;
    }

    // Déboguer les données reçues
    error_log("Données de réservation reçues: " . print_r($_POST, true));

    $userId = (int)($_SESSION['user']['id'] ?? 0);
    
    // Vérifier et créer l'entrée client si elle n'existe pas
    try {
        $checkClient = $this->db->prepare("SELECT id FROM client WHERE id = ?");
        $checkClient->execute([$userId]);
        if (!$checkClient->fetch()) {
            // Créer l'entrée client
            $insertClient = $this->db->prepare("INSERT INTO client (id) VALUES (?)");
            $insertClient->execute([$userId]);
            error_log("Entrée client créée pour l'utilisateur ID: $userId");
        }
    } catch (PDOException $e) {
        error_log("Erreur lors de la vérification/création du client: " . $e->getMessage());
    }
    
    error_log("User ID de la session: " . $userId);
    $terrainId = (int)($_POST['terrain_id'] ?? 0);
    $dateReservation = trim($_POST['date_reservation'] ?? '');
    $heureDebut = trim($_POST['heure_debut'] ?? '');
    $heureFin = trim($_POST['heure_fin'] ?? '');
    $commentaire = trim($_POST['commentaire'] ?? '');
    $options = isset($_POST['options']) && is_array($_POST['options']) ? $_POST['options'] : [];

    // Vérifier les données requises
    if (!$userId || !$terrainId || !$dateReservation || !$heureDebut || !$heureFin) {
        error_log("Données manquantes pour la réservation: userId=$userId, terrainId=$terrainId, date=$dateReservation, debut=$heureDebut, fin=$heureFin");
        $_SESSION['error'] = "Tous les champs requis doivent être remplis";
        header('Location: ' . BASE_URL . 'utilisateur/dashboard');
        exit;
    }

    // Normalisation format heure (HH:MM)
    $heureDebut = substr($heureDebut, 0, 5);
    $heureFin = substr($heureFin, 0, 5);

    $data = [
        'user_id' => $userId,
        'terrain_id' => $terrainId,
        'date_reservation' => $dateReservation,
        'heure_debut' => $heureDebut,
        'heure_fin' => $heureFin,
        'commentaire' => $commentaire,
        'options' => $options,
        'status' => 'en attente',
        'type' => 'normal'
    ];

    error_log("Données envoyées à createReservation: " . print_r($data, true));
    error_log("Options reçues: " . print_r($options, true));

    $reservationModel = new Reservation();
    $ok = $reservationModel->createReservation($data);

    error_log("Résultat de createReservation: " . ($ok ? "Succès" : "Échec"));

    if ($ok) {
        $_SESSION['success'] = "Réservation créée avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la création de la réservation. Le créneau peut être déjà réservé.";
        error_log("Échec de la création de la réservation: " . print_r($data, true));
    }

    // Rediriger vers Mes Réservations
    header('Location: ' . BASE_URL . 'utilisateur/mesReservations');
    exit;
}

    // GET /reservation/details?id=...
// GET /reservation/details?id=...
public function details() {
    if (!isset($_SESSION['user'])) {
        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            // S'assurer qu'aucun contenu n'a été envoyé avant les headers
            if (ob_get_length()) ob_clean();
            
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Non connecté']);
            exit;
        }
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }

    $reservationId = (int)($_GET['id'] ?? 0);
    if ($reservationId <= 0) {
        // S'assurer qu'aucun contenu n'a été envoyé avant les headers
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ID invalide']);
        exit;
    }
    
    // Log pour déboguer
    error_log("Début de details() pour reservation ID: $reservationId");

    $reservationModel = new Reservation();
    $reservation = $reservationModel->getReservationById($reservationId, $_SESSION['user']['id']);

    // Log des données de réservation
    error_log("Réservation récupérée: " . print_r($reservation, true));

    if (!$reservation) {
        // S'assurer qu'aucun contenu n'a été envoyé avant les headers
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Réservation non trouvée']);
        exit;
    }

    // Si format JSON est demandé, retourner les détails au format JSON
    if (isset($_GET['format']) && $_GET['format'] === 'json') {
        // S'assurer qu'aucun contenu n'a été envoyé avant les headers
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json');
        $html = $this->generateDetailsHTML($reservation);
        echo json_encode(['success' => true, 'html' => $html]);
        exit;
    }

    // Sinon, redirection classique
    header('Location: ' . BASE_URL . 'utilisateur/mesReservations');
    exit;
}

    
    /**
     * Génère le HTML pour le modal de détails
     */
   private function generateDetailsHTML($reservation) {
    // Log pour déboguer
    error_log("Début de generateDetailsHTML avec réservation: " . print_r($reservation, true));
    
    // Vérifier les clés disponibles
    $reservationId = isset($reservation['id_reservation']) ? $reservation['id_reservation'] : 
                    (isset($reservation['id']) ? $reservation['id'] : 0);
    
    error_log("ID de réservation utilisé: $reservationId");
    
    $prixTotal = (float)($reservation['prix_heure'] ?? 0);

    // Récupérer les options de la réservation
    $optionsData = $this->getReservationOptions($reservationId);
    error_log("Options récupérées: " . print_r($optionsData, true));

    $optionsHTML = '';
    if (!empty($optionsData)) {
        $optionsHTML = '<div class="options-list mt-4">
            <h6><i class="bi bi-plus-circle-fill me-2"></i>Options supplémentaires:</h6>
            <ul class="list-unstyled ps-2">';
        
        foreach ($optionsData as $option) {
            $optionsHTML .= '<li class="d-flex justify-content-between align-items-center mb-2">
                <span><i class="bi bi-check-circle-fill text-success me-2"></i>' . htmlspecialchars($option['nom_option']) . '</span>
                <span class="badge bg-light text-success px-3 py-2">+' . number_format((float)$option['prix_option'], 2) . ' MAD</span>
            </li>';
            $prixTotal += (float)$option['prix_option'];
        }

        $optionsHTML .= '</ul></div>';
    }

    // Vérifier le créneau horaire
    $creneauHoraire = '';
    if (!empty($reservation['heure_debut']) && !empty($reservation['heure_fin'])) {
        $creneauHoraire = $reservation['heure_debut'] . '-' . $reservation['heure_fin'];
    } elseif (!empty($reservation['creneau'])) {
        $creneauHoraire = $reservation['creneau'];
    } else {
        $creneauHoraire = 'Non spécifié';
    }
    
    error_log("Créneau horaire: $creneauHoraire");

    $statusClass = strtolower($reservation['statut'] ?? '') === 'acceptée' ? 'bg-success' : 
                 (strtolower($reservation['statut'] ?? '') === 'refusée' ? 'bg-danger' : 'bg-warning');

    // Préparer l'affichage du commentaire s'il existe
    $commentaireHTML = '';
    if (!empty($reservation['commentaire'])) {
        $commentaireHTML = '<div class="comment-section mt-3">
            <h6 class="comment-title"><i class="bi bi-chat-left-text me-2"></i>Commentaire:</h6>
            <div class="comment-content p-3">' . nl2br(htmlspecialchars($reservation['commentaire'])) . '</div>
        </div>';
    }

    return '<div class="reservation-details">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="position-relative">
                <img src="' . BASE_URL . 'images/' . htmlspecialchars($reservation['image'] ?? 'terrain.png') . '" 
                     class="card-img-top" alt="' . htmlspecialchars($reservation['terrain_nom'] ?? 'Terrain') . '" 
                     style="height: 250px; object-fit: cover;">
                <div class="position-absolute top-0 end-0 m-3">
                    <span class="badge ' . $statusClass . ' fs-6 px-3 py-2 rounded-pill shadow-sm">' . htmlspecialchars($reservation['statut'] ?? 'En attente') . '</span>
                </div>
            </div>
            
            <div class="card-body p-4">
                <h4 class="card-title fw-bold mb-3">' . htmlspecialchars($reservation['terrain_nom'] ?? 'Terrain') . '</h4>
                
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-geo-alt-fill text-primary me-2 fs-5"></i>
                    <span class="text-muted">' . htmlspecialchars($reservation['localisation'] ?? '') . '</span>
                </div>
                
                <div class="row g-4 mt-2">
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="info-label">Taille</div>
                            <div class="info-value">' . htmlspecialchars($reservation['format_terrain'] ?? '-') . '</div>
                        </div>
                        
                        <div class="info-group mt-3">
                            <div class="info-label">Type</div>
                            <div class="info-value">' . htmlspecialchars($reservation['type_terrain'] ?? '-') . '</div>
                        </div>
                        
                        <div class="info-group mt-3">
                            <div class="info-label">Contact</div>
                            <div class="info-value"><i class="bi bi-envelope me-1"></i>' . htmlspecialchars($reservation['email'] ?? $reservation['gestionnaire_email'] ?? '-') . '</div>
                        </div>
                        
                        <div class="info-group mt-3">
                            <div class="info-label">Prix horaire</div>
                            <div class="info-value fw-bold text-success">' . number_format((float)($reservation['prix_heure'] ?? 0), 2) . ' MAD</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="info-label">Date de réservation</div>
                            <div class="info-value"><i class="bi bi-calendar-date me-1"></i>' . htmlspecialchars($reservation['date_reservation'] ?? '-') . '</div>
                        </div>
                        
                        <div class="info-group mt-3">
                            <div class="info-label">Créneau horaire</div>
                            <div class="info-value"><i class="bi bi-clock me-1"></i>' . htmlspecialchars($creneauHoraire) . '</div>
                        </div>
                    </div>
                </div>
                
                ' . $optionsHTML . '
                ' . $commentaireHTML . '
                
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Fermer
                    </button>
                    <a href="' . BASE_URL . 'reservation/edit?id=' . $reservationId . '" 
                       class="btn btn-primary px-4 py-2">
                       <i class="bi bi-pencil me-2"></i>Modifier
                    </a>
                </div>
            </div>
        </div>
        
        <style>
            .comment-title {
                font-weight: 600;
                color: #064420;
                margin-bottom: 0.5rem;
            }
            .comment-content {
                background-color: #f8f9fa;
                border-radius: 8px;
                border-left: 4px solid #064420;
            }
            .info-group {
                margin-bottom: 0.5rem;
            }
            .info-label {
                font-size: 0.85rem;
                color: #6c757d;
                margin-bottom: 0.2rem;
            }
            .info-value {
                font-weight: 500;
            }
            .options-list {
                background-color: #f8f9fa;
                border-radius: 8px;
                padding: 1rem;
            }
            .options-list h6 {
                color: #064420;
                font-weight: 600;
                margin-bottom: 0.75rem;
            }
            .options-list ul li {
                margin-bottom: 0.5rem;
                padding-left: 0.5rem;
            }
        </style>
    </div>';
}

    
    /**
     * Récupère les options d'une réservation
     */
    private function getReservationOptions($reservationId) {
        error_log("getReservationOptions dans le contrôleur avec ID: $reservationId");
        
        // Vérifier directement dans la base de données
        try {
            require_once __DIR__ . '/../Core/Database.php';
            $db = \Database::getInstance()->getConnection();
            
            // Requête directe pour vérifier les options de la réservation
            // Vérifier si la table a une colonne commentaire
            try {
                $checkColumn = $db->query("SHOW COLUMNS FROM reservation_option LIKE 'commentaire'");
                $hasCommentaire = $checkColumn->rowCount() > 0;
            } catch (\PDOException $e) {
                $hasCommentaire = false;
            }
            
            if ($hasCommentaire) {
                $sql = "SELECT ro.id_reservation, ro.id_option, ro.commentaire, o.nom_option, o.prix_option 
                        FROM reservation_option ro
                        JOIN options o ON ro.id_option = o.id_option
                        WHERE ro.id_reservation = :id_reservation";
            } else {
                $sql = "SELECT ro.id_reservation, ro.id_option, '' as commentaire, o.nom_option, o.prix_option 
                        FROM reservation_option ro
                        JOIN options o ON ro.id_option = o.id_option
                        WHERE ro.id_reservation = :id_reservation";
            }
            
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_reservation', $reservationId, \PDO::PARAM_INT);
            $stmt->execute();
            
            $options = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            error_log("Options directement depuis la BD pour ID $reservationId: " . print_r($options, true));
            
            // Retourner un tableau vide si aucune option n'est trouvée (pas d'options hardcodées)
            return $options ?: [];
        } catch (\Exception $e) {
            error_log("Erreur dans getReservationOptions: " . $e->getMessage());
            // Retourner un tableau vide en cas d'erreur
            return [];
        }
    }
    

    // GET/POST /reservation/edit?id=...
    public function edit() {
        try {
            
            
            $reservationId = (int)($_GET['id'] ?? 0);
            if ($reservationId <= 0) {
                if (isset($_GET['format']) && $_GET['format'] === 'json') {
                    // S'assurer qu'aucun contenu n'a été envoyé avant les headers
                    if (ob_get_length()) ob_clean();
                    
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'ID invalide']);
                    exit;
                }
                header('Location: ' . BASE_URL . 'utilisateur/mesReservations');
                exit;
            }
            
            require_once __DIR__ . '/../Models/reservationTerrain.php';
            $reservationModel = new Reservation();
            
            // Vérifier si la réservation peut être modifiée (règle des 48h)
            $canModify = $reservationModel->canModifyReservation($reservationId, $_SESSION['user']['id']);
            
            if (!$canModify) {
                if (isset($_GET['format']) && $_GET['format'] === 'json') {
                    // S'assurer qu'aucun contenu n'a été envoyé avant les headers
                    if (ob_get_length()) ob_clean();
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false, 
                        'canModify' => false,
                        'message' => 'Cette réservation ne peut pas être modifiée (moins de 48h avant le début du match ou statut non modifiable)'
                    ]);
                    exit;
                }
                $_SESSION['error'] = 'Cette réservation ne peut pas être modifiée (moins de 48h avant le début du match ou statut non modifiable)';
                header('Location: ' . BASE_URL . 'utilisateur/mesReservations');
                exit;
            }
            
            // Récupérer les détails de la réservation
            $reservation = $reservationModel->getReservationById($reservationId, $_SESSION['user']['id']);
            
            if (!$reservation) {
                if (isset($_GET['format']) && $_GET['format'] === 'json') {
                    // S'assurer qu'aucun contenu n'a été envoyé avant les headers
                    if (ob_get_length()) ob_clean();
                    
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Réservation non trouvée']);
                    exit;
                }
                header('Location: ' . BASE_URL . 'utilisateur/mesReservations');
                exit;
            }
        } catch (\Exception $e) {
            error_log("Erreur dans edit(): " . $e->getMessage());
            if (isset($_GET['format']) && $_GET['format'] === 'json') {
                // S'assurer qu'aucun contenu n'a été envoyé avant les headers
                if (ob_get_length()) ob_clean();
                
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Erreur lors du chargement de la réservation: ' . $e->getMessage()]);
                exit;
            }
            $_SESSION['error'] = 'Erreur lors du chargement de la réservation';
            header('Location: ' . BASE_URL . 'utilisateur/mesReservations');
            exit;
        }
        
        // Traitement du formulaire POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Utiliser les valeurs des sélecteurs ou des champs cachés
            $heureDebut = isset($_POST['heure_debut_select']) ? $_POST['heure_debut_select'] : $_POST['heure_debut'];
            $heureFin = isset($_POST['heure_fin_select']) ? $_POST['heure_fin_select'] : $_POST['heure_fin'];
            
            $data = [
                'date_reservation' => $_POST['date_reservation'] ?? '',
                'heure_debut' => $heureDebut,
                'heure_fin' => $heureFin,
                'commentaire' => $_POST['commentaire'] ?? '',
                'nom' => $_POST['nom'] ?? '',
                'prenom' => $_POST['prenom'] ?? '',
                'email' => $_POST['email'] ?? '',
                'telephone' => $_POST['telephone'] ?? ''
            ];
            
            // Mettre à jour les informations utilisateur si elles ont changé
            if (isset($_SESSION['user']) && 
                ($_SESSION['user']['nom'] !== $data['nom'] || 
                 $_SESSION['user']['prenom'] !== $data['prenom'] || 
                 $_SESSION['user']['email'] !== $data['email'] || 
                 $_SESSION['user']['num_tel'] !== $data['telephone'])) {
                
                try {
                    $userId = $_SESSION['user']['id'];
                    $updateUserSql = "UPDATE utilisateur SET 
                                      nom = :nom, 
                                      prenom = :prenom, 
                                      email = :email, 
                                      num_tel = :telephone 
                                      WHERE id = :id";
                    $updateUserStmt = $this->db->prepare($updateUserSql);
                    $updateUserStmt->bindValue(':nom', $data['nom']);
                    $updateUserStmt->bindValue(':prenom', $data['prenom']);
                    $updateUserStmt->bindValue(':email', $data['email']);
                    $updateUserStmt->bindValue(':telephone', $data['telephone']);
                    $updateUserStmt->bindValue(':id', $userId, PDO::PARAM_INT);
                    $updateUserStmt->execute();
                    
                    // Mettre à jour la session
                    $_SESSION['user']['nom'] = $data['nom'];
                    $_SESSION['user']['prenom'] = $data['prenom'];
                    $_SESSION['user']['email'] = $data['email'];
                    $_SESSION['user']['num_tel'] = $data['telephone'];
                } catch (\Exception $e) {
                    error_log("Erreur lors de la mise à jour des informations utilisateur: " . $e->getMessage());
                }
            }
            
            // Récupérer les options sélectionnées
            $options = $_POST['options'] ?? [];
            $optionCommentaires = $_POST['commentaire_option'] ?? [];
            
            // Valider les données
            if (empty($data['date_reservation']) || empty($data['heure_debut']) || empty($data['heure_fin'])) {
                if (isset($_GET['format']) && $_GET['format'] === 'json') {
                    // S'assurer qu'aucun contenu n'a été envoyé avant les headers
                    if (ob_get_length()) ob_clean();
                    
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs requis']);
                    exit;
                }
                $_SESSION['error'] = 'Veuillez remplir tous les champs requis';
                header('Location: ' . BASE_URL . 'reservation/edit?id=' . $reservationId);
                exit;
            }

            // Vérification de disponibilité côté serveur
            $newCreneau = $data['heure_debut'] . '-' . $data['heure_fin'];
            $oldCreneau = $reservation['creneau'] ?? '';
            // Normaliser la comparaison (supprimer espaces)
            $newCreneauNorm = preg_replace('/\s+/', '', $newCreneau);
            $oldCreneauNorm = preg_replace('/\s+/', '', (string)$oldCreneau);
            $terrainIdForCheck = (int)($reservation['id_terrain'] ?? $_POST['terrain_id'] ?? 0);

            // Vérifier uniquement si le créneau a changé (normalisé)
            if ($newCreneauNorm !== $oldCreneauNorm) {
                $isAvailable = $reservationModel->checkAvailability($terrainIdForCheck, $data['date_reservation'], $newCreneau);
                if (!$isAvailable) {
                    if (isset($_GET['format']) && $_GET['format'] === 'json') {
                        if (ob_get_length()) ob_clean();
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'message' => "Le créneau sélectionné n'est plus disponible. Veuillez en choisir un autre."
                        ]);
                        exit;
                    }
                    $_SESSION['error'] = "Le créneau sélectionné n'est plus disponible. Veuillez en choisir un autre.";
                    header('Location: ' . BASE_URL . 'reservation/edit?id=' . $reservationId);
                    exit;
                }
            }
            
            // Démarrer une transaction
            $this->db->beginTransaction();
            
            try {
                // Mettre à jour la réservation
                $success = $reservationModel->updateReservation($reservationId, $_SESSION['user']['id'], $data);
                
                if ($success) {
                    // Supprimer les anciennes options
                    $reservationModel->deleteReservationOptions($reservationId);
                    
                    // Ajouter les nouvelles options
                    if (!empty($options)) {
                        $reservationModel->addReservationOptions($reservationId, $options, $optionCommentaires);
                    }
                    
                    // Valider la transaction
                    $this->db->commit();
                    
                    if (isset($_GET['format']) && $_GET['format'] === 'json') {
                        // S'assurer qu'aucun contenu n'a été envoyé avant les headers
                        if (ob_get_length()) ob_clean();
                        
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => 'Réservation mise à jour avec succès']);
                        exit;
                    }
                    $_SESSION['success'] = 'Réservation mise à jour avec succès';
                    header('Location: ' . BASE_URL . 'utilisateur/mesReservations');
                    exit;
                } else {
                    // Annuler la transaction
                    $this->db->rollBack();
                    
                    if (isset($_GET['format']) && $_GET['format'] === 'json') {
                        // S'assurer qu'aucun contenu n'a été envoyé avant les headers
                        if (ob_get_length()) ob_clean();
                        
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de la réservation']);
                        exit;
                    }
                    $_SESSION['error'] = 'Erreur lors de la mise à jour de la réservation';
                    header('Location: ' . BASE_URL . 'reservation/edit?id=' . $reservationId);
                    exit;
                }
            } catch (\Exception $e) {
                // Annuler la transaction en cas d'erreur
                $this->db->rollBack();
                
                error_log("Erreur lors de la mise à jour de la réservation: " . $e->getMessage());
                
                if (isset($_GET['format']) && $_GET['format'] === 'json') {
                    // S'assurer qu'aucun contenu n'a été envoyé avant les headers
                    if (ob_get_length()) ob_clean();
                    
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de la réservation']);
                    exit;
                }
                $_SESSION['error'] = 'Erreur lors de la mise à jour de la réservation';
                header('Location: ' . BASE_URL . 'reservation/edit?id=' . $reservationId);
                exit;
            }
        }
        
        // Si format JSON est demandé, retourner le formulaire au format JSON
        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            // S'assurer qu'aucun contenu n'a été envoyé avant les headers
            if (ob_get_length()) ob_clean();
            
            header('Content-Type: application/json');
            try {
                $html = $this->generateEditFormHTML($reservation);
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                error_log("Erreur lors de la génération du formulaire d'édition: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erreur lors du chargement du formulaire: ' . $e->getMessage(),
                    'html' => '<div class="alert alert-danger">Erreur lors du chargement du formulaire. Veuillez réessayer.</div>'
                ]);
            }
            exit;
        }
        
        // Sinon, afficher la vue de modification
        $this->render('reservation/edit', [
            'reservation' => $reservation,
            'baseUrl' => BASE_URL
        ]);
    }
    
    /**
     * Génère le HTML pour le formulaire de modification
     */
    private function generateEditFormHTML($reservation) {
        // Initialiser les variables
        $optionsData = [];
        $allOptions = [];
        $optionsHTML = '';
        
        error_log("generateEditFormHTML - Données réservation: " . print_r($reservation, true));
        
        try {
            // Récupérer les options de la réservation (celles déjà sélectionnées)
            // Handle both 'id_reservation' and 'id' keys
            $reservationIdForOptions = $reservation['id_reservation'] ?? $reservation['id'] ?? 0;
            error_log("Reservation ID pour options: $reservationIdForOptions");
            
            if ($reservationIdForOptions > 0) {
                $optionsData = $this->getReservationOptions($reservationIdForOptions);
                error_log("Options de la réservation récupérées: " . count($optionsData));
            } else {
                error_log("ATTENTION: Reservation ID invalide pour récupérer les options");
                $optionsData = [];
            }
            
            // Récupérer toutes les options disponibles pour ce terrain
            require_once __DIR__ . '/../Core/Database.php';
            $db = \Database::getInstance()->getConnection();
            
            $terrainId = (int)($reservation['id_terrain'] ?? 0);
            error_log("Terrain ID: $terrainId");
            
            if ($terrainId > 0) {
                try {
                    // Vérifier d'abord si le terrain existe
                    $checkTerrain = $db->prepare("SELECT id_terrain FROM terrain WHERE id_terrain = ?");
                    $checkTerrain->execute([$terrainId]);
                    if (!$checkTerrain->fetch()) {
                        error_log("Terrain $terrainId n'existe pas");
                        $allOptions = [];
                    } else {
                        $sql = "SELECT 
                                    o.id_option,
                                    o.nom_option,
                                    o.description,
                                    COALESCE(p.prix_option, o.prix_option, 0) as prix_option,
                                    p.disponible
                                FROM options o
                                INNER JOIN posseder p ON o.id_option = p.id_option
                                WHERE p.id_terrain = :terrain_id
                                AND (p.disponible = 1 OR p.disponible IS NULL)
                                ORDER BY o.nom_option";
                        
                        $stmt = $db->prepare($sql);
                        $stmt->bindValue(':terrain_id', $terrainId, PDO::PARAM_INT);
                        $stmt->execute();
                        $allOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        error_log("Options disponibles pour terrain $terrainId: " . count($allOptions));
                    }
                } catch (PDOException $e) {
                    error_log("Erreur lors de la récupération des options du terrain $terrainId: " . $e->getMessage());
                    error_log("Stack trace: " . $e->getTraceAsString());
                    $allOptions = [];
                }
            } else {
                error_log("Terrain ID invalide ou manquant dans la réservation. Clés disponibles: " . implode(', ', array_keys($reservation)));
                $allOptions = [];
            }
            
            // Créer un tableau des IDs d'options sélectionnées
            $selectedOptionIds = [];
            if (is_array($optionsData)) {
                foreach ($optionsData as $option) {
                    $selectedOptionIds[] = (int)($option['id_option'] ?? 0);
                }
            }
            
            // Générer le HTML pour les options
            $optionsHTML = '<div class="mb-4">
                <label class="form-label reservation-modal-label">Options supplémentaires</label>
                <div id="optionsList" class="options-list-reservation">';
            
            if (empty($allOptions)) {
                $optionsHTML .= '<div class="alert alert-info">Aucune option disponible pour ce terrain</div>';
            } else {
                foreach ($allOptions as $option) {
                    $optionId = (int)($option['id_option'] ?? 0);
                    $isChecked = in_array($optionId, $selectedOptionIds) ? 'checked' : '';
                    $commentValue = '';
                    
                    // Rechercher le commentaire pour cette option si elle est sélectionnée
                    if (is_array($optionsData)) {
                        foreach ($optionsData as $selectedOption) {
                            if ((int)($selectedOption['id_option'] ?? 0) == $optionId && isset($selectedOption['commentaire'])) {
                                $commentValue = htmlspecialchars($selectedOption['commentaire']);
                                break;
                            }
                        }
                    }
                    
                    $optionsHTML .= '<div class="option-item-reservation">
                        <div class="option-header">
                            <input type="checkbox" 
                                   id="opt_' . $optionId . '" 
                                   name="options[]" 
                                   value="' . $optionId . '" 
                                   data-price="' . ($option['prix_option'] ?? 0) . '" 
                                   onchange="calculateTotalPrice()" 
                                   ' . $isChecked . '>
                            <label for="opt_' . $optionId . '">' . htmlspecialchars($option['nom_option'] ?? '') . '</label>
                            <span class="option-price">+' . number_format((float)($option['prix_option'] ?? 0), 2) . ' MAD</span>
                        </div>';
                    
                    if (!empty($option['description'])) {
                        $optionsHTML .= '<div class="option-description">' . htmlspecialchars($option['description']) . '</div>';
                    }
                    
                    $optionsHTML .= '</div>';
                }
            }
            
            $optionsHTML .= '</div></div>';
        } catch (\Exception $e) {
            error_log("Erreur dans generateEditFormHTML: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            error_log("Données réservation au moment de l'erreur: " . print_r($reservation, true));
            
            // En cas d'erreur, essayer de récupérer les options de manière plus simple
            try {
                $terrainId = (int)($reservation['id_terrain'] ?? 0);
                if ($terrainId > 0) {
                    require_once __DIR__ . '/../Core/Database.php';
                    $db = \Database::getInstance()->getConnection();
                    
                    // Requête simplifiée sans JOIN complexe
                    $sql = "SELECT o.id_option, o.nom_option, o.description, o.prix_option, 1 as disponible
                            FROM options o
                            INNER JOIN posseder p ON o.id_option = p.id_option
                            WHERE p.id_terrain = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$terrainId]);
                    $allOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($allOptions)) {
                        // Si on a réussi à récupérer les options, continuer normalement
                        $optionsHTML = '<div class="mb-4">
                            <label class="form-label reservation-modal-label">Options supplémentaires</label>
                            <div id="optionsList" class="options-list-reservation">';
                        
                        $selectedOptionIds = [];
                        if (is_array($optionsData)) {
                            foreach ($optionsData as $opt) {
                                $selectedOptionIds[] = (int)($opt['id_option'] ?? 0);
                            }
                        }
                        
                        foreach ($allOptions as $option) {
                            $optionId = (int)($option['id_option'] ?? 0);
                            $isChecked = in_array($optionId, $selectedOptionIds) ? 'checked' : '';
                            $optionsHTML .= '<div class="option-item-reservation">
                                <div class="option-header">
                                    <input type="checkbox" 
                                           id="opt_' . $optionId . '" 
                                           name="options[]" 
                                           value="' . $optionId . '" 
                                           data-price="' . ($option['prix_option'] ?? 0) . '" 
                                           onchange="calculateTotalPrice()" 
                                           ' . $isChecked . '>
                                    <label for="opt_' . $optionId . '">' . htmlspecialchars($option['nom_option'] ?? '') . '</label>
                                    <span class="option-price">+' . number_format((float)($option['prix_option'] ?? 0), 2) . ' MAD</span>
                                </div>';
                            if (!empty($option['description'])) {
                                $optionsHTML .= '<div class="option-description">' . htmlspecialchars($option['description']) . '</div>';
                            }
                            $optionsHTML .= '</div>';
                        }
                        
                        $optionsHTML .= '</div></div>';
                    } else {
                        $optionsHTML = '<div class="mb-4">
                            <label class="form-label reservation-modal-label">Options supplémentaires</label>
                            <div id="optionsList" class="options-list-reservation">
                                <div class="alert alert-info">Aucune option disponible pour ce terrain</div>
                            </div>
                        </div>';
                    }
                } else {
                    throw new \Exception("Terrain ID manquant");
                }
            } catch (\Exception $e2) {
                error_log("Erreur dans le fallback: " . $e2->getMessage());
                // En cas d'erreur, afficher un message mais continuer avec un tableau vide
                $optionsHTML = '<div class="mb-4">
                    <label class="form-label reservation-modal-label">Options supplémentaires</label>
                    <div id="optionsList" class="options-list-reservation">
                        <div class="alert alert-info">Aucune option disponible pour ce terrain</div>
                    </div>
                </div>';
                $optionsData = [];
            }
        }
        
        // Calculer le prix total
        $prixTotal = (float)($reservation['prix_heure'] ?? 0);
        
        // Déterminer les valeurs initiales d'heure début/fin
        $heureDebutVal = $reservation['heure_debut'] ?? '';
        $heureFinVal = $reservation['heure_fin'] ?? '';
        if ((empty($heureDebutVal) || empty($heureFinVal)) && !empty($reservation['creneau'])) {
            $parts = explode('-', $reservation['creneau']);
            if (count($parts) === 2) {
                $heureDebutVal = trim($parts[0]);
                $heureFinVal = trim($parts[1]);
            }
        }
        
        // Ajouter le prix des options sélectionnées
        if (is_array($optionsData) && !empty($optionsData)) {
            foreach ($optionsData as $option) {
                $prixTotal += (float)($option['prix_option'] ?? 0);
            }
        }
        
        // Générer le HTML du formulaire de modification
        $reservationId = $reservation['id_reservation'] ?? $reservation['id'] ?? 0;
        $actionUrl = BASE_URL . 'reservation/edit?id=' . $reservationId;
        $html = '<form id="editReservationForm" method="POST" action="' . $actionUrl . '">
            <input type="hidden" name="terrain_id" id="terrain_id" value="' . htmlspecialchars($reservation['id_terrain'] ?? 0) . '">
            <input type="hidden" name="prix_heure" id="prix_heure" value="' . htmlspecialchars($reservation['prix_heure'] ?? 0) . '">
            
            <!-- Date de réservation -->
            <div class="mb-4">
                <label class="form-label reservation-modal-label">Date de réservation *</label>
                <input type="date" class="form-control reservation-modal-input" name="date_reservation" id="date_reservation" value="' . htmlspecialchars($reservation['date_reservation'] ?? '') . '" required>
            </div>
            
            <!-- Créneaux disponibles -->
            <div class="mb-4">
                <label class="form-label reservation-modal-label">Créneaux horaires disponibles *</label>
                <div id="heuresInfo" class="alert alert-secondary" style="display: none; margin-bottom: 10px;">
                  <strong>Heures d\'ouverture :</strong> <span id="heure_ouverture_display"></span>
                  <strong style="margin-left: 15px;">Heures de fermeture :</strong> <span id="heure_fermeture_display"></span>
                </div>
                <div id="creneauxList" class="alert alert-info" data-current-start="' . $heureDebutVal . '" data-current-end="' . $heureFinVal . '">
                  Veuillez sélectionner une date pour voir les créneaux disponibles
                </div>
                <input type="hidden" name="heure_debut" id="heure_debut" value="' . $heureDebutVal . '" required>
                <input type="hidden" name="heure_fin" id="heure_fin" value="' . $heureFinVal . '" required>
            </div>
            
            <!-- Options supplémentaires -->
            ' . $optionsHTML . '
            
            <!-- Commentaire général -->
            <div class="mb-4">
                <label class="form-label reservation-modal-label">Commentaire (optionnel)</label>
                <textarea class="form-control reservation-modal-input" name="commentaire" id="commentaire" rows="3" placeholder="Ajoutez des précisions pour votre réservation...">' . htmlspecialchars($reservation['commentaire'] ?? '') . '</textarea>
            </div>
            
            <!-- Informations du client -->
            <div class="mb-4">
                <label class="form-label reservation-modal-label">Informations du client</label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control reservation-modal-input" name="nom" 
                               placeholder="Nom *" value="' . htmlspecialchars($_SESSION['user']['nom'] ?? '') . '">
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control reservation-modal-input" name="prenom" 
                               placeholder="Prénom *" value="' . htmlspecialchars($_SESSION['user']['prenom'] ?? '') . '">
                    </div>
                    <div class="col-md-6">
                        <input type="email" class="form-control reservation-modal-input" name="email" 
                               placeholder="Email *" value="' . htmlspecialchars($_SESSION['user']['email'] ?? '') . '">
                    </div>
                    <div class="col-md-6">
                        <input type="tel" class="form-control reservation-modal-input" name="telephone" 
                               placeholder="Téléphone *" value="' . htmlspecialchars($_SESSION['user']['num_tel'] ?? '') . '">
                    </div>
                </div>
            </div>
            
            <!-- Boutons -->
            <div class="d-flex gap-3">
                <button type="button" class="btn-reservation-modal-cancel" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn-reservation-modal-confirm">Confirmer les modifications</button>
            </div>
        </form>';
        return $html;
    }
}