<?php
/**
 * Common top navbar for gestionnaire pages
 * @param string $title The page title
 * @param string $subtitle The page subtitle
 * @param string $baseUrl The base URL for the application
 */

// Default values
$title = $title ?? 'Dashboard';
$subtitle = $subtitle ?? 'Tableau de bord';
$baseUrl = $baseUrl ?? BASE_URL;
$userName = $userName ?? 'Gestionnaire';
?>

<!-- Top Navbar -->
<header class="top-navbar">
    <div class="navbar-left">
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <p class="subtitle"><?php echo htmlspecialchars($subtitle); ?></p>
    </div>
    <div class="navbar-right">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Rechercher...">
        </div>
        <div class="notifications">
            <button class="notification-btn">
                <i class="fas fa-bell"></i>
                <span class="badge">0</span>
            </button>
        </div>
        <div class="user-menu">
            <button class="user-menu-btn">
                <div class="user-avatar-small">
                    <?php echo strtoupper(substr($userName, 0, 1)); ?>
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
