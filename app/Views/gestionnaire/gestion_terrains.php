<?php
// app/views/gestionnaire/gestion_terrains.php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;

// Les données des terrains sont passées depuis le contrôleur
$terrains = $terrains ?? [];
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
        /* Styles pour les modales */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 2% auto;
            padding: 0;
            border: none;
            border-radius: 12px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.3);
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 20px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 24px;
        }
        
        .close {
            color: white;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
        }
        
        .close:hover,
        .close:focus {
            opacity: 0.8;
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .form-control, .form-select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .row {
            display: flex;
            gap: 20px;
            margin: 0 -10px;
        }
        
        .col-md-6 {
            flex: 1;
            padding: 0 10px;
        }
        
        .text-danger {
            color: #dc3545;
        }
        
        .form-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .modal-footer {
            padding: 20px 30px;
            background-color: #f8f9fa;
            border-radius: 0 0 12px 12px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .error-message {
            color: #dc3545;
            margin-bottom: 1rem;
            padding: 0.75rem 1.25rem;
            border: 1px solid #f5c6cb;
            border-radius: 0.25rem;
            background-color: #f8d7da;
        }
        
        .terrain-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            margin-right: 20px;
            border: 3px solid #e0e0e0;
            flex-shrink: 0;
        }
        
        .terrain-image-placeholder {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            margin-right: 20px;
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
            position: relative;
        }
        
        .activity-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        
        .activity-content {
            flex: 1;
            padding-right: 20px;
        }
        
        .activity-content h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 20px;
            font-weight: 600;
        }
        
        .activity-content p {
            margin: 8px 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .terrain-info-row {
            display: flex;
            gap: 20px;
            margin-top: 12px;
        }
        
        .terrain-info-row span {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #34495e;
            font-size: 14px;
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
        
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
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
            margin-bottom: 10px;
        }
        
        .terrain-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #000;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        .no-data {
            text-align: center;
            padding: 60px;
            color: #95a5a6;
        }
        
        .activities-list {
            padding: 20px;
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
        <a href="<?php echo $baseUrl; ?>dashboard/gestionnaire" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="<?php echo $baseUrl; ?>terrain/gestionnaireTerrains" class="nav-item active">
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
            <h1>Gestion des Terrains</h1>
            <p class="subtitle">Gérez vos terrains de sport</p>
        </div>
        <div class="navbar-right">
            <button onclick="openAddModal()" class="btn btn-primary" style="margin-right: 15px;">
                <i class="fas fa-plus"></i> Ajouter un terrain
            </button>
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
    
    <div class="dashboard-container" style="padding: 20px;">
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
    </div>
    
    <div class="activities-list">
        <?php if (!empty($terrains)): ?>
            <?php foreach ($terrains as $terrain): ?>
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
                                 alt="<?php echo htmlspecialchars($terrain['nom_terrain']); ?>"
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
                        <h4><?php echo htmlspecialchars($terrain['nom_terrain']); ?></h4>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($terrain['localisation']); ?></p>
                        <div class="terrain-info-row">
                            <span><i class="fas fa-layer-group"></i> <?php echo htmlspecialchars($terrain['type_terrain']); ?></span>
                            <span><i class="fas fa-coins"></i> <?php echo htmlspecialchars($terrain['prix_heure']); ?> DH/h</span>
                        </div>
                        <div class="terrain-stats-row">
                            <span class="badge badge-info">
                                <i class="fas fa-users"></i> <?php echo htmlspecialchars($terrain['format_terrain']); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Actions and Status -->
                    <div class="terrain-actions">
                        <?php
                        $statusColor = $terrain['statut'] === 'disponible' ? '#28a745' : '#dc3545';
                        $statusIcon = $terrain['statut'] === 'disponible' ? 'fas fa-check-circle' : 'fas fa-times-circle';
                        ?>
                        <span class="status-badge" style="background-color: <?php echo $statusColor; ?>">
                            <i class="<?php echo $statusIcon; ?>"></i>
                            <?php echo htmlspecialchars($terrain['statut']); ?>
                        </span>
                        <div class="action-buttons">
                            <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($terrain)); ?>)" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                            <a href="<?php echo $baseUrl; ?>terrain/delete/<?php echo $terrain['id_terrain']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce terrain ?');">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-map-marked-alt" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                <p>Aucun terrain trouvé</p>
                <button onclick="openAddModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Créer un terrain
                </button>
            </div>
        <?php endif; ?>
    </div>
    </div>
    </div>

    <!-- Modal Ajouter Terrain -->
    <div id="addTerrainModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Ajouter un nouveau terrain</h2>
                <button class="close" onclick="closeAddModal()">&times;</button>
            </div>
            <form action="<?= BASE_URL ?>terrain/store" method="POST" enctype="multipart/form-data" id="addTerrainForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nom_terrain" class="form-label">Nom du terrain <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nom_terrain" name="nom_terrain" 
                               placeholder="Ex: Complexe Sportif..." required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="localisation" class="form-label">Localisation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="localisation" name="localisation" 
                               placeholder="Adresse complète" required>
                    </div>

                    <div class="form-group">
                        <label for="prix" class="form-label">Prix/heure <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="prix" name="prix"
                               placeholder="Prix/heure" required>
                    </div>

                    <div class="form-group">
                        <label for="image" class="form-label">Image du terrain <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <div class="form-text">Formats acceptés: JPG, JPEG, PNG, GIF (max 5MB)</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type_terrain" class="form-label">Type de terrain <span class="text-danger">*</span></label>
                                <select class="form-select" id="type_terrain" name="type_terrain" required>
                                    <option value="" disabled selected>Sélectionner un type</option>
                                    <option value="Gazon naturel">Gazon naturel</option>
                                    <option value="Gazon synthétique">Gazon synthétique</option>
                                    <option value="Terre / Sol">Terre / Sol</option>
                                    <option value="Terrain couvert / Salle">Terrain couvert / Salle</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="format_terrain" class="form-label">Format du terrain <span class="text-danger">*</span></label>
                                <select class="form-select" id="format_terrain" name="format_terrain" required>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Ajouter le terrain
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Modifier Terrain -->
    <div id="editTerrainModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Modifier le terrain</h2>
                <button class="close" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="editTerrainForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nom_terrain" class="form-label">Nom du terrain <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nom_terrain" name="nom_terrain" 
                               placeholder="Ex: Complexe Sportif..." required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="edit_localisation" class="form-label">Localisation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_localisation" name="localisation" 
                               placeholder="Adresse complète" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_prix" class="form-label">Prix/heure <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_prix" name="prix"
                               placeholder="Prix/heure" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_image" class="form-label">Image du terrain</label>
                        <div id="current_image_container" style="margin-bottom: 10px;"></div>
                        <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                        <div class="form-text">Formats acceptés: JPG, JPEG, PNG, GIF (max 5MB). Laissez vide pour conserver l'image actuelle.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_type_terrain" class="form-label">Type de terrain <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_type_terrain" name="type_terrain" required>
                                    <option value="" disabled>Sélectionner un type</option>
                                    <option value="Gazon naturel">Gazon naturel</option>
                                    <option value="Gazon synthétique">Gazon synthétique</option>
                                    <option value="Terre / Sol">Terre / Sol</option>
                                    <option value="Terrain couvert / Salle">Terrain couvert / Salle</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_format_terrain" class="form-label">Format du terrain <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_format_terrain" name="format_terrain" required>
                                    <option value="" disabled>Sélectionner un format</option>
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

                    <div class="form-group">
                        <label for="edit_statut" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_statut" name="statut" required>
                            <option value="disponible">Disponible</option>
                            <option value="non disponible">Indisponible</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
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
<script>
// Fonctions pour gérer les modales
function openAddModal() {
    document.getElementById('addTerrainModal').style.display = 'block';
    document.getElementById('addTerrainForm').reset();
}

function closeAddModal() {
    document.getElementById('addTerrainModal').style.display = 'none';
}

function openEditModal(terrain) {
    const modal = document.getElementById('editTerrainModal');
    const form = document.getElementById('editTerrainForm');
    
    // Définir l'action du formulaire
    form.action = '<?= BASE_URL ?>terrain/updateTerrain/' + terrain.id_terrain;
    
    // Remplir les champs du formulaire
    document.getElementById('edit_nom_terrain').value = terrain.nom_terrain;
    document.getElementById('edit_localisation').value = terrain.localisation;
    document.getElementById('edit_prix').value = terrain.prix_heure;
    document.getElementById('edit_type_terrain').value = terrain.type_terrain;
    document.getElementById('edit_format_terrain').value = terrain.format_terrain;
    document.getElementById('edit_statut').value = terrain.statut;
    
    // Afficher l'image actuelle si elle existe
    const imageContainer = document.getElementById('current_image_container');
    if (terrain.image) {
        imageContainer.innerHTML = '<img src="<?= BASE_URL ?>images/' + terrain.image + '" alt="Image actuelle" style="max-width: 200px; border-radius: 8px;"><p style="font-size: 12px; color: #6c757d; margin-top: 5px;">Image actuelle</p>';
    } else {
        imageContainer.innerHTML = '';
    }
    
    modal.style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editTerrainModal').style.display = 'none';
}

// Fermer la modale en cliquant en dehors
window.onclick = function(event) {
    const addModal = document.getElementById('addTerrainModal');
    const editModal = document.getElementById('editTerrainModal');
    if (event.target == addModal) {
        closeAddModal();
    }
    if (event.target == editModal) {
        closeEditModal();
    }
}

// Fermer avec la touche Échap
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});
</script>
</body>
</html>