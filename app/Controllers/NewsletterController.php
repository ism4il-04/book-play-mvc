<?php
// app/Controllers/NewsletterController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class NewsletterController extends Controller {
       protected $db;
    
  
    public function index() {
        // Vérifier que l'utilisateur est admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Charger le modèle Newsletter
        $newsletterModel = $this->model('Newsletter');
        
        // Récupérer les statistiques
        $stats = $newsletterModel->getStats();
        
        // Récupérer l'historique des newsletters
        $history = $newsletterModel->getHistory(10);

        $this->view('administrateur/newsletter', [
            'user' => $_SESSION['user'],
            'stats' => $stats,
            'history' => $history
        ]);
    }

    public function send() {
        // Vérifier que l'utilisateur est admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject = trim($_POST['subject'] ?? '');
            $message = $_POST['message'] ?? '';
            $recipientType = $_POST['recipient_type'] ?? 'all';
            
            if (empty($subject) || empty($message)) {
                $_SESSION['error'] = 'Le sujet et le message sont obligatoires';
                header('Location: ' . BASE_URL . 'newsletter');
                exit;
            }

            $newsletterModel = $this->model('Newsletter');
            
            // Récupérer les destinataires
            $recipients = $newsletterModel->getRecipients($recipientType);
            
            if (empty($recipients)) {
                $_SESSION['error'] = 'Aucun destinataire trouvé';
                header('Location: ' . BASE_URL . 'newsletter');
                exit;
            }

            // Envoyer les emails
            $sent = 0;
            $failed = 0;
            
            foreach ($recipients as $recipient) {
                $emailContent = $this->generateEmailTemplate($subject, $message, $recipient);
                
                if ($this->sendEmail($recipient['email'], $subject, $emailContent)) {
                    $sent++;
                    // Petite pause pour éviter le spam (optionnel)
                    usleep(100000); // 0.1 seconde
                } else {
                    $failed++;
                }
            }

            // Enregistrer dans l'historique
            $newsletterModel->saveHistory($subject, $message, $recipientType, $sent, $failed);

            if ($sent > 0) {
                $_SESSION['success'] = "Newsletter envoyée avec succès ! ($sent envoyés, $failed échecs)";
            } else {
                $_SESSION['error'] = "Échec de l'envoi de la newsletter. Vérifiez votre configuration SMTP.";
            }
            
            header('Location: ' . BASE_URL . 'newsletter');
            exit;
        }

        header('Location: ' . BASE_URL . 'newsletter');
        exit;
    }

    private function generateEmailTemplate($subject, $message, $recipient) {
        $name = $recipient['prenom'] ?? 'Utilisateur';
        $currentYear = date('Y');
        
        // Convertir les retours à la ligne en <br>
        $message = nl2br(htmlspecialchars($message));
        
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
        .features {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
        }
        .feature-item {
            display: flex;
            align-items: center;
            margin: 15px 0;
            padding: 10px;
        }
        .feature-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #064420 0%, #0a5c3c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #CEFE24;
            font-size: 24px;
            margin-right: 15px;
        }
        .feature-text {
            flex: 1;
            color: #333333;
        }
        .footer {
            background: #2c3e50;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            margin: 0 5px;
            line-height: 40px;
            color: #ffffff;
            text-decoration: none;
            transition: all 0.3s;
        }
        .unsubscribe {
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            margin-top: 15px;
        }
        .unsubscribe a {
            color: #CEFE24;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">Book<span>&</span>Play</div>
            <div class="tagline">Votre terrain de sport en quelques clics</div>
        </div>
        
        <div class="content">
            <div class="greeting">Bonjour {$name} 👋</div>
            
            <div class="message">
                {$message}
            </div>
            
            <center>
                <a href="https://bookandplay.com/terrains" class="cta-button">
                    🎯 Réserver maintenant
                </a>
            </center>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">⚡</div>
                    <div class="feature-text">
                        <strong>Réservation instantanée</strong><br>
                        <small>Réservez votre terrain en quelques secondes</small>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🏆</div>
                    <div class="feature-text">
                        <strong>Tournois exclusifs</strong><br>
                        <small>Participez à nos compétitions passionnantes</small>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">💰</div>
                    <div class="feature-text">
                        <strong>Meilleurs prix</strong><br>
                        <small>Des tarifs compétitifs garantis</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <div class="logo">Book<span>&</span>Play</div>
            
            <div class="social-links">
                <a href="#">📘</a>
                <a href="#">📷</a>
                <a href="#">🐦</a>
                <a href="#">📺</a>
            </div>
            
            <p style="margin: 15px 0; font-size: 14px;">
                📍 Tétouan, Maroc<br>
                📧 contact@bookandplay.com<br>
                📞 +212 6XX XXX XXX
            </p>
            
            <div class="unsubscribe">
                © {$currentYear} Book&Play. Tous droits réservés.<br>
                <a href="#">Se désabonner</a> | <a href="#">Préférences email</a>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
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

    public function preview() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject = $_POST['subject'] ?? 'Aperçu Newsletter';
            $message = $_POST['message'] ?? '';
            
            $recipient = [
                'prenom' => $_SESSION['user']['name'] ?? 'Utilisateur',
                'email' => $_SESSION['user']['email'] ?? ''
            ];
            
            echo $this->generateEmailTemplate($subject, $message, $recipient);
            exit;
        }
    }

    /**
     * Tester la configuration SMTP
     */
    public function testSMTP() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $mail = new PHPMailer(true);
        $errors = [];
        $success = false;

        try {
            // Vérifier les variables d'environnement
            if (empty($_ENV['SMTP_HOST'])) $errors[] = "SMTP_HOST manquant dans .env";
            if (empty($_ENV['SMTP_PORT'])) $errors[] = "SMTP_PORT manquant dans .env";
            if (empty($_ENV['SMTP_USERNAME'])) $errors[] = "SMTP_USERNAME manquant dans .env";
            if (empty($_ENV['SMTP_PASSWORD'])) $errors[] = "SMTP_PASSWORD manquant dans .env";

            if (!empty($errors)) {
                $_SESSION['error'] = "Configuration SMTP incomplète : " . implode(", ", $errors);
                header('Location: ' . BASE_URL . 'newsletter');
                exit;
            }

            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = intval($_ENV['SMTP_PORT']);
            
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Email de test
            $mail->setFrom($_ENV['SMTP_USERNAME'], 'Book&Play - Test SMTP');
            $mail->addAddress($_SESSION['user']['email'] ?? $_ENV['SMTP_USERNAME']);
            
            $mail->isHTML(true);
            $mail->Subject = '✅ Test Configuration SMTP - Book&Play';
            $mail->Body = '
                <div style="font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5;">
                    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px;">
                        <h1 style="color: #064420; text-align: center;">✅ Configuration SMTP Réussie !</h1>
                        <p style="font-size: 16px; color: #333;">
                            Si vous recevez cet email, votre configuration SMTP fonctionne parfaitement.
                        </p>
                        <div style="background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0;">
                            <h3 style="color: #064420; margin-top: 0;">Détails de la configuration :</h3>
                            <ul style="color: #333;">
                                <li><strong>Host:</strong> ' . $_ENV['SMTP_HOST'] . '</li>
                                <li><strong>Port:</strong> ' . $_ENV['SMTP_PORT'] . '</li>
                                <li><strong>Username:</strong> ' . $_ENV['SMTP_USERNAME'] . '</li>
                            </ul>
                        </div>
                        <p style="text-align: center; color: #28a745; font-size: 18px; font-weight: bold;">
                            🎉 Vous pouvez maintenant envoyer vos newsletters !
                        </p>
                    </div>
                </div>
            ';
            $mail->AltBody = 'Configuration SMTP réussie ! Vous pouvez maintenant envoyer vos newsletters.';
            $mail->CharSet = 'UTF-8';
            
            $mail->send();
            
            $_SESSION['success'] = "✅ Email de test envoyé avec succès à " . ($_SESSION['user']['email'] ?? $_ENV['SMTP_USERNAME']) . " ! Vérifiez votre boîte mail (et les spams).";
            
        } catch (Exception $e) {
            $errorMsg = $mail->ErrorInfo;
            
            // Messages d'erreur personnalisés
            if (strpos($errorMsg, 'Could not authenticate') !== false) {
                $_SESSION['error'] = "❌ Authentification SMTP échouée. Vérifiez votre mot de passe Gmail (utilisez un mot de passe d'application). Erreur: " . $errorMsg;
            } elseif (strpos($errorMsg, 'connect()') !== false) {
                $_SESSION['error'] = "❌ Impossible de se connecter au serveur SMTP. Vérifiez SMTP_HOST et SMTP_PORT. Essayez le port 465. Erreur: " . $errorMsg;
            } else {
                $_SESSION['error'] = "❌ Erreur SMTP : " . $errorMsg;
            }
        }

        header('Location: ' . BASE_URL . 'newsletter');
        exit;
    }

    /**
     * Envoyer un email de test
     */
    public function test() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $testEmail = $_SESSION['user']['email'] ?? 'test@example.com';
        
        $recipient = [
            'prenom' => $_SESSION['user']['name'] ?? 'Admin',
            'email' => $testEmail
        ];
        
        $subject = "Test Newsletter - Book&Play";
        $message = "Ceci est un email de test pour vérifier la configuration de PHPMailer.\n\nSi vous recevez cet email, tout fonctionne correctement ! 🎉";
        
        $htmlContent = $this->generateEmailTemplate($subject, $message, $recipient);
        
        if ($this->sendEmail($testEmail, $subject, $htmlContent)) {
            $_SESSION['success'] = "Email de test envoyé avec succès à {$testEmail}";
        } else {
            $_SESSION['error'] = "Échec de l'envoi de l'email de test. Vérifiez les logs.";
        }
        
        header('Location: ' . BASE_URL . 'newsletter');
        exit;
    }
}