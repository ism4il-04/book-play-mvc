<?php

class ReservationsController extends Controller {
    public function index() {
        if (!isset($_SESSION['user']) || 'gestionnaire' !== ($_SESSION['user']['role'] ?? null)) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $reservationModel = $this->model('Reservation');

        $status = isset($_GET['status']) ? trim($_GET['status']) : null;
        if ($status === '') {
            $status = null;
        }

        $reservations = $reservationModel->getForGestionnaire($_SESSION['user']['id'], $status);

        $this->view('gestionnaire/reservations', [
            'user' => $_SESSION['user'],
            'reservations' => $reservations,
            'filter_status' => $status,
        ]);
    }

    public function accept($id = null) {
        $this->guardGestionnaire();
        if (!$id) {
            $_SESSION['error'] = "Réservation introuvable.";
            header('Location: ' . BASE_URL . 'reservations');
            exit;
        }
        $reservationModel = $this->model('Reservation');
        $ok = $reservationModel->updateStatusForGestionnaire($_SESSION['user']['id'], (int)$id, 'accepté');
        $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Réservation acceptée." : "Action non autorisée.";
        header('Location: ' . BASE_URL . 'reservations');
        exit;
    }

    public function refuse($id = null) {
        $this->guardGestionnaire();
        if (!$id) {
            $_SESSION['error'] = "Réservation introuvable.";
            header('Location: ' . BASE_URL . 'reservations');
            exit;
        }
        $reservationModel = $this->model('Reservation');
        $ok = $reservationModel->updateStatusForGestionnaire($_SESSION['user']['id'], (int)$id, 'refusé');
        $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Réservation refusée." : "Action non autorisée.";
        header('Location: ' . BASE_URL . 'reservations');
        exit;
    }

    private function guardGestionnaire() {
        if (!isset($_SESSION['user']) || 'gestionnaire' !== ($_SESSION['user']['role'] ?? null)) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }
}


