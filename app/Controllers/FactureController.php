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
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Non autorisé']);
                exit;
            }
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

            // Démarrer la bufferisation de sortie pour éviter les erreurs d'en-têtes
            ob_start();

            // Générer le PDF
            $pdfPath = $this->generatePDF($num_facture, $current_user_id);

            // Nettoyer la bufferisation
            ob_end_clean();

            // Mettre à jour la facture avec le chemin du PDF
            $factureModel->updatePdfPath($num_facture, $pdfPath);

            // Réponse AJAX ou redirection normale
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Facture générée avec succès!',
                    'num_facture' => $num_facture,
                    'download_url' => BASE_URL . 'facture/download/' . $num_facture
                ]);
                exit;
            }

            // Rediriger vers le téléchargement du PDF
            $_SESSION['success'] = 'Facture générée avec succès!';
            header('Location: ' . BASE_URL . 'facture/download/' . $num_facture);
            exit;

        } catch (Exception $e) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
                exit;
            }
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

        // Rediriger vers le téléchargement du PDF
        header('Location: ' . BASE_URL . 'facture/download/' . $num_facture);
        exit;
    }

    /**
     * Vérifie si la requête est une requête AJAX
     */
    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Génère le PDF d'une facture
     */
    private function generatePDF($num_facture, $gestionnaire_id) {
        require_once __DIR__ . '/../../vendor/setasign/fpdf/fpdf.php';

        $factureModel = new Facture();
        $facture = $factureModel->getFactureDetails($num_facture, $gestionnaire_id);

        if (!$facture) {
            throw new Exception("Facture introuvable");
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

        // Extraire les variables pour le template
        $facture_data = $viewData['facture'] ?? [];
        $reservation = $viewData['reservation'] ?? [];
        $terrain = $viewData['terrain'] ?? [];
        $client = $viewData['client'] ?? [];
        $gestionnaire = $viewData['gestionnaire'] ?? [];
        $options = $viewData['options'] ?? [];
        $total_options = $viewData['total_options'] ?? 0;

        // Créer le PDF avec FPDF
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // En-tête
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 15, 'BOOK&PLAY', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, 'Plateforme de Réservation de Terrains', 0, 1, 'C');
        $pdf->Ln(10);

        // Titre FACTURE
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(0, 15, 'FACTURE', 0, 1, 'R');
        $pdf->Ln(5);

        // Informations facture
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 8, 'Numéro de Facture:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, '#' . str_pad(($facture_data['num_facture'] ?? 0), 6, '0', STR_PAD_LEFT), 0, 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 8, 'Date de Facturation:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, !empty($facture_data['date_facturation']) ? date('d/m/Y', strtotime($facture_data['date_facturation'])) : date('d/m/Y'), 0, 1);
        $pdf->Ln(10);

        // Parties (Émetteur et Client)
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(90, 8, 'ÉMETTEUR', 1, 0, 'C');
        $pdf->Cell(10, 8, '', 0, 0); // Espace
        $pdf->Cell(90, 8, 'CLIENT', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $maxLines = max(
            count(explode("\n", wordwrap(iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $terrain['nom_terrain'] ?? 'N/A'), 35))),
            count(explode("\n", wordwrap(iconv('UTF-8', 'ISO-8859-1//TRANSLIT', ($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '') ?: 'N/A'), 35)))
        );

        for ($i = 0; $i < $maxLines; $i++) {
            $terrainLines = explode("\n", wordwrap(iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $terrain['nom_terrain'] ?? 'N/A'), 35));
            $clientLines = explode("\n", wordwrap(iconv('UTF-8', 'ISO-8859-1//TRANSLIT', ($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '') ?: 'N/A'), 35));

            $terrainLine = $terrainLines[$i] ?? '';
            $clientLine = $clientLines[$i] ?? '';

            $pdf->Cell(90, 6, $terrainLine, 1, 0);
            $pdf->Cell(10, 6, '', 0, 0);
            $pdf->Cell(90, 6, $clientLine, 1, 1);
        }
        $pdf->Ln(5);

        // Détails de la réservation
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'DÉTAILS DE LA RÉSERVATION', 1, 1, 'C');
        $pdf->SetFont('Arial', '', 10);

        $details = [
            ['Terrain', iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $terrain['nom_terrain'] ?? 'N/A')],
            ['Type de Terrain', iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $terrain['type_terrain'] ?? 'N/A')],
            ['Format', iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $terrain['format_terrain'] ?? 'N/A')],
            ['Date de Réservation', !empty($reservation['date_reservation']) ? date('d/m/Y', strtotime($reservation['date_reservation'])) : 'N/A'],
            ['Créneau Horaire', !empty($reservation['creneau']) ? date('H:i', strtotime($reservation['creneau'])) : 'N/A'],
            ['Type de Réservation', ucfirst($reservation['type'] ?? 'normal')]
        ];

        foreach ($details as $detail) {
            $pdf->Cell(50, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $detail[0] . ':'), 1, 0);
            $pdf->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $detail[1]), 1, 1);
        }

        if (!empty($reservation['commentaire'])) {
            $pdf->Cell(50, 8, 'Commentaire:', 1, 0);
            $pdf->MultiCell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $reservation['commentaire']), 1, 'L');
        }
        $pdf->Ln(5);

        // Options supplémentaires
        if (!empty($options)) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'OPTIONS SUPPLÉMENTAIRES', 1, 1, 'C');
            $pdf->SetFont('Arial', '', 10);

            foreach ($options as $option) {
                $pdf->Cell(120, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $option['nom_option'] ?? 'Option'), 1, 0);
                $pdf->Cell(0, 8, number_format(($option['prix'] ?? 0), 2, ',', ' ') . ' DH', 1, 1, 'R');
            }
            $pdf->Ln(5);
        }

        // Tableau des montants
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'DÉTAIL DES MONTANTS', 1, 1, 'C');
        $pdf->SetFont('Arial', '', 10);

        $montants = [
            ['Prix du Terrain (par heure)', number_format(($terrain['prix_heure'] ?? 0), 2, ',', ' ') . ' DH'],
            ['Options Supplémentaires', number_format(($total_options ?? 0), 2, ',', ' ') . ' DH'],
            ['Sous-total HT', number_format((($facture_data['TTC'] ?? 0) / 1.20), 2, ',', ' ') . ' DH'],
            ['TVA (20%)', number_format((($facture_data['TTC'] ?? 0) - (($facture_data['TTC'] ?? 0) / 1.20)), 2, ',', ' ') . ' DH'],
        ];

        foreach ($montants as $montant) {
            $pdf->Cell(120, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $montant[0]), 1, 0);
            $pdf->Cell(0, 8, $montant[1], 1, 1, 'R');
        }

        // Total
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(0, 123, 255);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(120, 10, 'TOTAL TTC', 1, 0, 'L', true);
        $pdf->Cell(0, 10, number_format(($facture_data['TTC'] ?? 0), 2, ',', ' ') . ' DH', 1, 1, 'R', true);
        $pdf->SetTextColor(0, 0, 0);

        // Pied de page
        $pdf->Ln(20);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 5, 'Book&Play - Plateforme de Réservation de Terrains de Sport', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Cette facture est générée automatiquement.', 0, 1, 'C');

        // Créer le répertoire si nécessaire
        $uploadDir = __DIR__ . '/../../public/uploads/factures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Nom du fichier
        $filename = 'facture_' . $num_facture . '.pdf';
        $filepath = $uploadDir . $filename;

        // Sauvegarder le PDF
        $pdf->Output($filepath, 'F');

        // Retourner le chemin relatif pour la base de données
        return 'uploads/factures/' . $filename;
    }

    /**
     * Télécharge la facture en PDF
     */
    public function download($num_facture) {
        // Vérifier que c'est un gestionnaire
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'gestionnaire') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $gestionnaire_id = $_SESSION['user']['id'];
        $factureModel = new Facture();

        // Récupérer les détails de la facture
        $facture = $factureModel->getFactureDetails($num_facture, $gestionnaire_id);

        if (!$facture || empty($facture['facture_path'])) {
            $_SESSION['error'] = 'Facture introuvable ou PDF non disponible';
            header('Location: ' . BASE_URL . 'facture');
            exit;
        }

        $filepath = __DIR__ . '/../../public/' . $facture['facture_path'];

        if (!file_exists($filepath)) {
            $_SESSION['error'] = 'Fichier PDF introuvable';
            header('Location: ' . BASE_URL . 'facture');
            exit;
        }

        // Servir le fichier PDF en ligne (ouvre dans le navigateur)
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="facture_' . $num_facture . '.pdf"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
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
