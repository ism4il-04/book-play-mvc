<?php

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Gestion_gestionnaireController extends Controller {
    public function index() {
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Charger le modèle Admin
        $adminModel = $this->model('Admin');
        
        // Récupérer les gestionnaires en attente
        $gestionnairesEnAttente = $adminModel->getAllGestionnairesEnAttente();

        // Récupérer les gestionnaires accepté
        $gestionnaires_accepte = $adminModel->getAllGestionnairesAccepte();

        // Récupérer les gestionnaires refusé
        $gestionnaires_refuse = $adminModel->getAllGestionnairesRefuse();

        $stats = $adminModel->getStats();



        // Préparer les données pour la vue
        $viewData = [
            'gestionnaires_en_attente' => $gestionnairesEnAttente,
            'gestionnaires_accepte' => $gestionnaires_accepte,
            'gestionnaires_refuse' => $gestionnaires_refuse,
            'nbrAccepte' => $stats['actifs'] ?? 0,
            'nbrEnAttente' => $stats['en_attente'] ?? 0,
            'nbrRefuse' => $stats['refuses'] ?? 0,
            'error' => null,
        ];

        // Afficher la vue avec TOUTES les données
        $this->view('administrateur/Gestion_gestionnaire', $viewData);
    }

    // Méthode pour accepter un gestionnaire
    public function accepter() {
        // Nettoyer complètement le buffer de sortie
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Démarrer un nouveau buffer propre
        ob_start();
        
        // Définir les headers JSON dès le début
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        // Récupérer l'ID du gestionnaire et du terrain
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $idTerrain = $data['id_terrain'] ?? null;

        if (!$id) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }

        // Charger le modèle et récupérer les informations du gestionnaire
        $adminModel = $this->model('Admin');
        
        // Récupérer les détails du gestionnaire avant la mise à jour
        $gestionnaire = $adminModel->getGestionnaireDetailsById($id);
        
        if (!$gestionnaire || $gestionnaire === false) {
            error_log("Gestionnaire non trouvé pour ID: " . $id);
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Gestionnaire non trouvé']);
            exit;
        }
        
        // Quand on accepte un gestionnaire, son terrain passe de 'en attente' à 'accepté'
        $result = $adminModel->updateGestionnaireStatus($id, 'accepté', 'acceptée', $idTerrain);
        
        // Ajouter des logs pour débugger
        error_log("updateGestionnaireStatus result: " . ($result ? 'true' : 'false') . " for ID: " . $id);

        if ($result) {
            // Vérifier si le gestionnaire était déjà accepté (pour éviter d'envoyer l'email plusieurs fois)
            $adminModel = $this->model('Admin');
            $gestionnaireActuel = $adminModel->getGestionnaireDetailsById($id);
            $gestionnaireDejaAccepte = ($gestionnaire['status'] === 'accepté');
            
            $message = '';
            $emailStatus = '';
            
            if ($gestionnaireDejaAccepte) {
                // Gestionnaire déjà accepté, on a juste accepté un terrain supplémentaire
                $message = 'Terrain accepté avec succès !';
                $emailStatus = 'Aucun email envoyé (gestionnaire déjà accepté)';
            } else {
                // Premier terrain accepté, gestionnaire nouvellement accepté
                $message = 'Gestionnaire accepté avec succès !';
                
                // Envoyer l'email d'acceptation
                $subject = "🎉 Félicitations ! Votre demande de gestionnaire a été acceptée";
                $emailContent = $this->generateGestionnaireEmailTemplate('acceptation', $gestionnaire);
                
                $emailSent = $this->sendEmail($gestionnaire['email'], $subject, $emailContent);
                $emailStatus = $emailSent ? 'Email de confirmation envoyé avec succès' : 'Erreur lors de l\'envoi de l\'email';
            }
            
            ob_clean();
            echo json_encode([
                'success' => true, 
                'message' => $message, 
                'email_status' => $emailStatus
            ]);
        } else {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'acceptation']);
        }
        exit;
    }

    // Méthode pour refuser un gestionnaire
    public function refuser() {
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        // Récupérer l'ID du gestionnaire et du terrain
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $idTerrain = $data['id_terrain'] ?? null;

        if (!$id) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }

        // Charger le modèle et récupérer les informations du gestionnaire
        $adminModel = $this->model('Admin');
        
        // Récupérer les détails du gestionnaire avant la mise à jour
        $gestionnaire = $adminModel->getGestionnaireDetailsById($id);
        
        if (!$gestionnaire || $gestionnaire === false) {
            error_log("Gestionnaire non trouvé pour ID: " . $id);
            echo json_encode(['success' => false, 'message' => 'Gestionnaire non trouvé']);
            exit;
        }
                
        // Quand on refuse un gestionnaire, son terrain passe à 'refusé'
        $result = $adminModel->updateGestionnaireStatus($id, 'refusé', 'refusée', $idTerrain);
        
        // Ajouter des logs pour débugger
        error_log("updateGestionnaireStatus result: " . ($result ? 'true' : 'false') . " for ID: " . $id);

        if ($result) {
            // Envoyer l'email de refus
            $subject = "Décision concernant votre demande de gestionnaire";
            $emailContent = $this->generateGestionnaireEmailTemplate('refus', $gestionnaire);
            
            $emailSent = $this->sendEmail($gestionnaire['email'], $subject, $emailContent);
            
            if ($emailSent) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Gestionnaire refusé avec succès !', 
                    'email_status' => 'Email de notification envoyé avec succès'
                ]);
            } else {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Gestionnaire refusé avec succès !', 
                    'email_status' => 'Erreur lors de l\'envoi de l\'email'
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors du refus']);
        }
        exit;
    }

    // Méthode pour remettre en attente un gestionnaire refusé
    public function remettreEnAttente() {
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        // Récupérer l'ID du gestionnaire et du terrain
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $idTerrain = $data['id_terrain'] ?? null;

        if (!$id) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }

        // Charger le modèle et mettre à jour le statut
        $adminModel = $this->model('Admin');
        // Remettre le gestionnaire en attente et son terrain en attente
        $result = $adminModel->updateGestionnaireStatus($id, 'en attente', 'en attente', $idTerrain);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Demande remise en attente avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la remise en attente']);
        }
        exit;
    }

    // Méthode pour récupérer les détails d'un gestionnaire
    public function getDetails() {
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        // Récupérer l'ID du gestionnaire
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;

        if (!$id) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }

        // Charger le modèle et récupérer les détails
        $adminModel = $this->model('Admin');
        $gestionnaire = $adminModel->getGestionnaireDetailsById($id);

        if ($gestionnaire) {
            echo json_encode([
                'success' => true, 
                'gestionnaire' => $gestionnaire
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gestionnaire non trouvé']);
        }
        exit;
    }

    // Méthode pour supprimer un gestionnaire
    public function supprimer() {
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        // Récupérer les données JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if (!$id) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            exit;
        }

        // Charger le modèle Admin
        $adminModel = $this->model('Admin');
        
        // Supprimer le gestionnaire
        $result = $adminModel->supprimerGestionnaire($id);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Gestionnaire supprimé avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
        exit;
    }

    /**
     * Envoyer un email avec PHPMailer
     */
    private function sendEmail($to, $subject, $htmlContent) {
        $mail = new PHPMailer(true);
        
        try {
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'] ?? 'votre-email@gmail.com';
            $mail->Password = $_ENV['SMTP_PASSWORD'] ?? 'votre-mot-de-passe-app';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = intval($_ENV['SMTP_PORT'] ?? 587);
            
            // Options supplémentaires (optionnel)
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Expéditeur et destinataire
            $mail->setFrom('noreply@bookandplay.com', 'Book&Play');
            $mail->addAddress($to);
            $mail->addReplyTo('contact@bookandplay.com', 'Book&Play Support');
            
            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent); // Version texte
            $mail->CharSet = 'UTF-8';
            
            // Envoyer
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("PHPMailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Vérifier les nouveaux gestionnaires acceptés (pour le système temps réel)
     */
    public function checkNewGestionnaires() {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        try {
            $adminModel = $this->model('Admin');
            
            // Récupérer tous les gestionnaires acceptés
            $recentlyAcceptedGestionnaires = $adminModel->getRecentlyAcceptedGestionnaires();
            
            // Log pour débogage
            error_log('checkNewGestionnaires - Nombre de gestionnaires acceptés: ' . count($recentlyAcceptedGestionnaires));
            
            echo json_encode([
                'success' => true,
                'recentlyAccepted' => $recentlyAcceptedGestionnaires,
                'debug' => [
                    'count' => count($recentlyAcceptedGestionnaires),
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (Exception $e) {
            error_log('Erreur checkNewGestionnaires: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
        exit;
    }

    /**
     * Récupérer un gestionnaire par son ID (pour le système temps réel)
     */
    public function getGestionnaireById($id = null) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        // Vérifier la session et le rôle
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'administrateur') {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        // Récupérer l'ID depuis l'URL ou les paramètres POST
        if (!$id) {
            $pathInfo = $_SERVER['PATH_INFO'] ?? '';
            $pathParts = explode('/', trim($pathInfo, '/'));
            $id = end($pathParts);
        }

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID manquant ou invalide']);
            exit;
        }

        try {
            $adminModel = $this->model('Admin');
            $gestionnaire = $adminModel->getGestionnaireDetailsById($id);
            
            if ($gestionnaire) {
                echo json_encode([
                    'success' => true,
                    'gestionnaire' => $gestionnaire
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gestionnaire non trouvé',
                    'removed' => true
                ]);
            }
        } catch (Exception $e) {
            error_log('Erreur getGestionnaireById: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
        exit;
    }

    /**
     * Générer le template d'email pour acceptation/refus de gestionnaire
     */
    private function generateGestionnaireEmailTemplate($type, $gestionnaire, $terrain = null) {
        $name = $gestionnaire['prenom'] ?? 'Gestionnaire';
        $currentYear = date('Y');
        $currentDate = date('d/m/Y à H:i');
        
        // Définir le contenu selon le type (acceptation ou refus)
        if ($type === 'acceptation') {
            $subject = "🎉 Félicitations! Votre demande de gestionnaire a été acceptée";
            $mainMessage = "Nous avons le plaisir de vous informer que votre demande pour devenir gestionnaire de terrain sur Book&Play a été <strong style='color: #28a745;'>acceptée</strong> ! Connecté vous avec Email {$gestionnaire['email']}";
            $ctaText = "🏆 Accéder à mon espace gestionnaire";
            $ctaLink = BASE_URL."auth/login";
            $statusColor = "#28a745";
            $statusIcon = "✅";
        } else {
            $subject = "❌ Décision concernant votre demande de gestionnaire";
            $mainMessage = "Nous vous remercions pour votre intérêt à devenir gestionnaire de terrain sur Book&Play. Après examen de votre dossier, nous ne pouvons malheureusement pas <strong style='color: #dc3545;'>accepter</strong> votre demande pour le moment.";
            $additionalInfo = "<p>N'hésitez pas à nous recontacter si vous souhaitez soumettre une nouvelle demande avec des informations complémentaires.</p>";
            $ctaText = "📞 Nous contacter";
            $ctaLink = "mailto:contact@bookandplay.com";
            $statusColor = "#dc3545";
            $statusIcon = "❌";
        }
        
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$subject}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #064420 0%, #0a5c3c 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 10px;
        }
        .logo span {
            color: #CEFE24;
        }
        .tagline {
            color: rgba(255,255,255,0.9);
            font-size: 14px;
        }
        .content {
            padding: 40px 30px;
        }
        .status-badge {
            display: inline-block;
            background: {$statusColor};
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .greeting {
            font-size: 24px;
            color: #064420;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message {
            color: #333333;
            line-height: 1.8;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid {$statusColor};
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #CEFE24 0%, #b9ff00 100%);
            color: #064420;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(206, 254, 36, 0.3);
        }
        .footer {
            background: #2c3e50;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .contact-info {
            margin: 20px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">Book<span>&</span>Play</div>
            <div class="tagline">Gestion des terrains de sport</div>
        </div>
        
        <div class="content">
            <center>
                <div class="status-badge">{$statusIcon} Demande de gestionnaire</div>
            </center>
            
            <div class="greeting">Bonjour {$name} 👋</div>
            
            <div class="message">
                {$mainMessage}
            </div>
            
            <div class="info-box">
                <h3 style="color: {$statusColor}; margin-bottom: 10px;">Informations de votre demande :</h3>
                <p><strong>Nom :</strong> {$gestionnaire['nom']} {$gestionnaire['prenom']}</p>
                <p><strong>Email :</strong> {$gestionnaire['email']}</p>
                <p><strong>Téléphone :</strong> {$gestionnaire['num_tel']}</p>
                <p><strong>Terrain :</strong> {$gestionnaire['nom_terrain']}</p>
                <p><strong>Date de traitement :</strong> {$currentDate}</p>
            </div>
            
            <div class="message">
                {$additionalInfo}
            </div>
            
            <center>
                <a href="{$ctaLink}" class="cta-button">
                    {$ctaText}
                </a>
            </center>
        </div>
        
        <div class="footer">
            <div class="logo">Book<span>&</span>Play</div>
            
            <div class="contact-info">
                📍 Tétouan, Maroc<br>
                📧 contact@bookandplay.com<br>
                📞 +212 6XX XXX XXX
            </div>
            
            <p style="margin-top: 15px; font-size: 12px; color: rgba(255,255,255,0.6);">
                © {$currentYear} Book&Play. Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}