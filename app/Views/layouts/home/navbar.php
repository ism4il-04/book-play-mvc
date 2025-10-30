<?php
require_once __DIR__ . '/../../../../config/config.php';
$baseUrl = BASE_URL;
?>
<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <!-- Brand -->
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= $baseUrl ?>">
            <div class="brand-icon">
                <img src="<?= $baseUrl ?>images/téléchargement.jpeg" alt="Book&Play Logo">
            </div>
            <span class="brand-text">Book<span>&</span>Play</span>
        </a>

        <!-- Center Nav Links -->
        <ul class="navbar-nav mx-auto d-none d-lg-flex gap-3">
            <li class="nav-item">
                <a class="nav-link fw-semibold" href="<?= $baseUrl ?>home/terrains">Terrains</a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-semibold" href="<?= $baseUrl ?>home/tournois">Tournois</a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-semibold" href="<?= $baseUrl ?>home/gestionnaire">Gestionnaire</a>
            </li>
        </ul>

        <!-- Right Section -->
        <div class="d-flex align-items-center gap-3">
            <!-- Logged-in user -->
            <?php if (isset($_SESSION['user_name'])): ?>
                <div class="dropdown">
                    <a class="nav-link d-flex align-items-center gap-2 profile-link" href="#" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center">
                            <?php echo strtoupper(substr($_SESSION['user_name'],0,1)); ?>
                        </div>
                        <span class="d-none d-md-inline profile-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <i class="bi bi-chevron-down ms-1 d-none d-md-inline"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="<?= $baseUrl ?>profile"><i class="bi bi-person me-2"></i>Profil</a></li>
                        <li><a class="dropdown-item" href="<?= $baseUrl ?>settings"><i class="bi bi-gear me-2"></i>Paramètres</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= $baseUrl ?>logout"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <!-- Not logged in -->
                <a href="<?= $baseUrl ?>auth/login" class="btn btn-outline-light px-3">Connexion</a>
                <a href="<?= $baseUrl ?>auth/register" class="btn px-3" style="background-color: #b9ff00; color: #1b1b1b; font-weight: 600;">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
    .navbar {
        background: #064420 !important;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 0.75rem 0;
    }
    .navbar-brand, .navbar-brand span, .nav-link {
        color: white !important;
    }
    .navbar-brand .brand-text {
        color: #b9ff00 !important;
    }
    .navbar-brand {
        font-weight: 700;
        letter-spacing: -0.5px;
    }
    .brand-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        height: auto;
    }
    .brand-icon img {
        max-height: 40px;
        width: auto;
        object-fit: contain;
    }
    .nav-link {
        transition: all 0.3s ease;
        color: white !important;
    }
    .nav-link:hover {
        color: #b9ff00 !important;
    }
    .avatar {
        width: 36px;
        height: 36px;
        font-weight: 600;
        line-height: 36px;
        text-transform: uppercase;
        background: linear-gradient(135deg, #b9ff00, #aaff00) !important;
    }
</style>
