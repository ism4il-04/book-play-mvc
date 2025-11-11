<?php

require_once __DIR__ . '/../Models/Facture.php';

class FactureController extends Controller {

    /**
     * Affiche la liste des réservations pour le gestionnaire
     */
    public function index() {
        // Vérifier que c'est un gestionnaire
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'gestionnaire') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $gestionnaire_id = $_SESSION['user']['id'];
        $factureModel = new Facture();

        // Récupérer les filtres
        $filters = [];
        if (!empty($_GET['date_debut'])) $filters['date_debut'] = $_GET['date_debut'];
        if (!empty($_GET['date_fin'])) $filters['date_fin'] = $_GET['date_fin'];
        if (!empty($_GET['terrain_id'])) $filters['terrain_id'] = (int)$_GET['terrain_id'];
        if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];

        // Récupérer les réservations
        $reservations = $factureModel->getReservationsByGestionnaire($gestionnaire_id, $filters);

        // Récupérer les terrains pour le filtre
        $terrains = $factureModel->getGestionnaireTerrains($gestionnaire_id);

        $this->view('gestionnaire/factures', [
            'reservations' => $reservations,
            'terrains' => $terrains,
            'filters' => $filters,
            'user' => $_SESSION['user']
        ]);
    }

    /**
     * Génère une facture pour une réservation
     */
    public function generate($id_reservation) {
        // Vérifier que c'est un gestionnaire
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'gestionnaire') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $current_user_id = $_SESSION['user']['id'];
        $factureModel = new Facture();

        try {
            // Vérifier l'accès à la réservation et récupérer les informations nécessaires
            $reservation = $factureModel->getReservationAccess($id_reservation, $current_user_id);

            if (!$reservation) {
                throw new Exception("Réservation introuvable ou accès non autorisé");
            }

            // Générer la facture avec l'ID du gestionnaire extrait du terrain
            $num_facture = $factureModel->createFacture($id_reservation, $reservation['id_gestionnaire']);

            // Rediriger vers l'affichage de la facture
            $_SESSION['success'] = 'Facture générée avec succès!';
            header('Location: ' . BASE_URL . 'facture/showFacture/' . $num_facture);
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur: ' . $e->getMessage();
            header('Location: ' . BASE_URL . 'facture');
            exit;
        }
    }

    /**
     * Affiche une facture
     */
    public function showFacture($num_facture) {
        // Vérifier que c'est un gestionnaire
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'gestionnaire') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $gestionnaire_id = $_SESSION['user']['id'];
        $factureModel = new Facture();

        // Récupérer les détails de la facture
        $facture = $factureModel->getFactureDetails($num_facture, $gestionnaire_id);

        if (!$facture) {
            $_SESSION['error'] = 'Facture introuvable ou accès non autorisé';
            header('Location: ' . BASE_URL . 'facture');
            exit;
        }

        // Préparer les données pour le template
        $viewData = [
            'facture' => [
                'num_facture' => $facture['num_facture'],
                'TTC' => $facture['TTC'],
                'date_facturation' => $facture['date_facturation']
            ],
            'reservation' => [
                'date_reservation' => $facture['date_reservation'],
                'creneau' => $facture['creneau'],
                'status' => $facture['status'],
                'type' => $facture['type'],
                'commentaire' => $facture['commentaire'],
                'id_terrain' => $facture['id_terrain'],
                'id_client' => $facture['id_client']
            ],
            'terrain' => $facture['terrain'],
            'client' => $facture['client'],
            'gestionnaire' => $facture['gestionnaire'],
            'options' => $facture['options'],
            'total_options' => $facture['total_options']
        ];

        // Extraire les variables pour le template avec des valeurs par défaut
        $facture = $viewData['facture'] ?? [];
        $reservation = $viewData['reservation'] ?? [];
        $terrain = $viewData['terrain'] ?? [];
        $client = $viewData['client'] ?? [];
        $gestionnaire = $viewData['gestionnaire'] ?? [];
        $options = $viewData['options'] ?? [];
        $total_options = $viewData['total_options'] ?? 0;

        // Charger le template de facture
        require_once __DIR__ . '/../Views/components/facture-template.php';
    }

    /**
     * Télécharge la facture en PDF (futur développement)
     */
    public function download($num_facture) {
        // Pour l'instant, rediriger vers la vue
        header('Location: ' . BASE_URL . 'facture/view/' . $num_facture);
        exit;
    }

    /**
     * API: Vérifie si une facture existe pour une réservation
     */
    public function checkExists($id_reservation) {
        header('Content-Type: application/json');

        // Vérifier que c'est un gestionnaire
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'gestionnaire') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        $gestionnaire_id = $_SESSION['user']['id'];
        $factureModel = new Facture();

        try {
            $num_facture = $factureModel->factureExists($id_reservation);

            echo json_encode([
                'success' => true,
                'exists' => $num_facture !== false,
                'num_facture' => $num_facture
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
