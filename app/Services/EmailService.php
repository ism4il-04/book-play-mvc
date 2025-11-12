<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Core/Controller.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private PHPMailer $mailer;
    private array $config;

    public function __construct() {
        // Charger les variables d'environnement
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->config = [
            'host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
            'port' => $_ENV['MAIL_PORT'] ?? 587,
            'username' => $_ENV['MAIL_USERNAME'] ?? '',
            'password' => $_ENV['MAIL_PASSWORD'] ?? '',
            'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
            'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Book-Play MVC',
            'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@bookplay.com'
        ];

        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }

    private function configureMailer(): void {
        try {
            // Configuration du serveur SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['username'];
            $this->mailer->Password = $this->config['password'];
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $this->config['port'];
            
            // Configuration de l'expéditeur
            $this->mailer->setFrom($this->config['from_address'], $this->config['from_name']);
            $this->mailer->CharSet = 'UTF-8';
        } catch (Exception $e) {
            error_log("Erreur de configuration EmailService: " . $e->getMessage());
        }
    }

    public function envoyerEmailAcceptation(string $email, string $nomGestionnaire, string $nomTerrain): bool {
        try {
            $this->mailer->addAddress($email);
            $this->mailer->Subject = 'Acceptation de votre demande de gestionnaire - Book-Play MVC';
            
            // Charger le template
            $template = $this->loadTemplate('acceptation_template.php', [
                'nom' => $nomGestionnaire,
                'terrain' => $nomTerrain
            ]);
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $template;
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erreur envoi email acceptation: " . $e->getMessage());
            return false;
        }
    }

    public function envoyerEmailRefus(string $email, string $nomGestionnaire, string $nomTerrain): bool {
        try {
            $this->mailer->addAddress($email);
            $this->mailer->Subject = 'Refus de votre demande de gestionnaire - Book-Play MVC';
            
            // Charger le template
            $template = $this->loadTemplate('refus_template.php', [
                'nom' => $nomGestionnaire,
                'terrain' => $nomTerrain
            ]);
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $template;
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erreur envoi email refus: " . $e->getMessage());
            return false;
        }
    }

    private function loadTemplate(string $templateName, array $variables): string {
        $templatePath = __DIR__ . '/../Views/administrateur/emails/' . $templateName;
        
        if (!file_exists($templatePath)) {
            throw new Exception("Template non trouvé: " . $templatePath);
        }

        // Extraire les variables pour les rendre disponibles dans le template
        extract($variables);
        
        // Démarrer la capture de sortie
        ob_start();
        include $templatePath;
        $content = ob_get_clean();
        
        return $content;
    }

    public function resetMailer(): void {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }
}
