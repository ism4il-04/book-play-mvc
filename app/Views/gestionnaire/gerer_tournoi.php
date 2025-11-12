<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer le Tournoi - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/navbar_gestionnaire.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/footer_gestionnaire.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/dashboard_gest.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
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
            <a href="<?php echo $baseUrl; ?>terrain/gestionnaireTerrains" class="nav-item">
                <i class="fas fa-map-marked-alt"></i>
                <span>Gestion des Terrains</span>
            </a>
            <a href="<?php echo $baseUrl; ?>facture" class="nav-item">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Gestion des Factures</span>
            </a>
            <a href="<?php echo $baseUrl; ?>reservations" class="nav-item">
                <i class="fas fa-calendar-check"></i>
                <span>Demandes de Réservation</span>
            </a>
            <a href="<?php echo $baseUrl; ?>tournoi" class="nav-item active">
                <i class="fas fa-trophy"></i>
                <span>Gestion des Tournois</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['name'] ?? '', 0, 1)); ?>
                </div>
                <div class="user-details">
                    <span class="user-name"><?php echo htmlspecialchars($currentUser['name'] ?? ''); ?></span>
                    <span class="user-role">Gestionnaire</span>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <div class="navbar-left">
                <h1><?php echo htmlspecialchars($tournoi['nom_tournoi']); ?></h1>
                <p class="subtitle">Gérez les matchs de ce tournoi</p>
            </div>
        </header>

        <div class="dashboard-container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" style="padding: 12px; margin-bottom: 16px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px;">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" style="padding: 12px; margin-bottom: 16px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px;">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Tournament Info -->
            <div class="content-section" style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h2 style="margin-top: 0; color: #2c3e50;">Informations du Tournoi</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div>
                        <span style="color: #95a5a6; font-size: 14px;">Date de début:</span>
                        <div style="font-weight: 500; margin-top: 4px;"><?php echo htmlspecialchars($tournoi['date_debut']); ?></div>
                    </div>
                    <div>
                        <span style="color: #95a5a6; font-size: 14px;">Date de fin:</span>
                        <div style="font-weight: 500; margin-top: 4px;"><?php echo htmlspecialchars($tournoi['date_fin']); ?></div>
                    </div>
                    <div>
                        <span style="color: #95a5a6; font-size: 14px;">Nombre d'équipes:</span>
                        <div style="font-weight: 500; margin-top: 4px;"><?php echo htmlspecialchars($tournoi['nb_equipes']); ?></div>
                    </div>
                    <div>
                        <span style="color: #95a5a6; font-size: 14px;">Nombre de matchs:</span>
                        <div style="font-weight: 500; margin-top: 4px;"><?php echo count($reservations); ?></div>
                    </div>
                </div>
            </div>

            <!-- Add Match Form -->
            <div class="content-section" style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h2 style="margin-top: 0; color: #2c3e50;">Ajouter un Match</h2>
                <form action="<?php echo $baseUrl; ?>tournoi/addMatch/<?php echo (int)$tournoi['id_tournoi']; ?>" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
                    <div>
                        <label for="id_terrain" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                            Terrain <span style="color: #dc3545;">*</span>
                        </label>
                        <select name="id_terrain" id="id_terrain" required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                            <option value="">Sélectionner un terrain</option>
                            <?php foreach ($terrains as $terrain): ?>
                                <option value="<?php echo (int)$terrain['id_terrain']; ?>">
                                    <?php echo htmlspecialchars($terrain['nom_terrain']); ?> - <?php echo htmlspecialchars($terrain['localisation']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="date_reservation" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                            Date <span style="color: #dc3545;">*</span>
                        </label>
                        <input type="date" name="date_reservation" id="date_reservation" required 
                               min="<?php echo $tournoi['date_debut']; ?>" 
                               max="<?php echo $tournoi['date_fin']; ?>"
                               style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="creneau" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                            Créneau <span style="color: #dc3545;">*</span>
                        </label>
                        <input type="time" name="creneau" id="creneau" required 
                               style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="commentaire" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                            Commentaire
                        </label>
                        <input type="text" name="commentaire" id="commentaire" 
                               placeholder="Ex: Match de qualification"
                               style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px 20px; border-radius: 6px; border: none; background-color: #667eea; color: white; cursor: pointer;">
                            <i class="fas fa-plus"></i> Ajouter le Match
                        </button>
                    </div>
                </form>
            </div>

            <!-- Matches List -->
            <div class="content-section">
                <h2 style="margin-bottom: 20px; color: #2c3e50;">Matchs du Tournoi</h2>
                <?php if (empty($reservations)): ?>
                    <div class="no-data" style="text-align:center; padding:40px; background: white; border-radius: 12px;">
                        <i class="fas fa-calendar-times" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                        <p>Aucun match ajouté pour ce tournoi</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive" style="background:#fff; border-radius:12px; box-shadow:0 3px 10px rgba(0,0,0,0.06); overflow:hidden;">
                        <table class="table" style="width:100%; border-collapse:collapse;">
                            <thead style="background:#f7f9fc;">
                                <tr>
                                    <th style="padding:12px; text-align:left;">Terrain</th>
                                    <th style="padding:12px; text-align:left;">Date</th>
                                    <th style="padding:12px; text-align:left;">Créneau</th>
                                    <th style="padding:12px; text-align:left;">Commentaire</th>
                                    <th style="padding:12px; text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                    <tr style="border-top:1px solid #eef2f7;">
                                        <td style="padding:12px;">
                                            <?php echo htmlspecialchars($reservation['nom_terrain']); ?>
                                            <br><small style="color: #95a5a6;"><?php echo htmlspecialchars($reservation['localisation']); ?></small>
                                        </td>
                                        <td style="padding:12px;">
                                            <?php echo htmlspecialchars($reservation['date_reservation']); ?>
                                        </td>
                                        <td style="padding:12px;">
                                            <?php echo htmlspecialchars(substr($reservation['creneau'], 0, 5)); ?>
                                        </td>
                                        <td style="padding:12px;">
                                            <?php echo htmlspecialchars($reservation['commentaire'] ?: '-'); ?>
                                        </td>
                                        <td style="padding:12px; text-align:right;">
                                            <a href="<?php echo $baseUrl; ?>tournoi/deleteMatch/<?php echo (int)$tournoi['id_tournoi']; ?>/<?php echo (int)$reservation['id_reservation']; ?>" 
                                               class="btn btn-danger" 
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce match?');"
                                               style="padding: 6px 12px; border-radius: 6px; text-decoration: none; background-color: #e74c3c; color: white; font-size: 12px;">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <footer class="main-footer">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tous droits réservés.</p>
            </div>
        </footer>
    </main>

    <script src="<?php echo $baseUrl; ?>js/dashboard_gest.js"></script>
</body>
</html>


