<?php
// app/Controllers/AutoNewsletterController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AutoNewsletterController extends Controller {
    protected $db;

public function __construct($db = null) {
    $this->db = $db;
    // N'appelez PAS parent::__construct()
}

    
    
    /**
     * Page de configuration de la newsletter automatique
     */
    public function index() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $newsletterModel = $this->model('AutoNewsletter');
        
        // Récupérer la configuration actuelle
        $config = $newsletterModel->getConfig();
        
        // Récupérer les statistiques
        $stats = $newsletterModel->getStats();
        
        // Récupérer l'historique
        $history = $newsletterModel->getHistory(10);

        $this->view('administrateur/auto_newsletter', [
            'user' => $_SESSION['user'],
            'config' => $config,
            'stats' => $stats,
            'history' => $history
        ]);
    }

    /**
     * Sauvegarder la configuration
     */
    public function saveConfig() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $config = [
                'enabled' => isset($_POST['enabled']) ? 1 : 0,
                'frequency' => $_POST['frequency'] ?? 'weekly',
                'day_of_week' => $_POST['day_of_week'] ?? 'monday',
                'send_time' => $_POST['send_time'] ?? '09:00',
                'include_new_terrains' => isset($_POST['include_new_terrains']) ? 1 : 0,
                'include_tournaments' => isset($_POST['include_tournaments']) ? 1 : 0,
                'include_promotions' => isset($_POST['include_promotions']) ? 1 : 0,
                'include_statistics' => isset($_POST['include_statistics']) ? 1 : 0,
            ];

            $newsletterModel = $this->model('AutoNewsletter');
            
            if ($newsletterModel->saveConfig($config)) {
                $_SESSION['success'] = 'Configuration sauvegardée avec succès !';
            } else {
                $_SESSION['error'] = 'Erreur lors de la sauvegarde de la configuration';
            }
        }

        header('Location: ' . BASE_URL . 'auto_newsletter');
        exit;
    }

    /**
     * Abonnement depuis la landing page (visiteurs non connectés)
     */
    public function subscribe() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $acceptTerms = isset($_POST['accept_terms']);

        // Validation
        if (empty($nom) || empty($email)) {
            $_SESSION['newsletter_error'] = 'Veuillez remplir tous les champs';
            header('Location: ' . BASE_URL);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['newsletter_error'] = 'Adresse email invalide';
            header('Location: ' . BASE_URL);
            exit;
        }

        if (!$acceptTerms) {
            $_SESSION['newsletter_error'] = 'Veuillez accepter les conditions';
            header('Location: ' . BASE_URL);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();

            // Vérifier si l'email existe déjà
            $sql = "SELECT id, newsletter_subscribed FROM utilisateur WHERE email = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // L'utilisateur existe déjà
                if ($user['newsletter_subscribed']) {
                    $_SESSION['newsletter_error'] = '✉️ Vous êtes déjà abonné à notre newsletter !';
                } else {
                    // Réabonner l'utilisateur
                    $sql = "UPDATE utilisateur SET newsletter_subscribed = 1, newsletter_subscribed_at = NOW() WHERE id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$user['id']]);
                    
                    // Envoyer email de bienvenue
                    $this->sendWelcomeEmail($email, $nom);
                    
                    $_SESSION['newsletter_success'] = '🎉 Vous êtes maintenant abonné à notre newsletter !';
                }
            } else {
                // Créer un nouvel utilisateur (visiteur abonné)
                $sql = "INSERT INTO utilisateur (nom, prenom, email, newsletter_subscribed, newsletter_subscribed_at, role, mot_de_passe) 
                        VALUES (?, '', ?, 1, NOW(), 'client', '')";
                $stmt = $db->prepare($sql);
                $stmt->execute([$nom, $email]);
                
                // Envoyer email de bienvenue
                $this->sendWelcomeEmail($email, $nom);
                
                $_SESSION['newsletter_success'] = '🎉 Merci de votre abonnement ! Vous recevrez bientôt nos actualités.';
            }

        } catch (PDOException $e) {
            error_log("Erreur abonnement newsletter: " . $e->getMessage());
            $_SESSION['newsletter_error'] = 'Une erreur est survenue. Veuillez réessayer.';
        }

        header('Location: ' . BASE_URL);
        exit;
    }

    /**
     * Désabonner par email (depuis le lien dans l'email)
     */
    public function unsubscribe() {
        $email = $_GET['email'] ?? '';
        
        if (empty($email)) {
            echo "Email manquant";
            exit;
        }

        $newsletterModel = $this->model('AutoNewsletter');
        
        if ($newsletterModel->unsubscribeByEmail($email)) {
            $this->view('newsletter/unsubscribe_success', ['email' => $email]);
        } else {
            echo "Erreur lors du désabonnement";
        }
        exit;
    }

    /**
     * Envoyer un email de bienvenue
     */
    private function sendWelcomeEmail($email, $name) {
        $subject = "Bienvenue chez Book&Play ! 🎾";
        
        $htmlContent = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f7fa;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #064420 0%, #0a5c3c 100%);
            padding: 40px;
            text-align: center;
            color: white;
        }
        .logo { font-size: 32px; font-weight: 700; }
        .logo span { color: #CEFE24; }
        .content { padding: 40px; }
        .greeting { font-size: 24px; color: #064420; margin-bottom: 20px; font-weight: 600; }
        .message { color: #333; line-height: 1.8; margin-bottom: 20px; }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #CEFE24 0%, #b9ff00 100%);
            color: #064420;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            margin: 20px 0;
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
            font-size: 14px;
        }
        .footer a { color: #CEFE24; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Book<span>&</span>Play</div>
            <p style="margin-top: 10px;">Bienvenue dans notre communauté !</p>
        </div>
        
        <div class="content">
            <div class="greeting">Bonjour {$name} 👋</div>
            
            <div class="message">
                <p>Merci de vous être abonné à notre newsletter !</p>
                <p style="margin-top: 15px;">
                    Vous recevrez désormais régulièrement :
                </p>
                <ul style="margin-top: 10px; margin-left: 20px;">
                    <li>🆕 Les nouveaux terrains disponibles</li>
                    <li>🏆 Les tournois à venir</li>
                    <li>💰 Des promotions exclusives</li>
                    <li>📊 Les statistiques de la communauté</li>
                </ul>
            </div>
            
            <center>
                <a href="https://bookandplay.com" class="cta-button">
                    🎯 Découvrir nos terrains
                </a>
            </center>
        </div>
        
        <div class="footer">
            <p>© 2025 Book&Play. Tous droits réservés.</p>
            <p style="margin-top: 10px; font-size: 12px; opacity: 0.8;">
                <a href="{BASE_URL}auto_newsletter/unsubscribe?email={$email}">Se désabonner</a>
            </p>
        </div>
    </div>
</body>
</html>
HTML;

        return $this->sendEmail($email, $subject, $htmlContent);
    }

    /**
     * Générer et envoyer la newsletter automatiquement
     * Cette méthode sera appelée par un CRON job
     */
    /**
 * Générer et envoyer la newsletter automatiquement
 * Cette méthode sera appelée par un CRON job
 */
public function sendAutomatic() {
    // Activer les logs pour déboguer
    error_log("=== DÉBUT ENVOI AUTOMATIQUE NEWSLETTER ===");
    
    $newsletterModel = $this->model('AutoNewsletter');
    
    // Vérifier si la newsletter est activée
    $config = $newsletterModel->getConfig();
    
    if (!$config || !$config['enabled']) {
        error_log("❌ Newsletter automatique désactivée");
        echo "Newsletter automatique désactivée\n";
        exit;
    }
    
    error_log("✅ Configuration chargée - Jour: {$config['day_of_week']}, Heure: {$config['send_time']}");

    // CORRECTION 1: Vérifier si déjà envoyée AUJOURD'HUI (empêche les doublons)
    $history = $newsletterModel->getHistory(1);
    if (!empty($history)) {
        $lastSent = strtotime($history[0]['sent_at']);
        $todayStart = strtotime('today'); // Minuit aujourd'hui
        
        if ($lastSent >= $todayStart) {
            error_log("⚠️ Newsletter déjà envoyée aujourd'hui à " . date('H:i', $lastSent));
            echo "Newsletter déjà envoyée aujourd'hui\n";
            exit;
        }
    }

    // CORRECTION 2: Vérifier le jour SEULEMENT (plus souple)
    $currentDay = strtolower(date('l')); // monday, tuesday, etc.
    
    if ($config['day_of_week'] !== $currentDay) {
        error_log("⏳ Pas le bon jour. Configuré: {$config['day_of_week']}, Aujourd'hui: $currentDay");
        echo "Pas le bon jour pour envoyer (configuré: {$config['day_of_week']}, aujourd'hui: $currentDay)\n";
        exit;
    }
    
    error_log("✅ Bon jour détecté: $currentDay");

    // CORRECTION 3: Vérifier l'heure avec plus de flexibilité (3 heures au lieu de 1)
    $currentTime = date('H:i:s');
    $configTime = strtotime($config['send_time']);
    $now = strtotime($currentTime);
    $diff = abs($now - $configTime);
    
    // Accepter si on est dans les 3 heures APRÈS l'heure configurée
    // (Pour que le CRON puisse s'exécuter avec un peu de retard)
    $threeHours = 3 * 3600; // 3 heures en secondes
    
    if ($diff > $threeHours && $now < $configTime) {
        error_log("⏳ Trop tôt. Heure configurée: {$config['send_time']}, Maintenant: $currentTime");
        echo "Trop tôt pour envoyer (configuré: {$config['send_time']}, maintenant: $currentTime)\n";
        exit;
    }
    
    error_log("✅ Heure valide: $currentTime (configuré: {$config['send_time']})");

    // Générer le contenu de la newsletter
    error_log("📝 Génération du contenu...");
    $content = $this->generateNewsletterContent($config);
    error_log("✅ Contenu généré: {$content['subject']}");
    
    // Récupérer UNIQUEMENT les abonnés
    error_log("👥 Récupération des abonnés...");
    $subscribers = $newsletterModel->getSubscribers();
    
    if (empty($subscribers)) {
        error_log("❌ Aucun abonné trouvé");
        echo "Aucun abonné\n";
        exit;
    }
    
    error_log("✅ " . count($subscribers) . " abonné(s) trouvé(s)");

    // Envoyer à tous les abonnés
    $sent = 0;
    $failed = 0;
    
    foreach ($subscribers as $subscriber) {
        $email = $subscriber['email'];
        $name = $subscriber['prenom'] ?: $subscriber['nom'] ?: 'Utilisateur';
        
        error_log("📤 Envoi à $name ($email)...");
        
        $emailContent = $this->generateEmailHTML($content, $subscriber);
        
        if ($this->sendEmail($email, $content['subject'], $emailContent)) {
            $sent++;
            error_log("✅ Envoyé à $email");
        } else {
            $failed++;
            error_log("❌ Échec pour $email");
        }
        
        usleep(100000); // Pause de 0.1 seconde
    }

    // Enregistrer l'envoi
    $newsletterModel->logSend($content['subject'], $sent, $failed);

    error_log("📊 RÉSULTAT: $sent succès, $failed échecs");
    error_log("=== FIN ENVOI AUTOMATIQUE NEWSLETTER ===");
    
    echo "Newsletter envoyée : $sent succès, $failed échecs\n";
}

/**
 * BONUS: Méthode pour tester l'envoi automatique manuellement
 * Accessible via: /auto_newsletter/testAutomatic
 */
public function testAutomatic() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }

    echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}</style>";
    echo "<div style='max-width:800px;margin:0 auto;background:white;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);'>";
    echo "<h2>🧪 Test de l'envoi automatique</h2>";
    echo "<p>Cette page simule ce que ferait le CRON.</p>";
    echo "<hr style='margin:20px 0;'>";
    
    // Afficher l'état actuel
    $newsletterModel = $this->model('AutoNewsletter');
    $config = $newsletterModel->getConfig();
    
    echo "<h3>📋 Configuration actuelle :</h3>";
    echo "<ul>";
    echo "<li><strong>Activée :</strong> " . ($config['enabled'] ? '✅ Oui' : '❌ Non') . "</li>";
    echo "<li><strong>Jour configuré :</strong> {$config['day_of_week']}</li>";
    echo "<li><strong>Heure configurée :</strong> {$config['send_time']}</li>";
    echo "<li><strong>Jour actuel :</strong> " . strtolower(date('l')) . "</li>";
    echo "<li><strong>Heure actuelle :</strong> " . date('H:i:s') . "</li>";
    echo "</ul>";
    
    echo "<h3>🔍 Vérifications :</h3>";
    echo "<div style='background:#f9f9f9;padding:15px;border-radius:5px;font-family:monospace;'>";
    
    // Capturer la sortie de sendAutomatic()
    ob_start();
    $this->sendAutomatic();
    $output = ob_get_clean();
    
    echo nl2br(htmlspecialchars($output));
    echo "</div>";
    
    echo "<br><a href='" . BASE_URL . "auto_newsletter' style='display:inline-block;background:#064420;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>← Retour</a>";
    echo "</div>";
}

    /**
     * Récupérer les statistiques de la semaine
     */
    private function getWeeklyStats() {
        $db = Database::getInstance()->getConnection();
        
        // Nombre de réservations cette semaine
        $sql = "SELECT COUNT(*) as count FROM reservation WHERE date_reservation >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = $db->query($sql);
        $reservations = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Nombre de nouveaux utilisateurs
        $sql = "SELECT COUNT(*) as count FROM utilisateur WHERE id >= (SELECT MAX(id) - 10 FROM utilisateur)";
        $stmt = $db->query($sql);
        $newUsers = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        return [
            'reservations' => $reservations,
            'new_users' => $newUsers,
            'terrains_disponibles' => 25
        ];
    }

    /**
     * Générer le HTML de l'email
     */
    private function generateEmailHTML($content, $subscriber) {
        $name = $subscriber['prenom'] ?? 'Utilisateur';
        $email = $subscriber['email'] ?? '';
        $sections = '';
        
        foreach ($content['sections'] as $section) {
            $sections .= $this->renderSection($section);
        }
        
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$content['subject']}</title>
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
        .logo span { color: #CEFE24; }
        .date { color: rgba(255,255,255,0.8); font-size: 14px; }
        .content { padding: 40px 30px; }
        .greeting {
            font-size: 24px;
            color: #064420;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .section {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border-left: 4px solid #CEFE24;
        }
        .section-title {
            font-size: 20px;
            color: #064420;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .terrain-item, .tournament-item {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .item-title {
            font-weight: 600;
            color: #064420;
            margin-bottom: 5px;
        }
        .item-detail {
            color: #666;
            font-size: 14px;
            margin: 3px 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        .stat-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #064420;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #CEFE24 0%, #b9ff00 100%);
            color: #064420;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(206, 254, 36, 0.3);
        }
        .footer {
            background: #2c3e50;
            color: #ffffff;
            padding: 30px;
            text-align: center;
            font-size: 14px;
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
            <div class="date">Newsletter du {$content['subject']}</div>
        </div>
        
        <div class="content">
            <div class="greeting">Bonjour {$name} 👋</div>
            {$sections}
            
            <center>
                <a href="https://bookandplay.com/terrains" class="cta-button">
                    🎯 Voir tous les terrains
                </a>
            </center>
        </div>
        
        <div class="footer">
            <p>© 2025 Book&Play. Tous droits réservés.</p>
            <div class="unsubscribe">
                <a href="' . BASE_URL . 'auto_newsletter/unsubscribe?email=' . urlencode($email) . '">Se désabonner</a> | 
                <a href="' . BASE_URL . 'profile">Gérer mes préférences</a>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Rendre une section HTML
     */
    private function renderSection($section) {
        $html = '<div class="section">';
        
        if (isset($section['title'])) {
            $html .= '<div class="section-title">' . $section['title'] . '</div>';
        }
        
        switch ($section['type']) {
            case 'intro':
                $html .= '<p>' . $section['text'] . '</p>';
                break;
                
            case 'terrains':
                foreach ($section['items'] as $terrain) {
                    $html .= '<div class="terrain-item">';
                    $html .= '<div class="item-title">' . htmlspecialchars($terrain['nom_terrain']) . '</div>';
                    $html .= '<div class="item-detail">📍 ' . htmlspecialchars($terrain['localisation']) . '</div>';
                    $html .= '<div class="item-detail">💰 ' . htmlspecialchars($terrain['prix_heure']) . ' DH/heure</div>';
                    $html .= '<div class="item-detail">⚽ ' . htmlspecialchars($terrain['type_terrain']) . ' - ' . htmlspecialchars($terrain['format_terrain']) . '</div>';
                    $html .= '</div>';
                }
                break;
                
            case 'tournaments':
                foreach ($section['items'] as $tournament) {
                    $html .= '<div class="tournament-item">';
                    $html .= '<div class="item-title">🏆 ' . htmlspecialchars($tournament['nom_tournoi']) . '</div>';
                    $html .= '<div class="item-detail">📅 ' . date('d/m/Y', strtotime($tournament['date_debut'])) . '</div>';
                    $html .= '<div class="item-detail">👥 ' . htmlspecialchars($tournament['nb_equipes']) . ' équipes</div>';
                    $html .= '</div>';
                }
                break;
                
            case 'promotion':
                $html .= '<p style="font-size: 16px; line-height: 1.6;">' . $section['text'] . '</p>';
                break;
                
            case 'stats':
                $html .= '<div class="stats-grid">';
                $html .= '<div class="stat-box">';
                $html .= '<div class="stat-number">' . $section['data']['reservations'] . '</div>';
                $html .= '<div class="stat-label">Réservations</div>';
                $html .= '</div>';
                $html .= '<div class="stat-box">';
                $html .= '<div class="stat-number">' . $section['data']['new_users'] . '</div>';
                $html .= '<div class="stat-label">Nouveaux utilisateurs</div>';
                $html .= '</div>';
                $html .= '<div class="stat-box">';
                $html .= '<div class="stat-number">' . $section['data']['terrains_disponibles'] . '</div>';
                $html .= '<div class="stat-label">Terrains disponibles</div>';
                $html .= '</div>';
                $html .= '</div>';
                break;
        }
        
        $html .= '</div>';
        return $html;
    }
    /**
 * Point d'entrée pour le CRON
 * Accessible via: http://localhost/book-play-mvc/public/auto_newsletter/cron
 * 
 * Cette méthode peut être appelée:
 * 1. Via un CRON: curl http://localhost/book-play-mvc/public/auto_newsletter/cron
 * 2. Via le navigateur (pour tester)
 * 3. Via wget dans un CRON
 */
public function cron() {
    // Pas de vérification de session pour permettre l'accès au CRON
    
    // Log de démarrage
    error_log("=== CRON NEWSLETTER DÉCLENCHÉ ===");
    error_log("URL appelée: " . $_SERVER['REQUEST_URI']);
    error_log("Heure: " . date('Y-m-d H:i:s'));
    
    // En-têtes pour éviter les timeouts
    set_time_limit(300); // 5 minutes max
    header('Content-Type: text/plain; charset=utf-8');
    
    echo "🚀 CRON Newsletter Book&Play\n";
    echo "═══════════════════════════════════════\n";
    echo "Démarrage: " . date('Y-m-d H:i:s') . "\n\n";
    
    $newsletterModel = $this->model('AutoNewsletter');
    
    // 1. Vérifier si la newsletter est activée
    echo "📋 Vérification de la configuration...\n";
    $config = $newsletterModel->getConfig();
    
    if (!$config) {
        echo "❌ ERREUR: Configuration introuvable\n";
        error_log("❌ CRON: Configuration introuvable");
        exit;
    }
    
    if (!$config['enabled']) {
        echo "⏸️  Newsletter automatique désactivée\n";
        echo "💡 Activez-la dans l'interface admin\n";
        error_log("⏸️  CRON: Newsletter désactivée");
        exit;
    }
    
    echo "✅ Newsletter activée\n";
    echo "   Jour configuré: {$config['day_of_week']}\n";
    echo "   Heure configurée: {$config['send_time']}\n\n";
    
    // 2. Vérifier si déjà envoyée aujourd'hui
    echo "🔍 Vérification des envois du jour...\n";
    $history = $newsletterModel->getHistory(1);
    
    if (!empty($history)) {
        $lastSent = strtotime($history[0]['sent_at']);
        $todayStart = strtotime('today');
        
        if ($lastSent >= $todayStart) {
            $lastSentFormatted = date('H:i', $lastSent);
            echo "⏭️  Newsletter déjà envoyée aujourd'hui à $lastSentFormatted\n";
            echo "   Emails envoyés: {$history[0]['sent_count']}\n";
            echo "   Échecs: {$history[0]['failed_count']}\n";
            error_log("⏭️  CRON: Newsletter déjà envoyée aujourd'hui");
            exit;
        }
    }
    
    echo "✅ Aucun envoi aujourd'hui\n\n";
    
    // 3. Vérifier le jour
    $currentDay = strtolower(date('l'));
    echo "📅 Vérification du jour...\n";
    echo "   Jour actuel: $currentDay\n";
    echo "   Jour configuré: {$config['day_of_week']}\n";
    
    if ($config['day_of_week'] !== $currentDay) {
        echo "⏳ Ce n'est pas le bon jour\n";
        echo "💡 La newsletter est programmée pour {$config['day_of_week']}\n";
        error_log("⏳ CRON: Pas le bon jour ($currentDay vs {$config['day_of_week']})");
        exit;
    }
    
    echo "✅ Bon jour détecté\n\n";
    
    // 4. Vérifier l'heure (avec flexibilité de 3 heures)
    $currentTime = date('H:i:s');
    $configTime = strtotime($config['send_time']);
    $now = strtotime($currentTime);
    $diff = abs($now - $configTime);
    $threeHours = 3 * 3600;
    
    echo "⏰ Vérification de l'heure...\n";
    echo "   Heure actuelle: $currentTime\n";
    echo "   Heure configurée: {$config['send_time']}\n";
    echo "   Différence: " . round($diff / 60) . " minutes\n";
    
    // Si c'est trop tôt (plus de 3h avant l'heure configurée)
    if ($now < ($configTime - $threeHours)) {
        echo "⏳ Trop tôt pour envoyer\n";
        echo "💡 Attendez au moins " . date('H:i', $configTime - $threeHours) . "\n";
        error_log("⏳ CRON: Trop tôt");
        exit;
    }
    
    // Si c'est trop tard (plus de 3h après l'heure configurée)
    if ($now > ($configTime + $threeHours)) {
        echo "⏰ Dépassement de la fenêtre d'envoi\n";
        echo "💡 L'heure d'envoi était: {$config['send_time']}\n";
        echo "💡 Fenêtre: " . date('H:i', $configTime) . " - " . date('H:i', $configTime + $threeHours) . "\n";
        error_log("⏰ CRON: Hors de la fenêtre d'envoi");
        exit;
    }
    
    echo "✅ Heure valide (fenêtre de 3h)\n\n";
    
    // 5. Générer le contenu
    echo "📝 Génération du contenu...\n";
    flush();
    
    try {
        $content = $this->generateNewsletterContent($config);
        echo "✅ Contenu généré: {$content['subject']}\n";
        echo "   Sections incluses: " . count($content['sections']) . "\n\n";
    } catch (Exception $e) {
        echo "❌ ERREUR lors de la génération du contenu\n";
        echo "   Message: " . $e->getMessage() . "\n";
        error_log("❌ CRON: Erreur génération contenu - " . $e->getMessage());
        exit;
    }
    
    // 6. Récupérer les abonnés
    echo "👥 Récupération des abonnés...\n";
    flush();
    
    $subscribers = $newsletterModel->getSubscribers();
    
    if (empty($subscribers)) {
        echo "⚠️  Aucun abonné trouvé\n";
        echo "💡 Vérifiez que des utilisateurs ont newsletter_subscribed = 1\n";
        error_log("⚠️  CRON: Aucun abonné");
        exit;
    }
    
    echo "✅ " . count($subscribers) . " abonné(s) trouvé(s)\n\n";
    
    // 7. Envoi à tous les abonnés
    echo "📤 ENVOI EN COURS...\n";
    echo "───────────────────────────────────────\n";
    flush();
    
    $sent = 0;
    $failed = 0;
    $startTime = microtime(true);
    
    foreach ($subscribers as $index => $subscriber) {
        $email = $subscriber['email'];
        $name = $subscriber['prenom'] ?: $subscriber['nom'] ?: 'Utilisateur';
        $num = $index + 1;
        
        echo "[$num/" . count($subscribers) . "] $name ($email)... ";
        flush();
        
        try {
            $emailContent = $this->generateEmailHTML($content, $subscriber);
            
            if ($this->sendEmail($email, $content['subject'], $emailContent)) {
                echo "✅\n";
                $sent++;
                error_log("✅ CRON: Email envoyé à $email");
            } else {
                echo "❌\n";
                $failed++;
                error_log("❌ CRON: Échec envoi à $email");
            }
        } catch (Exception $e) {
            echo "❌ (erreur: {$e->getMessage()})\n";
            $failed++;
            error_log("❌ CRON: Exception pour $email - " . $e->getMessage());
        }
        
        flush();
        usleep(100000); // Pause de 0.1 seconde entre chaque email
    }
    
    $duration = round(microtime(true) - $startTime, 2);
    
    echo "───────────────────────────────────────\n\n";
    
    // 8. Enregistrer dans l'historique
    echo "💾 Enregistrement dans l'historique...\n";
    
    try {
        $newsletterModel->logSend($content['subject'], $sent, $failed);
        echo "✅ Historique enregistré\n\n";
    } catch (Exception $e) {
        echo "⚠️  Erreur lors de l'enregistrement\n";
        error_log("⚠️  CRON: Erreur enregistrement historique - " . $e->getMessage());
    }
    
    // 9. Résumé final
    echo "📊 RÉSUMÉ FINAL\n";
    echo "═══════════════════════════════════════\n";
    echo "✅ Envoyés avec succès: $sent\n";
    
    if ($failed > 0) {
        echo "❌ Échecs: $failed\n";
    }
    
    echo "⏱️  Durée totale: {$duration}s\n";
    echo "📅 Date: " . date('Y-m-d H:i:s') . "\n";
    echo "═══════════════════════════════════════\n";
    
    if ($sent > 0) {
        echo "🎉 Newsletter envoyée avec succès !\n";
        error_log("🎉 CRON: Newsletter envoyée - $sent succès, $failed échecs");
    } else {
        echo "⚠️  Aucun email n'a pu être envoyé\n";
        error_log("⚠️  CRON: Aucun email envoyé");
    }
    
    error_log("=== FIN CRON NEWSLETTER ===");
}

/**
 * Envoyer un email de test (BONUS)
 */
public function sendTest() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }

    $newsletterModel = $this->model('AutoNewsletter');
    $config = $newsletterModel->getConfig();
    
    // Générer le contenu
    $content = $this->generateNewsletterContent($config);
    
    // Envoyer à l'admin connecté
    $subscriber = [
        'prenom' => $_SESSION['user']['prenom'] ?? $_SESSION['user']['name'] ?? 'Admin',
        'nom' => $_SESSION['user']['nom'] ?? '',
        'email' => $_SESSION['user']['email'] ?? 'test@example.com'
    ];
    
    $emailContent = $this->generateEmailHTML($content, $subscriber);
    
    if ($this->sendEmail($subscriber['email'], $content['subject'], $emailContent)) {
        $_SESSION['success'] = "✅ Newsletter de test envoyée à {$subscriber['email']}";
    } else {
        $_SESSION['error'] = "❌ Échec de l'envoi de la newsletter de test";
    }

    header('Location: ' . BASE_URL . 'auto_newsletter');
    exit;
}

/**
 * Générer le contenu de la newsletter automatique
 */
private function generateNewsletterContent($config) {
    $content = [
        'subject' => '🎾 Newsletter Book&Play - ' . date('d/m/Y'),
        'sections' => []
    ];

    // Introduction
    $content['sections'][] = [
        'type' => 'intro',
        'text' => "Découvrez les dernières nouveautés de Book&Play cette semaine !"
    ];

    // Nouveaux terrains
    if ($config['include_new_terrains']) {
        $terrainModel = $this->model('Terrain');
        $newTerrains = $terrainModel->getRecentTerrains(5);
        
        if (!empty($newTerrains)) {
            $content['sections'][] = [
                'type' => 'terrains',
                'title' => '🆕 Nouveaux Terrains',
                'items' => $newTerrains
            ];
        }
    }

    // Tournois à venir
    if ($config['include_tournaments']) {
        $tournoiModel = $this->model('Tournoi');
        $tournaments = $tournoiModel->getUpcomingTournois(3);
        
        if (!empty($tournaments)) {
            $content['sections'][] = [
                'type' => 'tournaments',
                'title' => '🏆 Tournois à venir',
                'items' => $tournaments
            ];
        }
    }

    // Promotions
    if ($config['include_promotions']) {
        $content['sections'][] = [
            'type' => 'promotion',
            'title' => '🎉 Offre Spéciale',
            'text' => 'Profitez de -15% sur toutes vos réservations ce week-end avec le code : WEEKEND15'
        ];
    }

    // Statistiques
    if ($config['include_statistics']) {
        $stats = $this->getWeeklyStats();
        $content['sections'][] = [
            'type' => 'stats',
            'title' => '📊 Cette semaine sur Book&Play',
            'data' => $stats
        ];
    }

    return $content;
}

    /**
     * Envoyer un email avec PHPMailer
     */
    private function sendEmail($to, $subject, $htmlContent) {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'] ?? '';
            $mail->Password = $_ENV['SMTP_PASSWORD'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = intval($_ENV['SMTP_PORT'] ?? 587);
            
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            $mail->setFrom('noreply@bookandplay.com', 'Book&Play');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlContent;
            $mail->CharSet = 'UTF-8';
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("PHPMailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
    /**
 * Forcer l'envoi immédiat de la newsletter
 */
public function sendNow() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }

    // Activer l'affichage des erreurs pour déboguer
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    echo "<style>body{font-family:Arial;padding:20px;}</style>";
    echo "<h2>📧 Envoi de la newsletter en cours...</h2>";
    echo "<hr>";

    $newsletterModel = $this->model('AutoNewsletter');
    
    // Vérifier la configuration
    $config = $newsletterModel->getConfig();
    
    if (!$config) {
        echo "❌ <strong>Erreur :</strong> Configuration introuvable<br>";
        echo "<a href='" . BASE_URL . "auto_newsletter'>← Retour</a>";
        exit;
    }

    if (!$config['enabled']) {
        echo "⚠️ <strong>Attention :</strong> La newsletter automatique est désactivée.<br>";
        echo "Activez-la dans la configuration pour pouvoir envoyer.<br>";
        echo "<a href='" . BASE_URL . "auto_newsletter'>← Retour</a>";
        exit;
    }

    echo "✅ Configuration OK<br>";
    echo "📊 Contenu à inclure : ";
    $includes = [];
    if ($config['include_new_terrains']) $includes[] = "Terrains";
    if ($config['include_tournaments']) $includes[] = "Tournois";
    if ($config['include_promotions']) $includes[] = "Promotions";
    if ($config['include_statistics']) $includes[] = "Stats";
    echo implode(", ", $includes) . "<br><br>";

    // Générer le contenu
    echo "📝 Génération du contenu...<br>";
    $content = $this->generateNewsletterContent($config);
    echo "✅ Contenu généré : <strong>{$content['subject']}</strong><br><br>";
    
    // Récupérer les abonnés
    echo "👥 Recherche des abonnés...<br>";
    $subscribers = $newsletterModel->getSubscribers();
    
    if (empty($subscribers)) {
        echo "❌ <strong>Aucun abonné trouvé !</strong><br><br>";
        echo "<strong>Solutions :</strong><br>";
        echo "1. Vérifiez que la colonne 'newsletter_subscribed' existe :<br>";
        echo "<code>SHOW COLUMNS FROM utilisateur LIKE 'newsletter_subscribed';</code><br><br>";
        echo "2. Créez un abonné de test :<br>";
        echo "<code>UPDATE utilisateur SET newsletter_subscribed = 1, newsletter_subscribed_at = NOW() WHERE id = 1;</code><br><br>";
        echo "3. Ou utilisez le formulaire de la landing page<br><br>";
        echo "<a href='" . BASE_URL . "auto_newsletter'>← Retour</a>";
        exit;
    }

    echo "✅ <strong>" . count($subscribers) . " abonné(s) trouvé(s)</strong><br><br>";
    echo "<div style='background:#f0f0f0;padding:10px;border-radius:5px;margin:10px 0;'>";
    
    // Envoyer à tous les abonnés
    $sent = 0;
    $failed = 0;
    
    foreach ($subscribers as $subscriber) {
        $email = $subscriber['email'];
        $name = $subscriber['prenom'] ?: $subscriber['nom'] ?: 'Utilisateur';
        
        echo "📤 Envoi à <strong>$name</strong> ($email)... ";
        flush();
        
        $emailContent = $this->generateEmailHTML($content, $subscriber);
        
        if ($this->sendEmail($email, $content['subject'], $emailContent)) {
            echo "<span style='color:green;'>✅ Succès</span><br>";
            $sent++;
        } else {
            echo "<span style='color:red;'>❌ Échec</span><br>";
            $failed++;
        }
        
        flush();
        usleep(100000); // Pause de 0.1 seconde
    }
    
    echo "</div>";
    echo "<hr>";

    // Enregistrer l'envoi
    $newsletterModel->logSend($content['subject'], $sent, $failed);

    // Résultat final
    if ($sent > 0) {
        echo "<h3 style='color:green;'>✅ Newsletter envoyée avec succès !</h3>";
        echo "<p><strong>📊 Résultats :</strong></p>";
        echo "<ul>";
        echo "<li>✅ Envoyés : <strong>$sent</strong></li>";
        if ($failed > 0) {
            echo "<li>❌ Échecs : <strong>$failed</strong></li>";
        }
        echo "</ul>";
    } else {
        echo "<h3 style='color:red;'>❌ Aucun email n'a pu être envoyé</h3>";
        echo "<p><strong>Vérifiez :</strong></p>";
        echo "<ul>";
        echo "<li>Votre configuration SMTP dans le fichier .env</li>";
        echo "<li>Que vous utilisez SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD (pas MAIL_*)</li>";
        echo "<li>Votre mot de passe d'application Gmail (16 caractères)</li>";
        echo "</ul>";
    }

    echo "<br><a href='" . BASE_URL . "auto_newsletter' style='display:inline-block;background:#064420;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>← Retour au tableau de bord</a>";
    exit;
}
}