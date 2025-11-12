<?php

require_once __DIR__ . '/../Core/Controller.php';

class TournoiController extends Controller {
    
    /**
     * Gestionnaire - liste des tournois
     */
    public function index() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $role = $_SESSION['user']['role'] ?? null;

        if ($role !== 'gestionnaire') {
            if ($role === 'utilisateur') {
                header('Location: ' . BASE_URL . 'tournoi/mesDemandes');
            } else {
                header('Location: ' . BASE_URL);
            }
            exit;
        }

        require_once __DIR__ . '/../Models/Tournoi.php';
        require_once __DIR__ . '/../Models/terrain.php';

        $gestionnaireId = $_SESSION['user']['id'];
        $tournoiModel = new Tournoi($gestionnaireId);
        $terrainModel = new Terrain($gestionnaireId);

        $tournois = $tournoiModel->getForGestionnaire();
        $demandes = $tournoiModel->getDemandesForGestionnaire();
        $terrains = $terrainModel->getGestionnaireTerrains();

        $activeSection = $_GET['section'] ?? 'tournois';
        $activeSection = in_array($activeSection, ['tournois', 'demandes'], true) ? $activeSection : 'tournois';

        $this->view('gestionnaire/tournois', [
            'user' => $_SESSION['user'],
            'tournois' => $tournois,
            'terrains' => $terrains,
            'demandes' => $demandes,
            'activeSection' => $activeSection
        ]);
    }

    /**
     * Afficher le formulaire de création/demande de tournoi
     */
    public function create() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $role = $_SESSION['user']['role'] ?? null;

        // Gestionnaire : création de tournoi
        if ($role === 'gestionnaire') {
            return $this->createTournoiGestionnaire();
        }

        // Client : afficher le formulaire de demande
        if ($role === 'utilisateur') {
            return $this->createDemandeTournoi();
        }

        header('Location: ' . BASE_URL);
        exit;
    }

    /**
     * Création de tournoi par gestionnaire
     */
    private function createTournoiGestionnaire() {
        require_once __DIR__ . '/../Models/Tournoi.php';
        require_once __DIR__ . '/../Models/terrain.php';

        $gestionnaireId = $_SESSION['user']['id'];
        $terrainModel = new Terrain($gestionnaireId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $required = ['nom_tournoi', 'date_debut', 'date_fin', 'nb_equipes'];
                foreach ($required as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Le champ '$field' est requis");
                    }
                }

                if (strtotime($_POST['date_debut']) > strtotime($_POST['date_fin'])) {
                    throw new Exception("La date de fin doit être après la date de début");
                }

                $tournoiModel = new Tournoi($gestionnaireId);
                $data = [
                    'nom_tournoi' => htmlspecialchars($_POST['nom_tournoi'] ?? ''),
                    'slogan' => htmlspecialchars($_POST['slogan'] ?? ''),
                    'date_debut' => $_POST['date_debut'],
                    'date_fin' => $_POST['date_fin'],
                    'nb_equipes' => (int)$_POST['nb_equipes'],
                    'prixPremiere' => htmlspecialchars($_POST['prixPremiere'] ?? ''),
                    'prixDeuxieme' => htmlspecialchars($_POST['prixDeuxieme'] ?? ''),
                    'prixTroisieme' => htmlspecialchars($_POST['prixTroisieme'] ?? '')
                ];

                $tournoiId = $tournoiModel->create($data);
                if (!$tournoiId) {
                    throw new Exception("Erreur lors de la création du tournoi");
                }

                $_SESSION['success'] = 'Tournoi créé avec succès !';
                header('Location: ' . BASE_URL . 'tournoi/manage/' . $tournoiId);
                exit;

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . 'tournoi/create');
                exit;
            }
        }

        $terrains = $terrainModel->getGestionnaireTerrains();
        $formData = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);

        $this->view('gestionnaire/creer_tournoi', [
            'user' => $_SESSION['user'],
            'terrains' => $terrains,
            'formData' => $formData,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
    }

    /**
     * Affichage du formulaire de demande de tournoi par client
     */
    private function createDemandeTournoi() {
        require_once __DIR__ . '/../Models/terrain.php';
        
        $terrainModel = $this->model('Terrain');
        $terrains = $terrainModel->getAvailableTerrains();

        $this->view('utilisateur/demande_tournoi', [
            'user' => $_SESSION['user'],
            'terrains' => $terrains,
            'gestionnaires' => $this->getGestionnairesWithTerrains()
        ]);
    }

    /**
     * API: Récupérer les terrains d'un gestionnaire
     */
    public function getTerrainsGestionnaire() {
        header('Content-Type: application/json');

        if (!isset($_GET['gestionnaire_id'])) {
            echo json_encode(['success' => false, 'message' => 'ID gestionnaire manquant']);
            exit;
        }

        $gestionnaireId = (int)$_GET['gestionnaire_id'];
        
        try {
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT id_terrain, nom_terrain, type_terrain, format_terrain, localisation, prix_heure, image
                    FROM terrain 
                    WHERE id_gestionnaire = ? 
                    AND statut = 'disponible' 
                    AND etat = 'acceptée'
                    ORDER BY nom_terrain";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$gestionnaireId]);
            $terrains = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'terrains' => $terrains
            ]);

        } catch (PDOException $e) {
            error_log("Erreur getTerrainsGestionnaire: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
        exit;
    }

    /**
     * Soumettre une demande de tournoi (par client)
     */
    public function submitDemande() {
        header('Content-Type: application/json');

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'utilisateur') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        try {
            // Validation des données
            $required = ['nom_tournoi', 'date_debut', 'date_fin', 'nb_equipes', 'gestionnaire_id', 'terrain_id', 'creneaux', 'equipes'];
            
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['success' => false, 'message' => "Le champ $field est requis"]);
                    exit;
                }
            }

            // Vérifier que les dates sont cohérentes
            if (strtotime($_POST['date_debut']) > strtotime($_POST['date_fin'])) {
                echo json_encode(['success' => false, 'message' => 'La date de fin doit être après la date de début']);
                exit;
            }

            // Vérifier que les dates sont dans le futur
            if (strtotime($_POST['date_debut']) < strtotime('today')) {
                echo json_encode(['success' => false, 'message' => 'La date de début doit être dans le futur']);
                exit;
            }

            // Décoder les créneaux et équipes
            $creneaux = json_decode($_POST['creneaux'], true);
            $equipes = json_decode($_POST['equipes'], true);

            if (empty($creneaux) || !is_array($creneaux)) {
                echo json_encode(['success' => false, 'message' => 'Au moins un créneau est requis']);
                exit;
            }

            if (empty($equipes) || count($equipes) != (int)$_POST['nb_equipes']) {
                echo json_encode(['success' => false, 'message' => 'Toutes les équipes doivent être renseignées']);
                exit;
            }

            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            // Insérer le tournoi
            $sql = "INSERT INTO tournoi (
                        nom_tournoi, slogan, date_debut, date_fin, 
                        nb_equipes, prixPremiere, prixDeuxieme, prixTroisieme,
                        id_gestionnaire, status
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'en attente')";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $_POST['nom_tournoi'],
                $_POST['slogan'] ?? '',
                $_POST['date_debut'],
                $_POST['date_fin'],
                (int)$_POST['nb_equipes'],
                $_POST['prix_premiere'] ?? 'Trophée',
                $_POST['prix_deuxieme'] ?? 'Médaille',
                $_POST['prix_troisieme'] ?? 'Médaille',
                (int)$_POST['gestionnaire_id']
            ]);

            $tournoiId = $db->lastInsertId();
            $terrainId = (int)$_POST['terrain_id'];
            $userId = (int)$_SESSION['user']['id'];
            
            // Vérifier et créer l'entrée dans la table client si elle n'existe pas
            $sqlCheckClient = "SELECT id FROM client WHERE id = ?";
            $stmtCheckClient = $db->prepare($sqlCheckClient);
            $stmtCheckClient->execute([$userId]);
            $clientExists = $stmtCheckClient->fetch();
            
            if (!$clientExists) {
                $sqlInsertClient = "INSERT INTO client (id) VALUES (?)";
                $stmtInsertClient = $db->prepare($sqlInsertClient);
                $stmtInsertClient->execute([$userId]);
            }
            
            $clientId = $userId;

            // Créer les équipes et les lier au tournoi
            foreach ($equipes as $equipeData) {
                $sqlEquipe = "INSERT INTO equipe (nom_equipe, nbr_joueurs, liste_joueurs, id_client) 
                             VALUES (?, ?, ?, ?)";
                $stmtEquipe = $db->prepare($sqlEquipe);
                $listeJoueursJson = json_encode($equipeData['liste_joueurs'] ?? []);
                $stmtEquipe->execute([
                    $equipeData['nom_equipe'],
                    (int)$equipeData['nbr_joueurs'],
                    $listeJoueursJson,
                    $clientId
                ]);
                
                $equipeId = $db->lastInsertId();
                
                // Lier l'équipe au tournoi
                $sqlParticipation = "INSERT INTO participation (id_tournoi, id_equipe) VALUES (?, ?)";
                $stmtParticipation = $db->prepare($sqlParticipation);
                $stmtParticipation->execute([$tournoiId, $equipeId]);
            }

            // Créer les réservations pour chaque créneau
            foreach ($creneaux as $creneau) {
                $sqlReservation = "INSERT INTO reservation (
                    date_reservation, creneau, status, type, 
                    commentaire, id_terrain, id_client, id_tournoi
                ) VALUES (?, ?, 'en attente', 'tournoi', ?, ?, ?, ?)";
                
                $stmtReservation = $db->prepare($sqlReservation);
                $commentaire = "Réservation pour le tournoi '{$_POST['nom_tournoi']}' (ID: $tournoiId) - Match: {$creneau['heure_debut']} à {$creneau['heure_fin']}";
                
                $stmtReservation->execute([
                    $creneau['date'],
                    $creneau['heure_debut'],
                    $commentaire,
                    $terrainId,
                    $clientId,
                    $tournoiId
                ]);
                
                $reservationId = $db->lastInsertId();
                
                // Créer la demande
                $sqlDemande = "INSERT INTO demande (id_tournoi, id_client, id_reservation, statut) 
                              VALUES (?, ?, ?, 'en attente')";
                $stmtDemande = $db->prepare($sqlDemande);
                $stmtDemande->execute([$tournoiId, $clientId, $reservationId]);
            }

            $db->commit();

            // Envoyer notification au gestionnaire
            $this->sendNotificationGestionnaire($_POST['gestionnaire_id'], $tournoiId);

            echo json_encode([
                'success' => true,
                'message' => 'Demande de tournoi envoyée avec succès !',
                'tournoi_id' => $tournoiId
            ]);

        } catch (Exception $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            error_log("Erreur submitDemande: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Envoyer notification au gestionnaire
     */
    private function sendNotificationGestionnaire($gestionnaireId, $tournoiId) {
        // TODO: Implémenter l'envoi d'email ou notification push
        error_log("Notification envoyée au gestionnaire $gestionnaireId pour le tournoi $tournoiId");
    }

    /**
     * Récupérer les gestionnaires avec terrains
     */
    private function getGestionnairesWithTerrains() {
        try {
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT DISTINCT 
                        g.id AS gestionnaire_id,
                        u.nom,
                        u.prenom,
                        u.email,
                        u.num_tel,
                        COUNT(DISTINCT t.id_terrain) AS nombre_terrains
                    FROM gestionnaire g
                    INNER JOIN utilisateur u ON g.id = u.id
                    INNER JOIN terrain t ON g.id = t.id_gestionnaire
                    WHERE g.status = 'accepté'
                    AND t.statut = 'disponible'
                    AND t.etat = 'acceptée'
                    GROUP BY g.id
                    HAVING nombre_terrains > 0
                    ORDER BY u.nom, u.prenom";
            
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Erreur getGestionnairesWithTerrains: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Voir les demandes de tournoi du client
     */
    public function mesDemandes() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'utilisateur') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT DISTINCT
                        t.*,
                        u.nom AS gestionnaire_nom,
                        u.prenom AS gestionnaire_prenom,
                        COUNT(DISTINCT r.id_terrain) AS nombre_terrains
                    FROM tournoi t
                    INNER JOIN demande d ON t.id_tournoi = d.id_tournoi
                    INNER JOIN gestionnaire g ON t.id_gestionnaire = g.id
                    INNER JOIN utilisateur u ON g.id = u.id
                    LEFT JOIN reservation r ON t.id_tournoi = r.id_tournoi
                    WHERE d.id_client = ?
                    GROUP BY t.id_tournoi
                    ORDER BY t.date_debut DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$_SESSION['user']['id']]);
            $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->view('utilisateur/mes_demandes_tournoi', [
                'user' => $_SESSION['user'],
                'demandes' => $demandes
            ]);

        } catch (PDOException $e) {
            error_log("Erreur mesDemandes: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors du chargement des demandes';
            header('Location: ' . BASE_URL . 'dashboard/utilisateur');
            exit;
        }
    }

    /**
     * Gestionnaire: Voir les demandes de tournoi
     */
    public function demandesGestionnaire() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'gestionnaire') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        header('Location: ' . BASE_URL . 'tournoi?section=demandes');
        exit;
    }

    /**
     * Accepter/Refuser une demande de tournoi (Gestionnaire)
     */
    public function updateStatus() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'gestionnaire') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        if (!isset($_POST['tournoi_id']) || !isset($_POST['status'])) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            exit;
        }

        $tournoiId = (int)$_POST['tournoi_id'];
        $newStatus = $_POST['status'];

        if (!in_array($newStatus, ['accepté', 'refusé'])) {
            echo json_encode(['success' => false, 'message' => 'Statut invalide']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            // Vérifier que ce tournoi appartient bien à ce gestionnaire
            $sql = "SELECT id_tournoi FROM tournoi WHERE id_tournoi = ? AND id_gestionnaire = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$tournoiId, $_SESSION['user']['id']]);
            
            if (!$stmt->fetch()) {
                throw new Exception('Tournoi non trouvé ou accès non autorisé');
            }

            // Mettre à jour le statut du tournoi
            $sql = "UPDATE tournoi SET status = ? WHERE id_tournoi = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$newStatus, $tournoiId]);

            // Mettre à jour le statut des réservations associées
            $reservationStatus = $newStatus === 'accepté' ? 'accepté' : 'refusé';
            $sql = "UPDATE reservation r
                    INNER JOIN demande d ON r.id_reservation = d.id_reservation
                    SET r.status = ?
                    WHERE d.id_tournoi = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$reservationStatus, $tournoiId]);

            // Mettre à jour le statut dans la table demande
            $sql = "UPDATE demande SET statut = ? WHERE id_tournoi = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$newStatus === 'accepté' ? 'confirmée' : 'annulée', $tournoiId]);

            $db->commit();

            echo json_encode([
                'success' => true,
                'message' => $newStatus === 'accepté' ? 'Tournoi accepté' : 'Tournoi refusé'
            ]);

        } catch (Exception $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            error_log("Erreur updateStatus: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Gestionnaire : gérer un tournoi (visualisation des matches)
     */
    public function manage($id) {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) !== 'gestionnaire') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        require_once __DIR__ . '/../Models/Tournoi.php';
        require_once __DIR__ . '/../Models/terrain.php';

        $gestionnaireId = $_SESSION['user']['id'];
        $tournoiModel = new Tournoi($gestionnaireId);
        $terrainModel = new Terrain($gestionnaireId);

        $tournoi = $tournoiModel->getByIdForGestionnaire($id);

        if (!$tournoi) {
            $_SESSION['error'] = 'Tournoi non trouvé';
            header('Location: ' . BASE_URL . 'tournoi');
            exit;
        }

        $reservations = $tournoiModel->getReservationsForTournoi($id);
        $terrains = $terrainModel->getGestionnaireTerrains();

        $this->view('gestionnaire/gerer_tournoi', [
            'user' => $_SESSION['user'],
            'tournoi' => $tournoi,
            'reservations' => $reservations,
            'terrains' => $terrains
        ]);
    }

    /**
     * Gestionnaire : ajouter un match au tournoi
     */
    public function addMatch($id) {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) !== 'gestionnaire') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'tournoi/manage/' . $id);
            exit;
        }

        try {
            if (empty($_POST['id_terrain']) || empty($_POST['date_reservation']) || empty($_POST['creneau'])) {
                throw new Exception("Tous les champs sont requis");
            }

            require_once __DIR__ . '/../Models/Tournoi.php';
            $tournoiModel = new Tournoi($_SESSION['user']['id']);

            $data = [
                'id_terrain' => (int)$_POST['id_terrain'],
                'date_reservation' => $_POST['date_reservation'],
                'creneau' => $_POST['creneau'],
                'commentaire' => htmlspecialchars($_POST['commentaire'] ?? '')
            ];

            if ($tournoiModel->createReservationForTournoi($id, $data)) {
                $_SESSION['success'] = 'Match ajouté au tournoi avec succès !';
            } else {
                throw new Exception("Erreur lors de l'ajout du match");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ' . BASE_URL . 'tournoi/manage/' . $id);
        exit;
    }

    /**
     * Gestionnaire : supprimer un match du tournoi
     */
    public function deleteMatch($tournoiId, $reservationId) {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) !== 'gestionnaire') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        try {
            require_once __DIR__ . '/../Models/Tournoi.php';
            $tournoiModel = new Tournoi($_SESSION['user']['id']);

            if ($tournoiModel->deleteReservationForTournoi($reservationId, $tournoiId)) {
                $_SESSION['success'] = 'Match supprimé avec succès !';
            } else {
                throw new Exception("Erreur lors de la suppression du match");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ' . BASE_URL . 'tournoi/manage/' . $tournoiId);
        exit;
    }

    /**
     * Voir les détails d'un tournoi (Gestionnaire)
     */
    public function details($tournoiId = null) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'gestionnaire') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        if (!$tournoiId) {
            $tournoiId = $_GET['id'] ?? null;
        }

        if (!$tournoiId) {
            $_SESSION['error'] = 'ID tournoi manquant';
            header('Location: ' . BASE_URL . 'tournoi?section=demandes');
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();
            
            // Récupérer les détails du tournoi
            $sql = "SELECT t.*,
                        u.nom AS client_nom,
                        u.prenom AS client_prenom,
                        u.email AS client_email,
                        u.num_tel AS client_tel
                    FROM tournoi t
                    INNER JOIN demande d ON t.id_tournoi = d.id_tournoi
                    INNER JOIN utilisateur u ON d.id_client = u.id
                    WHERE t.id_tournoi = ? AND t.id_gestionnaire = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$tournoiId, $_SESSION['user']['id']]);
            $tournoi = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$tournoi) {
                $_SESSION['error'] = 'Tournoi non trouvé';
                header('Location: ' . BASE_URL . 'tournoi?section=demandes');
                exit;
            }

            // Récupérer les équipes participantes
            $sqlEquipes = "SELECT e.*, p.id_tournoi
                          FROM equipe e
                          INNER JOIN participation p ON e.id_equipe = p.id_equipe
                          WHERE p.id_tournoi = ?
                          ORDER BY e.nom_equipe";
            $stmtEquipes = $db->prepare($sqlEquipes);
            $stmtEquipes->execute([$tournoiId]);
            $equipes = $stmtEquipes->fetchAll(PDO::FETCH_ASSOC);

            // Récupérer les créneaux (réservations)
            $sqlCreneaux = "SELECT r.*, ter.nom_terrain, ter.localisation
                          FROM reservation r
                          LEFT JOIN terrain ter ON r.id_terrain = ter.id_terrain
                          WHERE r.id_tournoi = ?
                          ORDER BY r.date_reservation, r.creneau";
            $stmtCreneaux = $db->prepare($sqlCreneaux);
            $stmtCreneaux->execute([$tournoiId]);
            $creneaux = $stmtCreneaux->fetchAll(PDO::FETCH_ASSOC);

            // Récupérer le terrain principal
            $sqlTerrain = "SELECT DISTINCT ter.*
                          FROM terrain ter
                          INNER JOIN reservation r ON ter.id_terrain = r.id_terrain
                          WHERE r.id_tournoi = ?
                          LIMIT 1";
            $stmtTerrain = $db->prepare($sqlTerrain);
            $stmtTerrain->execute([$tournoiId]);
            $terrain = $stmtTerrain->fetch(PDO::FETCH_ASSOC);

            $this->view('gestionnaire/details_tournoi', [
                'user' => $_SESSION['user'],
                'tournoi' => $tournoi,
                'equipes' => $equipes,
                'creneaux' => $creneaux,
                'terrain' => $terrain
            ]);

        } catch (PDOException $e) {
            error_log("Erreur details: " . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors du chargement des détails';
            header('Location: ' . BASE_URL . 'tournoi?section=demandes');
            exit;
        }
    }
}