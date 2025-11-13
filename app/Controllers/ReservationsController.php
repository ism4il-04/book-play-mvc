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
        
        // Get the last reservation ID for real-time monitoring
        $lastReservationId = 0;
        if (!empty($reservations)) {
            $lastReservationId = max(array_column($reservations, 'id_reservation'));
        }

        $this->view('gestionnaire/reservations', [
            'user' => $_SESSION['user'],
            'reservations' => $reservations,
            'filter_status' => $status,
            'last_reservation_id' => $lastReservationId,
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
        
        // Récupérer les détails de la réservation avant la mise à jour
        $reservation = $reservationModel->getReservationDetailsForGestionnaire($_SESSION['user']['id'], (int)$id);
        
        if (!$reservation) {
            $_SESSION['error'] = "Réservation non trouvée ou accès non autorisé.";
            header('Location: ' . BASE_URL . 'reservations');
            exit;
        }
        
        $ok = $reservationModel->updateStatusForGestionnaire($_SESSION['user']['id'], (int)$id, 'accepté');
        
        if ($ok) {
            // Refuser automatiquement les réservations conflictuelles
            $conflictsRefused = $reservationModel->refuseConflictingReservations(
                $_SESSION['user']['id'],
                (int)$id,
                $reservation['date_reservation'],
                $reservation['creneau'],
                $reservation['id_terrain']
            );
            
            $message = "Réservation acceptée.";
            if ($conflictsRefused) {
                $message .= " Les réservations conflictuelles ont été automatiquement refusées.";
            }
            $_SESSION['success'] = $message;
        } else {
            $_SESSION['error'] = "Action non autorisée.";
        }
        
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
        
        // Récupérer les détails de la réservation avant la mise à jour
        $reservation = $reservationModel->getReservationDetailsForGestionnaire($_SESSION['user']['id'], (int)$id);
        
        if (!$reservation) {
            $_SESSION['error'] = "Réservation non trouvée ou accès non autorisé.";
            header('Location: ' . BASE_URL . 'reservations');
            exit;
        }
        
        $ok = $reservationModel->updateStatusForGestionnaire($_SESSION['user']['id'], (int)$id, 'refusé');
        
        if ($ok) {
            // Refuser automatiquement les réservations conflictuelles
            $conflictsRefused = $reservationModel->refuseConflictingReservations(
                $_SESSION['user']['id'],
                (int)$id,
                $reservation['date_reservation'],
                $reservation['creneau'],
                $reservation['id_terrain']
            );
            
            $message = "Réservation refusée.";
            if ($conflictsRefused) {
                $message .= " Les réservations conflictuelles ont été automatiquement refusées.";
            }
            $_SESSION['success'] = $message;
        } else {
            $_SESSION['error'] = "Action non autorisée.";
        }
        
        header('Location: ' . BASE_URL . 'reservations');
        exit;
    }
    
    /**
     * AJAX: Vérifier les nouvelles réservations
     */
    public function checkNewReservations() {
        $this->guardGestionnaire();
        
        header('Content-Type: application/json');
        
        try {
            $reservationModel = $this->model('Reservation');
            
            // Récupérer le dernier ID de réservation pour ce gestionnaire
            $status = isset($_GET['status']) ? trim($_GET['status']) : null;
            if ($status === '') {
                $status = null;
            }
            
            $reservations = $reservationModel->getForGestionnaire($_SESSION['user']['id'], $status);
            $lastId = 0;
            if (!empty($reservations)) {
                $lastId = max(array_column($reservations, 'id_reservation'));
            }
            
            echo json_encode([
                'success' => true,
                'lastId' => $lastId
            ]);
        } catch (\Exception $e) {
            error_log("Erreur checkNewReservations: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur',
                'lastId' => 0
            ]);
        }
        exit;
    }
    
    /**
     * AJAX: Récupérer une réservation par ID
     */
    public function getReservationById($id = null) {
        $this->guardGestionnaire();
        
        header('Content-Type: application/json');
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }
        
        try {
            $reservationModel = $this->model('Reservation');
            $reservations = $reservationModel->getForGestionnaire($_SESSION['user']['id'], null);
            
            // Trouver la réservation spécifique
            $reservation = null;
            foreach ($reservations as $r) {
                if ($r['id_reservation'] == $id) {
                    $reservation = $r;
                    break;
                }
            }
            
            if ($reservation) {
                echo json_encode([
                    'success' => true,
                    'reservation' => $reservation
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'removed' => true,
                    'message' => 'Réservation non trouvée'
                ]);
            }
        } catch (\Exception $e) {
            error_log("Erreur getReservationById: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur'
            ]);
        }
        exit;
    }
    
    /**
     * AJAX: Mettre à jour le statut d'une réservation
     */
    public function updateStatus() {
        $this->guardGestionnaire();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $status = isset($data['status']) ? trim($data['status']) : '';
        
        if (!$id || !$status) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            exit;
        }
        
        $allowedStatuses = ['accepté', 'refusé', 'en attente', 'annulé'];
        if (!in_array($status, $allowedStatuses, true)) {
            echo json_encode(['success' => false, 'message' => 'Statut invalide']);
            exit;
        }
        
        try {
            $reservationModel = $this->model('Reservation');
            
            // Récupérer les détails de la réservation avant la mise à jour
            $reservation = $reservationModel->getReservationDetailsForGestionnaire($_SESSION['user']['id'], $id);
            
            if (!$reservation) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Réservation non trouvée ou accès non autorisé'
                ]);
                exit;
            }
            
            // Mettre à jour le statut de la réservation
            $ok = $reservationModel->updateStatusForGestionnaire($_SESSION['user']['id'], $id, $status);
            
            if ($ok) {
                // Si la réservation est acceptée ou refusée, refuser automatiquement les réservations conflictuelles
                if (in_array($status, ['accepté', 'refusé'], true)) {
                    $conflictsRefused = $reservationModel->refuseConflictingReservations(
                        $_SESSION['user']['id'],
                        $id,
                        $reservation['date_reservation'],
                        $reservation['creneau'],
                        $reservation['id_terrain']
                    );
                    
                    $statusLabels = [
                        'accepté' => 'acceptée',
                        'refusé' => 'refusée',
                        'en attente' => 'mise en attente',
                        'annulé' => 'annulée'
                    ];
                    
                    $message = 'Réservation ' . ($statusLabels[$status] ?? $status) . ' avec succès';
                    if ($conflictsRefused) {
                        $message .= '. Les réservations conflictuelles ont été automatiquement refusées.';
                    }
                    
                    echo json_encode([
                        'success' => true,
                        'message' => $message,
                        'conflictsRefused' => $conflictsRefused
                    ]);
                } else {
                    $statusLabels = [
                        'accepté' => 'acceptée',
                        'refusé' => 'refusée',
                        'en attente' => 'mise en attente',
                        'annulé' => 'annulée'
                    ];
                    echo json_encode([
                        'success' => true,
                        'message' => 'Réservation ' . ($statusLabels[$status] ?? $status) . ' avec succès'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Action non autorisée ou réservation introuvable'
                ]);
            }
        } catch (\Exception $e) {
            error_log("Erreur updateStatus: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur'
            ]);
        }
        exit;
    }

    private function guardGestionnaire() {
        if (!isset($_SESSION['user']) || 'gestionnaire' !== ($_SESSION['user']['role'] ?? null)) {
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            } else {
                header('Location: ' . BASE_URL . 'auth/login');
            }
            exit;
        }
    }
}


