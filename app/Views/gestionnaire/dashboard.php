<?php
// app/views/gestionnaire/dashboard.php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;

require_once __DIR__ . '/../../Core/Model.php';
require_once __DIR__ . '/../../Models/Dashboard_gestionnaire.php';
$dashboardModel = new Dashboard();
$stats = $dashboardModel->getManagerStats($currentUser['id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/navbar_gestionnaire.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/footer_gestionnaire.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/dashboard_gest.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .terrain-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            margin-right: 20px;
            border: 3px solid #e0e0e0;
            flex-shrink: 0;
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 25px;
            background: white;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .activity-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        
        .terrain-image-placeholder {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
        }
        
        .terrain-image-placeholder i {
            font-size: 48px;
            color: white;
        }

        .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-content h4 {
            margin: 0 0 12px 0;
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
        }

        .activity-content p {
            margin: 8px 0;
            font-size: 15px;
            color: #7f8c8d;
        }

        .terrain-info-row {
            display: flex;
            gap: 20px;
            margin: 12px 0;
            font-size: 14px;
            color: #95a5a6;
        }

        .terrain-info-row span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .terrain-stats-row {
            display: flex;
            gap: 12px;
            margin-top: 12px;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 500;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .no-data {
            text-align: center;
            padding: 60px;
            color: #95a5a6;
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="<?php echo $baseUrl; ?>images/logo.png" alt="Logo" class="logo">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a href="<?php echo $baseUrl; ?>dashboard/gestionnaire" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo $baseUrl; ?>terrains" class="nav-item">
                <i class="fas fa-map-marked-alt"></i>
                <span>Gestion des Terrains</span>
            </a>
            <a href="<?php echo $baseUrl; ?>reservations" class="nav-item">
                <i class="fas fa-calendar-check"></i>
                <span>Demandes de Réservation</span>
            </a>
            <a href="<?php echo $baseUrl; ?>tournois" class="nav-item">
                <i class="fas fa-trophy"></i>
                <span>Gestion des Tournois</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?>
                </div>
                <div class="user-details">
                    <span class="user-name"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                    <span class="user-role">Gestionnaire</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="navbar-left">
                <h1>Tableau de bord</h1>
                <p class="subtitle">Vue d'ensemble de votre activité</p>
            </div>
            <div class="navbar-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Rechercher...">
                </div>
                <div class="notifications">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="badge"><?php echo $stats['notifications_count'] ?? 0; ?></span>
                    </button>
                </div>
                <div class="user-menu">
                    <button class="user-menu-btn">
                        <div class="user-avatar-small">
                            <?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?>
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="<?php echo $baseUrl; ?>profile"><i class="fas fa-user"></i> Mon Profil</a>
                        <a href="<?php echo $baseUrl; ?>settings"><i class="fas fa-cog"></i> Paramètres</a>
                        <hr>
                        <a href="<?php echo $baseUrl; ?>logout" class="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-container">
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['terrains_count'] ?? 0; ?></h3>
                        <p>Terrains créés</p>
                        <span class="stat-change positive">+2 la semaine dernière</span>
                    </div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['reservations_en_attente'] ?? 0; ?></h3>
                        <p>En attente</p>
                        <span class="stat-change"><?php echo $stats['reservations_today'] ?? 0; ?> réservation(s) aujourd'hui</span>
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['reservations_confirmees'] ?? 0; ?></h3>
                        <p>Gains enregistrés</p>
                        <span class="stat-change positive">+8 de la semaine passée</span>
                    </div>
                </div>

                <div class="stat-card yellow">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['clients_count'] ?? 0; ?></h3>
                        <p>Réserves en cours</p>
                        <span class="stat-change">+3 équipes inscrites</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Mes Terrains</h2>
                    <a href="<?php echo $baseUrl; ?>terrains/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un terrain
                    </a>
                </div>

                <div class="activities-list">
                    <?php if (!empty($stats['recent_activities'])): ?>
                        <?php foreach ($stats['recent_activities'] as $terrain): ?>
                            <div class="activity-item">
                                <!-- Image -->
                                <?php if (!empty($terrain['image'])): ?>
                                    <?php 
                                        $imagePath = $baseUrl . 'images/' . $terrain['image'];
                                        $rootDir = realpath(__DIR__ . '/../../../');
                                        $absolutePath = $rootDir . '/public/images/' . $terrain['image'];
                                        $imageExists = file_exists($absolutePath);
                                    ?>
                                    
                                    <?php if ($imageExists): ?>
                                        <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                             alt="<?php echo htmlspecialchars($terrain['nom']); ?>" 
                                             class="terrain-image"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="terrain-image-placeholder" style="display:none;">
                                            <i class="fas fa-futbol"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="terrain-image-placeholder">
                                            <i class="fas fa-futbol"></i>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="terrain-image-placeholder">
                                        <i class="fas fa-futbol"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Contenu -->
                                <div class="activity-content">
                                    <h4><?php echo htmlspecialchars($terrain['nom']); ?></h4>
                                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($terrain['localisation']); ?></p>
                                    <div class="terrain-info-row">
                                        <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($terrain['horaires']); ?></span>
                                        <span><i class="fas fa-coins"></i> <?php echo htmlspecialchars($terrain['prix']); ?></span>
                                    </div>
                                    <div class="terrain-stats-row">
                                        <span class="badge badge-warning">
                                            <?php echo (int)$terrain['reservations_en_attente']; ?> en attente
                                        </span>
                                        <span class="badge badge-success">
                                            <?php echo (int)$terrain['reservations_acceptees']; ?> acceptées
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Badge de statut uniquement -->
                                <span class="status-badge" style="background-color: <?php echo htmlspecialchars($terrain['status_color']); ?>">
                                    <i class="<?php echo htmlspecialchars($terrain['status_icon']); ?>"></i> 
                                    <?php echo htmlspecialchars($terrain['statut']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-map-marked-alt" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                            <p>Aucun terrain trouvé</p>
                            <a href="<?php echo $baseUrl; ?>terrains/create" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Créer un terrain
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tous droits réservés.</p>
                <div class="footer-links">
                    <a href="#">Aide</a>
                    <a href="#">Conditions d'utilisation</a>
                    <a href="#">Confidentialité</a>
                </div>
            </div>
        </footer>
    </main>

    <script src="<?php echo $baseUrl; ?>js/dashboard_gest.js"></script>
</body>
</html>