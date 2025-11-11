<?php
/**
 * Common sidebar navigation for gestionnaire pages
 * @param string $activeItem The active navigation item (dashboard, terrains, factures, reservations, tournois)
 * @param string $userName The display name of the current user
 * @param string $baseUrl The base URL for the application
 */

// Default values
$activeItem = $activeItem ?? 'dashboard';
$userName = $userName ?? 'Gestionnaire';
$baseUrl = $baseUrl ?? BASE_URL;
?>

<!-- Sidebar Navigation -->
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="<?php echo $baseUrl; ?>images/logo.png" alt="Logo" class="logo">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <a href="<?php echo $baseUrl; ?>dashboard/gestionnaire" class="nav-item <?php echo $activeItem === 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="<?php echo $baseUrl; ?>terrain/gestionnaireTerrains" class="nav-item <?php echo $activeItem === 'terrains' ? 'active' : ''; ?>">
            <i class="fas fa-map-marked-alt"></i>
            <span>Gestion des Terrains</span>
        </a>
        <a href="<?php echo $baseUrl; ?>facture" class="nav-item <?php echo $activeItem === 'factures' ? 'active' : ''; ?>">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Gestion des Factures</span>
        </a>
        <a href="<?php echo $baseUrl; ?>reservations" class="nav-item <?php echo $activeItem === 'reservations' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i>
            <span>Demandes de Réservation</span>
        </a>
        <a href="<?php echo $baseUrl; ?>tournois" class="nav-item <?php echo $activeItem === 'tournois' ? 'active' : ''; ?>">
            <i class="fas fa-trophy"></i>
            <span>Gestion des Tournois</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo strtoupper(substr($userName, 0, 1)); ?>
            </div>
            <div class="user-details">
                <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                <span class="user-role">Gestionnaire</span>
            </div>
        </div>
    </div>
</aside>
