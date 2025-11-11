<?php
// app/Core/NewsletterTrigger.php

class NewsletterTrigger {
    
    private static $lockFile = __DIR__ . '/../../storage/newsletter.lock';
    private static $logFile = __DIR__ . '/../../storage/newsletter.log';
    
    /**
     * Vérifier et déclencher la newsletter si nécessaire
     * Appelé automatiquement à chaque visite du site
     */
    public static function check() {
        // Ne pas bloquer l'exécution de la page
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        
        // Vérifier si on doit envoyer
        if (!self::shouldSend()) {
            return;
        }
        
        // Créer un lock pour éviter les envois multiples
        if (!self::acquireLock()) {
            return;
        }
        
        try {
            self::sendNewsletter();
        } finally {
            self::releaseLock();
        }
    }
    
    /**
     * Vérifier s'il faut envoyer la newsletter
     */
    private static function shouldSend() {
        try {
            require_once __DIR__ . '/Database.php';
            require_once __DIR__ . '/Model.php';
            require_once __DIR__ . '/../Models/AutoNewsletter.php';
            
            $newsletterModel = new AutoNewsletter();
            $config = $newsletterModel->getConfig();
            
            // Newsletter désactivée
            if (!$config || !$config['enabled']) {
                return false;
            }
            
            // Vérifier la dernière exécution
            $lastSend = self::getLastSendTime();
            $now = time();
            
            // Vérifier selon la fréquence
            switch ($config['frequency']) {
                case 'daily':
                    $interval = 86400; // 24 heures
                    break;
                case 'weekly':
                    $interval = 604800; // 7 jours
                    break;
                case 'monthly':
                    $interval = 2592000; // 30 jours
                    break;
                default:
                    $interval = 604800;
            }
            
            // Pas encore le moment
            if ($now - $lastSend < $interval) {
                return false;
            }
            
            // Vérifier le jour de la semaine (pour hebdomadaire)
            if ($config['frequency'] === 'weekly') {
                $dayMap = [
                    'monday' => 1, 'tuesday' => 2, 'wednesday' => 3,
                    'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 0
                ];
                
                $targetDay = $dayMap[$config['day_of_week']] ?? 1;
                $currentDay = (int)date('w');
                
                if ($currentDay !== $targetDay) {
                    return false;
                }
            }
            
            // Vérifier l'heure (avec marge de 6 heures)
            $configHour = (int)substr($config['send_time'], 0, 2);
            $currentHour = (int)date('H');
            
            if (abs($currentHour - $configHour) > 6) {
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            self::log("Erreur shouldSend: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoyer la newsletter
     */
    private static function sendNewsletter() {
        try {
            self::log("Démarrage envoi newsletter automatique");
            
            require_once __DIR__ . '/../../vendor/autoload.php';
            require_once __DIR__ . '/../core/Controller.php';
            require_once __DIR__ . '/../Controllers/AutoNewsletterController.php';
            require_once __DIR__ . '/../Models/Terrain.php';
            require_once __DIR__ . '/../Models/Tournoi.php';
            
            $db = Database::getInstance()->getConnection();
            $controller = new AutoNewsletterController($db);
            
            // Capturer la sortie
            ob_start();
            $controller->sendAutomatic();
            $output = ob_get_clean();
            
            self::log("Newsletter envoyée: " . $output);
            self::updateLastSendTime();
            
        } catch (Exception $e) {
            self::log("ERREUR: " . $e->getMessage());
        }
    }
    
    /**
     * Créer un fichier de verrou
     */
    private static function acquireLock() {
        $lockDir = dirname(self::$lockFile);
        if (!is_dir($lockDir)) {
            mkdir($lockDir, 0755, true);
        }
        
        // Si le lock existe et a moins de 30 minutes, ne pas envoyer
        if (file_exists(self::$lockFile)) {
            $lockTime = filemtime(self::$lockFile);
            if (time() - $lockTime < 1800) { // 30 minutes
                return false;
            }
            // Supprimer le vieux lock
            @unlink(self::$lockFile);
        }
        
        // Créer le lock
        return file_put_contents(self::$lockFile, time()) !== false;
    }
    
    /**
     * Libérer le verrou
     */
    private static function releaseLock() {
        if (file_exists(self::$lockFile)) {
            @unlink(self::$lockFile);
        }
    }
    
    /**
     * Récupérer le timestamp du dernier envoi
     */
    private static function getLastSendTime() {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "SELECT MAX(sent_at) as last_send FROM auto_newsletter_history";
            $stmt = $db->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['last_send']) {
                return strtotime($result['last_send']);
            }
        } catch (Exception $e) {
            self::log("Erreur getLastSendTime: " . $e->getMessage());
        }
        
        return 0;
    }
    
    /**
     * Mettre à jour le timestamp du dernier envoi
     */
    private static function updateLastSendTime() {
        $timeFile = dirname(self::$lockFile) . '/last_send.txt';
        file_put_contents($timeFile, time());
    }
    
    /**
     * Logger un message
     */
    private static function log($message) {
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }
}