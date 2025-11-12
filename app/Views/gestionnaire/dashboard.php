<?php
// app/views/gestionnaire/dashboard.php - Tableau de bord du gestionnaire
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
        
        /* Animations for notifications */
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        /* Animation for new terrain items */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .activity-item.new-item {
            animation: fadeInDown 0.5s ease-out;
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
            <a href="<?php echo $baseUrl; ?>terrain/gestionnaireTerrains" class="nav-item">
                <i class="fas fa-map-marked-alt"></i>
                <span>Gestion des Terrains</span>
            </a>
            <a href="<?php echo $baseUrl; ?>facture" class="nav-item">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Gestion des Factures</span>
            </a>
            <a href="<?php echo $baseUrl; ?>factures" class="nav-item">
                <i class="fas fa-calendar-check"></i>
                <span>Demandes de Réservation</span>
            </a>
            <a href="<?php echo $baseUrl; ?>tournoi" class="nav-item">
                <i class="fas fa-trophy"></i>
                <span>Tournois & Demandes</span>
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
            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" style="padding: 15px; margin-bottom: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>
            
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

            <!-- Demandes de Tournois Récentes -->
            <?php
            // Récupérer les demandes de tournois récentes
            require_once __DIR__ . '/../../Controllers/TournoiController.php';
            require_once __DIR__ . '/../../Core/Database.php';
            try {
                $db = Database::getInstance()->getConnection();
                $sql = "SELECT t.*, u.prenom AS client_prenom, u.nom AS client_nom
                        FROM tournoi t
                        INNER JOIN demande d ON t.id_tournoi = d.id_tournoi
                        INNER JOIN utilisateur u ON d.id_client = u.id
                        WHERE t.id_gestionnaire = ?
                        ORDER BY t.date_debut DESC
                        LIMIT 3";
                $stmt = $db->prepare($sql);
                $stmt->execute([$currentUser['id']]);
                $demandesTournois = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $demandesTournois = [];
            }
            ?>
            
            <?php if (!empty($demandesTournois)): ?>
            <div class="content-section" style="margin-bottom: 30px;">
                <div class="section-header">
                    <h2><i class="fas fa-trophy"></i> Demandes de Tournois Récentes</h2>
                    <a href="<?php echo $baseUrl; ?>tournoi?section=demandes" class="btn btn-primary" style="text-decoration: none; padding: 10px 20px; background: #064420; color: white; border-radius: 8px;">
                        Voir les demandes
                    </a>
                </div>
                <div class="activities-list">
                    <?php foreach ($demandesTournois as $demande): ?>
                        <div class="activity-item">
                            <div class="activity-content">
                                <h4><?= htmlspecialchars($demande['nom_tournoi']) ?></h4>
                                <p><i class="fas fa-user"></i> Client: <?= htmlspecialchars($demande['client_prenom'] . ' ' . $demande['client_nom']) ?></p>
                                <div class="terrain-info-row">
                                    <span><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($demande['date_debut'])) ?></span>
                                    <span><i class="fas fa-users"></i> <?= htmlspecialchars($demande['nb_equipes']) ?> équipes</span>
                                </div>
                            </div>
                            <span class="status-badge" style="background-color: <?= $demande['status'] === 'en attente' ? '#ffc107' : ($demande['status'] === 'accepté' ? '#28a745' : '#dc3545') ?>">
                                <?= htmlspecialchars($demande['status']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Recent Activities -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Mes Terrains</h2>
<!--                    <button onclick="openAddModal()" class="btn btn-primary">-->
<!--                        <i class="fas fa-plus"></i> Ajouter un terrain-->
<!--                    </button>-->
                </div>

                <div class="activities-list">
                    <?php if (!empty($stats['recent_activities'])): ?>
                        <?php foreach ($stats['recent_activities'] as $terrain): ?>
                            <div class="activity-item" data-terrain-id="<?php echo $terrain['id']; ?>">
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
                        <a class="no-data">
                            <i class="fas fa-map-marked-alt" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                            <p>Aucun terrain trouvé</p>
<!--                            <a href="--><?php //echo $baseUrl; ?><!--terrain/gestionnaireTerrains"><button class="btn btn-primary">-->
<!--                                <i class="fas fa-plus"></i> gérer vos terrain-->
<!--                            </button></a>-->
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

    <!-- Modal Ajouter Terrain -->
    <div id="addTerrainModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background-color: #fefefe; margin: 2% auto; padding: 0; border-radius: 12px; width: 90%; max-width: 700px; box-shadow: 0 5px 30px rgba(0,0,0,0.3); max-height: 90vh; overflow-y: auto;">
            <div class="modal-header" style="padding: 20px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; font-size: 24px;">Ajouter un nouveau terrain</h2>
                <button class="close" onclick="closeAddModal()" style="color: white; font-size: 32px; font-weight: bold; cursor: pointer; background: none; border: none; padding: 0; line-height: 1;">&times;</button>
            </div>
            <form action="<?= BASE_URL ?>terrain/store" method="POST" enctype="multipart/form-data" id="addTerrainForm">
                <div class="modal-body" style="padding: 30px;">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="nom_terrain" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">Nom du terrain <span style="color: #dc3545;">*</span></label>
                        <input type="text" class="form-control" id="nom_terrain" name="nom_terrain" 
                               placeholder="Ex: Complexe Sportif..." required maxlength="255" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="localisation" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">Localisation <span style="color: #dc3545;">*</span></label>
                        <input type="text" class="form-control" id="localisation" name="localisation" 
                               placeholder="Adresse complète" required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="prix" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">Prix/heure <span style="color: #dc3545;">*</span></label>
                        <input type="number" class="form-control" id="prix" name="prix"
                               placeholder="Prix/heure" required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="image" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">Image du terrain <span style="color: #dc3545;">*</span></label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                        <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">Formats acceptés: JPG, JPEG, PNG, GIF (max 5MB)</div>
                    </div>

                    <div style="display: flex; gap: 20px; margin: 0 -10px;">
                        <div style="flex: 1; padding: 0 10px;">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label for="type_terrain" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">Type de terrain <span style="color: #dc3545;">*</span></label>
                                <select class="form-select" id="type_terrain" name="type_terrain" required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                                    <option value="" disabled selected>Sélectionner un type</option>
                                    <option value="Gazon naturel">Gazon naturel</option>
                                    <option value="Gazon synthétique">Gazon synthétique</option>
                                    <option value="Terre / Sol">Terre / Sol</option>
                                    <option value="Terrain couvert / Salle">Terrain couvert / Salle</option>
                                </select>
                            </div>
                        </div>
                        <div style="flex: 1; padding: 0 10px;">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label for="format_terrain" class="form-label" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">Format du terrain <span style="color: #dc3545;">*</span></label>
                                <select class="form-select" id="format_terrain" name="format_terrain" required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                                    <option value="" disabled selected>Sélectionner un format</option>
                                    <option value="5v5">5v5</option>
                                    <option value="6v6">6v6</option>
                                    <option value="7v7">7v7</option>
                                    <option value="8v8">8v8</option>
                                    <option value="9v9">9v9</option>
                                    <option value="10v10">10v10</option>
                                    <option value="11v11">11v11</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 20px 30px; background-color: #f8f9fa; border-radius: 0 0 12px 12px; display: flex; justify-content: space-between; gap: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()" style="padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; border: none; cursor: pointer; background-color: #6c757d; color: white;">Annuler</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                        <i class="fas fa-plus-circle"></i> Ajouter le terrain
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo $baseUrl; ?>js/dashboard_gest.js"></script>
    <script src="<?php echo $baseUrl; ?>js/terrain-realtime.js?v=<?php echo time(); ?>"></script>
    <script>
    // Fonctions pour gérer la modale d'ajout
    function openAddModal() {
        document.getElementById('addTerrainModal').style.display = 'block';
        document.getElementById('addTerrainForm').reset();
    }

    function closeAddModal() {
        document.getElementById('addTerrainModal').style.display = 'none';
    }

    // Fermer la modale en cliquant en dehors
    window.onclick = function(event) {
        const addModal = document.getElementById('addTerrainModal');
        if (event.target == addModal) {
            closeAddModal();
        }
    }

    // Fermer avec la touche Échap
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAddModal();
        }
    });

    // Fonction utilitaire pour échapper le HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Fonction de rendu pour les éléments de terrain du tableau de bord
    function renderDashboardTerrain(terrain) {
        const div = document.createElement('div');
        div.classList.add('activity-item', 'new-item');
        div.setAttribute('data-terrain-id', terrain.id_terrain);
        
        let imageHtml = '';
        if (terrain.image) {
            imageHtml = `
                <img src="<?= BASE_URL ?>images/${terrain.image}" 
                     alt="${escapeHtml(terrain.nom_terrain)}" 
                     class="terrain-image"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="terrain-image-placeholder" style="display:none;">
                    <i class="fas fa-futbol"></i>
                </div>
            `;
        } else {
            imageHtml = `
                <div class="terrain-image-placeholder">
                    <i class="fas fa-futbol"></i>
                </div>
            `;
        }
        
        const statusColor = terrain.statut === 'disponible' ? '#28a745' : '#dc3545';
        const statusIcon = terrain.statut === 'disponible' ? 'fas fa-check-circle' : 'fas fa-times-circle';
        
        div.innerHTML = `
            ${imageHtml}
            <div class="activity-content">
                <h4>${escapeHtml(terrain.nom_terrain)}</h4>
                <p><i class="fas fa-map-marker-alt"></i> ${escapeHtml(terrain.localisation)}</p>
                <div class="terrain-info-row">
                    <span><i class="fas fa-layer-group"></i> ${escapeHtml(terrain.type_terrain)}</span>
                    <span><i class="fas fa-coins"></i> ${escapeHtml(terrain.prix_heure)} DH/h</span>
                </div>
                <div class="terrain-stats-row">
                    <span class="badge badge-warning">0 en attente</span>
                    <span class="badge badge-success">0 acceptées</span>
                </div>
            </div>
            <span class="status-badge" style="background-color: ${statusColor}">
                <i class="${statusIcon}"></i> 
                ${escapeHtml(terrain.statut)}
            </span>
        `;
        
        return div;
    }

    // Initialiser la surveillance en temps réel
    const terrainMonitor = new TerrainRealtimeMonitor({
        baseUrl: '<?= BASE_URL ?>',
        containerSelector: '.activities-list',
        renderFunction: renderDashboardTerrain,
        pollingInterval: 3000
    });

    // Initialiser au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($stats['recent_activities'])): ?>
            const terrainIds = [<?php echo implode(',', array_column($stats['recent_activities'], 'id')); ?>];
            const maxId = Math.max(...terrainIds);
            terrainMonitor.init(maxId);
        <?php else: ?>
            terrainMonitor.init(0);
        <?php endif; ?>
        
        // Démarrer le polling pour la surveillance en temps réel
        terrainMonitor.demarrerPolling();
    });

    // Gérer la soumission du formulaire via AJAX
    document.getElementById('addTerrainForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('<?= BASE_URL ?>terrain/store', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                terrainMonitor.afficherNotification('Terrain ajouté avec succès !');
                closeAddModal();
                
                // Ajouter au DOM
                const container = document.querySelector('.activities-list');
                const noData = container.querySelector('.no-data');
                if (noData) noData.remove();
                
                const element = renderDashboardTerrain(data.terrain);
                container.insertBefore(element, container.firstChild);
                
                // Mettre à jour le dernier ID
                terrainMonitor.mettreAJourDernierId(data.terrain.id_terrain);
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur AJAX :', error);
            alert('Erreur lors de l\'ajout du terrain');
        });
    });
    </script>
</body>
</html>