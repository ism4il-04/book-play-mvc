<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;

$stats = [
    'totalReservations' => $totalReservations ?? 0,
    'upcomingReservations' => $upcomingReservationsCount ?? 0,
    'hoursBooked' => $hoursBooked ?? 0,
    'amountSpent' => $amountSpent ?? 0,
    'notifications_count' => 3 // Example notification count
];

$recentReservations = $recentReservations ?? [];
$upcoming = $upcoming ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #2ecc71;
            --info: #1abc9c;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gray: #95a5a6;
            --white: #ffffff;
            --card-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        
        body {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }
        
        /* Navbar */
        .navbar {
            background: #064420 !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0.8rem 2rem;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary) !important;
            font-size: 1.5rem;
        }
        
        .navbar-brand span {
            color: var(--success);
        }
        
        .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: #ffeb3b !important;
            opacity: 0.9;
        }
        
        /* Main Content */
        .main-content {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Welcome Section */
        .welcome-card {
            background: linear-gradient(135deg, #6f9566 0%, #387321 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-card h1 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .welcome-card p {
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }
        
        .welcome-card .btn {
            background: var(--white);
            color: var(--primary);
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s;
        }
        
        .welcome-card .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--white);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid var(--success);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0.5rem 0;
            color: var(--dark);
        }
        
        .stat-card p {
            color: var(--gray);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .stat-card i {
            font-size: 2rem;
            color: var(--success);
            margin-bottom: 1rem;
        }
        
        /* Recent Activities */
        .activities-card {
            background: var(--white);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }
        
        .activities-card h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(46, 204, 113, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--success);
            font-size: 1.2rem;
        }
        
        .activity-details h4 {
            font-weight: 600;
            margin: 0 0 0.2rem 0;
            font-size: 1rem;
        }
        
        .activity-details p {
            color: var(--gray);
            font-size: 0.85rem;
            margin: 0;
        }
        
        .activity-time {
            margin-left: auto;
            color: var(--gray);
            font-size: 0.85rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .welcome-card {
                text-align: center;
                padding: 1.5rem 1rem;
            }
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

        .brand-icon img {
            max-height: 70px;
            height: 70px;
            width: auto;
            object-fit: contain;
            display: block;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-green">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $baseUrl; ?>">
                <img src="<?php echo $baseUrl; ?>images/logo.png" alt="Logo" height="40" class="d-inline-block align-text-top me-2">
                <span class="brand-text">Book<span>&</span>Play</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $baseUrl; ?>dashboard">
                            <i class="fas fa-home me-1"></i> Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>home/terrains">
                            <i class="far fa-calendar-alt me-1"></i> Mes Réservations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>home/tournois">
                            <i class="fas fa-trophy me-1"></i> Tournois
                        </a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>compte">
                            <i class="far fa-user me-1"></i> Mon Compte
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <?php echo strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)); ?>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>profil"><i class="fas fa-user-circle me-2"></i>Mon Profil</a></li>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>parametres"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo $baseUrl; ?>logout"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Section -->
        <div class="welcome-card">
            <h1>Bonjour, <?php echo htmlspecialchars($currentUser['name'] ?? 'Utilisateur'); ?> </h1>
            <p>Bienvenue sur votre tableau de bord. Gérez facilement vos réservations et restez informé de vos activités.</p>
            <a href="<?php echo $baseUrl; ?>home/terrains" class="btn">
                <i class="fas fa-plus me-2"></i>Nouvelle réservation
            </a>
        </div>

        <!-- Stats Overview -->
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-calendar-check"></i>
                <h3><?php echo (int)$stats['totalReservations']; ?></h3>
                <p>Réservations totales</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3><?php echo (int)$stats['upcomingReservations']; ?></h3>
                <p>Réservations à venir</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-hourglass-half"></i>
                <h3><?php echo (int)$stats['hoursBooked']; ?>h</h3>
                <p>Heures réservées</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-wallet"></i>
                <h3><?php echo number_format((float)$stats['amountSpent'], 0, ',', ' '); ?> <small>MAD</small></h3>
                <p>Total dépensé</p>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="activities-card">
            <h2>Activités récentes</h2>
            
            <?php if (!empty($recentReservations)): ?>
                <?php foreach (array_slice($recentReservations, 0, 5) as $r): ?>
                    <?php 
                    $status = strtolower($r['status'] ?? 'en attente');
                    $icon = 'calendar-check';
                    $statusText = 'En attente';
                    $statusClass = 'text-warning';
                    
                    if (strpos($status, 'confirm') !== false) {
                        $icon = 'check-circle';
                        $statusText = 'Confirmée';
                        $statusClass = 'text-success';
                    } elseif (strpos($status, 'annul') !== false) {
                        $icon = 'times-circle';
                        $statusClass = 'text-danger';
                        $statusText = 'Annulée';
                    } elseif (strpos($status, 'en cours') !== false) {
                        $icon = 'play-circle';
                        $statusClass = 'text-info';
                        $statusText = 'En cours';
                    }
                    ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-<?php echo $icon; ?>"></i>
                        </div>
                        <div class="activity-details">
                            <h4>Réservation #<?php echo $r['id'] ?? ''; ?></h4>
                            <p>Terrain: <?php echo htmlspecialchars($r['terrain'] ?? 'Non spécifié'); ?></p>
                        </div>
                        <div class="activity-time <?php echo $statusClass; ?>">
                            <i class="far fa-clock me-1"></i>
                            <?php echo $statusText; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="far fa-calendar-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune activité récente</p>
                    <a href="<?php echo $baseUrl; ?>reserver" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Réserver maintenant
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Upcoming Tournaments -->
        <div class="activities-card">
            <h2>Prochains tournois</h2>
            
            <?php if (!empty($upcoming)): ?>
                <?php foreach (array_slice($upcoming, 0, 3) as $tournament): ?>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: rgba(52, 152, 219, 0.1); color: #3498db;">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="activity-details">
                            <h4><?php echo htmlspecialchars($tournament['title'] ?? 'Tournoi'); ?></h4>
                            <p>
                                <i class="far fa-calendar-alt me-1"></i> 
                                <?php echo htmlspecialchars($tournament['date'] ?? 'Date non précisée'); ?>
                                <?php if (!empty($tournament['location'])): ?>
                                    <span class="ms-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($tournament['location']); ?>
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <a href="<?php echo $baseUrl; ?>tournoi/<?php echo $tournament['id'] ?? ''; ?>" class="btn btn-sm btn-outline-primary">
                            Détails
                        </a>
                    </div>
                <?php endforeach; ?>
                
                <?php if (count($upcoming) > 3): ?>
                    <div class="text-center mt-3">
                        <a href="<?php echo $baseUrl; ?>tournois" class="btn btn-link">
                            Voir tous les tournois <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun tournoi prévu pour le moment</p>
                    <a href="<?php echo $baseUrl; ?>tournois" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-plus me-2"></i>Voir le calendrier
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Support Section -->
        <div class="activities-card mt-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2">Besoin d'aide ?</h3>
                    <p class="text-muted mb-3 mb-md-0">Notre équipe de support est disponible 24/7 pour répondre à vos questions.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="<?php echo $baseUrl; ?>contact" class="btn btn-primary">
                        <i class="fas fa-headset me-2"></i>Contacter le support
                    </a>
                </div>
            </div>
        </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Activate tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Active nav link based on current URL
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath || 
                    (link.getAttribute('href') !== '#' && currentPath.includes(link.getAttribute('href')))) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>

