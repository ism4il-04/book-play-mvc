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
    <title>Créer un Tournoi - <?php echo APP_NAME; ?></title>
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
                <h1>Créer un Tournoi</h1>
                <p class="subtitle">Remplissez les informations du tournoi</p>
            </div>
        </header>

        <div class="dashboard-container">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" style="padding: 12px; margin-bottom: 16px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px;">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="content-section">
                <form action="<?php echo $baseUrl; ?>tournoi/create" method="POST" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); max-width: 800px;">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="nom_tournoi" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                            Nom du Tournoi <span style="color: #dc3545;">*</span>
                        </label>
                        <input type="text" class="form-control" id="nom_tournoi" name="nom_tournoi" 
                               value="<?php echo htmlspecialchars($_SESSION['form_data']['nom_tournoi'] ?? ''); unset($_SESSION['form_data']); ?>"
                               required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="slogan" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                            Slogan
                        </label>
                        <input type="text" class="form-control" id="slogan" name="slogan" 
                               value="<?php echo htmlspecialchars($_SESSION['form_data']['slogan'] ?? ''); ?>"
                               style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    </div>

                    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                        <div style="flex: 1;">
                            <label for="date_debut" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                                Date de début <span style="color: #dc3545;">*</span>
                            </label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                   value="<?php echo htmlspecialchars($_SESSION['form_data']['date_debut'] ?? ''); ?>"
                                   required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                        </div>
                        <div style="flex: 1;">
                            <label for="date_fin" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                                Date de fin <span style="color: #dc3545;">*</span>
                            </label>
                            <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                   value="<?php echo htmlspecialchars($_SESSION['form_data']['date_fin'] ?? ''); ?>"
                                   required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="nb_equipes" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                            Nombre d'équipes <span style="color: #dc3545;">*</span>
                        </label>
                        <input type="number" class="form-control" id="nb_equipes" name="nb_equipes" 
                               value="<?php echo htmlspecialchars($_SESSION['form_data']['nb_equipes'] ?? ''); ?>"
                               min="2" required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <h3 style="font-size: 18px; color: #2c3e50; margin-bottom: 16px;">Prix (optionnel)</h3>
                        <div style="display: flex; gap: 20px;">
                            <div style="flex: 1;">
                                <label for="prixPremiere" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                                    1er Prix
                                </label>
                                <input type="text" class="form-control" id="prixPremiere" name="prixPremiere" 
                                       value="<?php echo htmlspecialchars($_SESSION['form_data']['prixPremiere'] ?? ''); ?>"
                                       placeholder="Ex: 5000 DH" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                            </div>
                            <div style="flex: 1;">
                                <label for="prixDeuxieme" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                                    2ème Prix
                                </label>
                                <input type="text" class="form-control" id="prixDeuxieme" name="prixDeuxieme" 
                                       value="<?php echo htmlspecialchars($_SESSION['form_data']['prixDeuxieme'] ?? ''); ?>"
                                       placeholder="Ex: 3000 DH" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                            </div>
                            <div style="flex: 1;">
                                <label for="prixTroisieme" style="display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50;">
                                    3ème Prix
                                </label>
                                <input type="text" class="form-control" id="prixTroisieme" name="prixTroisieme" 
                                       value="<?php echo htmlspecialchars($_SESSION['form_data']['prixTroisieme'] ?? ''); ?>"
                                       placeholder="Ex: 1000 DH" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 30px;">
                        <a href="<?php echo $baseUrl; ?>tournoi" class="btn btn-secondary" style="padding: 10px 20px; border-radius: 6px; text-decoration: none; background-color: #6c757d; color: white;">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 6px; border: none; background-color: #667eea; color: white; cursor: pointer;">
                            <i class="fas fa-check"></i> Créer le Tournoi
                        </button>
                    </div>
                </form>
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


