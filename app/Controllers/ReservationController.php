<?php

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/reservationTerrain.php';



class ReservationController extends Controller {



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
    
    // Vérifier si l'utilisateur existe dans la table client
    // Si non, créer l'entrée client ou utiliser une autre logique
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

    $reservationModel = new Reservation();
    $ok = $reservationModel->createReservation($data);

    error_log("Résultat de createReservation: " . ($ok ? "Succès" : "Échec"));

    if ($ok) {
        $_SESSION['success'] = "Réservation créée avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la création de la réservation";
        error_log("Échec de la création de la réservation: " . print_r($data, true));
    }

    // Rediriger vers Mes Réservations
    header('Location: ' . BASE_URL . 'utilisateur/mesReservations');
    exit;
}

    // GET /reservation/details?id=...
    public function details() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . BASE_URL . 'utilisateur/mesReservations');
            exit;
        }
        $reservationModel = new Reservation();
        $reservation = $reservationModel->getReservationById($id, $_SESSION['user']['id']);
        // Pour l'instant, redirection vers la liste; une vue dédiée pourra être ajoutée plus tard
        header('Location: ' . BASE_URL . 'utilisateur/mesReservations');
        exit;
    }

    // GET/POST /reservation/edit?id=...
    public function edit() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        // Placeholder: à implémenter si nécessaire (formulaire de modification)
        header('Location: ' . BASE_URL . 'utilisateur/mesReservations');
        exit;
    }
}