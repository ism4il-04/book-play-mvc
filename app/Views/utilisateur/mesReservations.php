<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;

// Les variables $reservations est passée par le contrôleur
$reservations = $reservations ?? [];

// Exemple de données (à remplacer par les vraies données du contrôleur)
/*
$reservations = [
    [
        'id' => 1,
        'terrain_nom' => 'Terrain Al Massira',
        'localisation' => 'Avenue Mohammed V, Agadir 80000, Maroc',
        'format_terrain' => 'Grand terrain',
        'type_terrain' => 'Gazon naturel',
        'date_reservation' => '2025-11-15',
        'heure_debut' => '14:00',
        'heure_fin' => '16:00',
        'prix_total' => '400',
        'statut' => 'Acceptée',
        'image' => 'terrain1.jpg',
        'email' => 'almassira@bookplay.ma'
    ]
];
*/
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-green: #064420;
            --dark-teal: #1a4d3e;
            --accent-cyan: #00bcd4;
            --accent-lime: #cddc39;
            --lime-green: #b9ff00;
            --card-shadow: 0 2px 8px rgba(0,0,0,0.1);
            --status-accepted: #4caf50;
            --status-pending: #ff9800;
            --status-refused: #f44336;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: #e8f0ed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Navbar */
        .navbar {
            background: var(--primary-green) !important;
            padding: 0.8rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        
        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-brand img {
            height: 45px;
            width: auto;
        }
        
        .brand-text {
            color: var(--lime-green) !important;
        }

        .navbar-nav.center-nav {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 0.5rem 1.2rem !important;
            transition: all 0.3s;
            border-radius: 5px;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .nav-link.active {
            background: var(--accent-cyan);
            color: white !important;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            color: var(--primary-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff5252;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
        }
        
        /* Main Content */
        .main-content {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Search Section */
        .search-section {
            background: white;
            padding: 1.2rem 1.5rem;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }
        
        .search-input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="%23999" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>') no-repeat 1rem center;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.1);
        }
        
        /* Reservations Grid */
        .reservations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        /* Reservation Card */
        .reservation-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }
        
        .reservation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        /* Status Badge on Image */
        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: capitalize;
            z-index: 10;
        }
        
        .status-accepted {
            background: var(--status-accepted);
            color: white;
        }
        
        .status-pending {
            background: var(--status-pending);
            color: white;
        }
        
        .status-refused {
            background: var(--status-refused);
            color: white;
        }
        
        .reservation-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #2e7d5e 0%, #1a4d3e 100%);
        }
        
        .reservation-content {
            padding: 1.5rem;
        }
        
        .reservation-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.8rem;
        }
        
        .reservation-info {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            margin-bottom: 0.8rem;
            font-size: 0.85rem;
            color: #7f8c8d;
        }
        
        .reservation-info i {
            color: var(--accent-cyan);
            margin-top: 2px;
            min-width: 16px;
        }
        
        .reservation-badges {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        
        .badge-size {
            background: #e8f0ed;
            color: var(--primary-green);
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-type {
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .reservation-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #ecf0f1;
        }
        
        .reservation-price {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary-green);
        }
        
        .reservation-price .label {
            font-size: 0.75rem;
            font-weight: 400;
            color: #7f8c8d;
            display: block;
            margin-bottom: 2px;
        }
        
        .reservation-price .amount {
            font-size: 1.1rem;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-details {
            background: var(--dark-teal);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        .btn-details:hover {
            background: #0f3d2f;
            color: white;
        }
        
        .btn-modify {
            background: var(--lime-green);
            color: var(--primary-green);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        .btn-modify:hover {
            background: #a5d600;
            color: var(--primary-green);
        }
        
        .btn-pay {
            background: var(--accent-cyan);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            width: 100%;
            justify-content: center;
            margin-top: 0.8rem;
        }
        
        .btn-pay:hover {
            background: #00a5bb;
            color: white;
        }
        
        /* Empty State */
        .empty-state {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: var(--card-shadow);
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #bdc3c7;
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            color: #7f8c8d;
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: #95a5a6;
        }
        
        /* Dropdown Menu */
        .dropdown-menu {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            padding: 0.5rem 0;
        }
        
        .dropdown-item {
            padding: 0.7rem 1.5rem;
            transition: all 0.3s;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 0.5rem;
        }
        
        /* Styles pour le modal de détails */
        #detailsModalBody {
            transition: opacity 0.3s ease;
        }
        
        #detailsModalBody.loading {
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .reservation-details .card {
            transition: all 0.3s ease;
        }
        
        .comment-content {
            transition: background-color 0.3s ease;
        }
        
        .comment-content:hover {
            background-color: #f0f0f0;
        }
        
        /* Styles pour le modal d'édition */
        #editModalBody {
            transition: opacity 0.3s ease;
        }
        
        #editModalBody.loading {
            min-height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        /* Styles communs pour les modals */
        .modal-content {
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .modal-header .btn-close:focus {
            box-shadow: none;
        }
        
        /* Styles des créneaux */
        #creneauxList {
            background-color: #e7f5ff;
            border-radius: 8px;
            padding: 12px;
        }
        .creneau-item {
            background: #ffffff;
            border: 1px solid #cfe2ff;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: background-color .2s, border-color .2s;
        }
        .creneau-item:hover { background: #f8fbff; }
        .creneau-item.selected {
            background: #d1e7ff;
            border-color: #0d6efd;
        }
        .creneau-item.disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .navbar-nav.center-nav {
                position: static;
                transform: none;
            }
            
            .reservations-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }
            
            .search-section {
                padding: 1rem;
            }
            
            .reservations-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-details,
            .btn-modify {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $baseUrl; ?>">
                <img src="<?php echo $baseUrl; ?>images/logo.png" alt="Logo">
                <span>Book<span class="brand-text">&</span><span class="brand-text">Play</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="background: white;">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menu centré -->
                <ul class="navbar-nav center-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>utilisateur/dashboard">
                            Terrains
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $baseUrl; ?>utilisateur/mesReservations">
                            Mes Réservations
                        </a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>home/tournois">
                            <i class="fas fa-trophy me-1"></i> Tournois
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>tournoi/create">
                            <i class="fas fa-plus-circle me-1"></i> Demander un tournoi
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link p-0" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Mon profil">
                            <i class="bi bi-person-circle" style="font-size: 1.4rem;"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="profileDropdown" style="min-width: 280px;">
                            <h6 class="mb-3" style="color:#2c3e50; font-weight:700;">Informations personnelles</h6>
                            <div class="d-flex align-items-start mb-2" style="gap:.6rem; color:#7f8c8d;">
                                <i class="bi bi-person" style="color:#00bcd4;"></i>
                                <div>
                                    <div style="font-size:.8rem; opacity:.8;">Nom complet</div>
                                    <div style="font-weight:600; color:#2c3e50;">
                                        <?php 
                                        $prenom = $currentUser['prenom'] ?? '';
                                        $nom = $currentUser['nom'] ?? '';
                                        $name = trim($prenom . ' ' . $nom);
                                        if ($name === '') { $name = $currentUser['name'] ?? 'Utilisateur'; }
                                        echo htmlspecialchars($name);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-2" style="gap:.6rem; color:#7f8c8d;">
                                <i class="bi bi-envelope" style="color:#00bcd4;"></i>
                                <div>
                                    <div style="font-size:.8rem; opacity:.8;">Email</div>
                                    <div style="font-weight:600; color:#2c3e50;">
                                        <?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3" style="gap:.6rem; color:#7f8c8d;">
                                <i class="bi bi-telephone" style="color:#00bcd4;"></i>
                                <div>
                                    <div style="font-size:.8rem; opacity:.8;">Téléphone</div>
                                    <div style="font-weight:600; color:#2c3e50;">
                                        <?php 
                                        $tel = $currentUser['telephone'] ?? ($currentUser['num_tel'] ?? '');
                                        echo htmlspecialchars($tel);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a class="btn btn-outline-secondary btn-sm" href="<?php echo $baseUrl; ?>utilisateur/changePassword">Changer mot de passe</a>
                                <a class="btn btn-primary btn-sm" style="background:#b9ff00; color:#064420; border-color:#b9ff00;" href="<?php echo $baseUrl; ?>utilisateur/profil">Modifier mes informations</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>logout" title="Déconnexion">
                            <i class="bi bi-box-arrow-right" style="font-size: 1.2rem;"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Search Section -->
        <div class="search-section">
            <input type="search" class="search-input" placeholder="Rechercher une réservation...">
        </div>

        <!-- Reservations Grid -->
        <?php if (!empty($reservations)): ?>
            <div class="reservations-grid">
                <?php foreach ($reservations as $reservation): ?>
                    <?php
                    // Gestion de l'image
                    $imageFile = isset($reservation['image']) ? trim($reservation['image']) : '';
                    $safeFile = '' !== $imageFile ? basename($imageFile) : '';
                    $rootDir = realpath(__DIR__ . '/../../../');
                    $absolutePath = $rootDir . '/public/images/' . $safeFile;
                    $imageExists = ('' !== $safeFile) && file_exists($absolutePath);
                    $imageUrl = $imageExists ? ($baseUrl . 'images/' . rawurlencode($safeFile)) : ($baseUrl . 'images/terrain.png');
                    
                    // Statut
                    $statut = strtolower($reservation['statut'] ?? 'pending');
                    $statutClass = 'status-pending';
                    $statutLabel = 'En attente';
                    
                    if ($statut === 'acceptée' || $statut === 'accepted' || $statut === 'accepte') {
                        $statutClass = 'status-accepted';
                        $statutLabel = 'Acceptée';
                    } elseif ($statut === 'refusée' || $statut === 'refused' || $statut === 'refuse') {
                        $statutClass = 'status-refused';
                        $statutLabel = 'Refusée';
                    }
                    ?>
                    
                    <div class="reservation-card">
                        <div style="position: relative;">
                            <span class="status-badge <?php echo $statutClass; ?>"><?php echo $statutLabel; ?></span>
                            <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                                 alt="<?php echo htmlspecialchars($reservation['terrain_nom'] ?? 'Terrain'); ?>" 
                                 class="reservation-image">
                        </div>
                        
                        <div class="reservation-content">
                            <h3 class="reservation-title">
                                <?php echo htmlspecialchars($reservation['terrain_nom'] ?? 'Terrain inconnu'); ?>
                            </h3>
                            
                            <div class="reservation-info">
                                <i class="bi bi-geo-alt"></i>
                                <span><?php echo htmlspecialchars($reservation['localisation'] ?? 'Localisation inconnue'); ?></span>
                            </div>
                            
                            <div class="reservation-badges">
                                <span class="badge-size">
                                    Taille: <?php echo htmlspecialchars($reservation['format_terrain'] ?? '-'); ?>
                                </span>
                                <span class="badge-type">
                                    Type: <?php echo htmlspecialchars($reservation['type_terrain'] ?? '-'); ?>
                                </span>
                            </div>
                            
                            <div class="reservation-info">
                                <i class="bi bi-envelope"></i>
                                <span><?php echo htmlspecialchars($reservation['email'] ?? 'contact@bookplay.ma'); ?></span>
                            </div>
                            
                            <div class="reservation-info">
                                <i class="bi bi-calendar-check"></i>
                                <span>
                                    <?php 
                                    $date = $reservation['date_reservation'] ?? '';
                                    $heureDebut = $reservation['heure_debut'] ?? '';
                                    $heureFin = $reservation['heure_fin'] ?? '';
                                    echo htmlspecialchars($date . ' • ' . $heureDebut . '-' . $heureFin); 
                                    ?>
                                </span>
                            </div>
                            
                            <div class="action-buttons">
                                <a href="<?php echo $baseUrl; ?>reservation/details?id=<?php echo $reservation['id']; ?>" class="btn-details">
                                    <i class="bi bi-info-circle"></i> Détails
                                </a>
                                <a href="<?php echo $baseUrl; ?>reservation/edit?id=<?php echo $reservation['id']; ?>" class="btn-modify">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                            </div>
                            
                            <a href="<?php echo $baseUrl; ?>payment/process?reservation_id=<?php echo $reservation['id']; ?>" class="btn-pay">
                                <i class="bi bi-credit-card"></i> Voir Facture
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <h3>Aucune réservation</h3>
                <p>Vous n'avez pas encore de réservation. Explorez nos terrains disponibles !</p>
                <a href="<?php echo $baseUrl; ?>utilisateur/dashboard" class="btn-modify" style="margin-top: 1.5rem; display: inline-flex;">
                    <i class="bi bi-search"></i> Voir les terrains
                </a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Modal Détails Réservation -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">
                        <i class="bi bi-info-circle me-2"></i>Détails de la réservation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailsModalBody">
                    <!-- Contenu chargé dynamiquement -->
                    <div class="text-center p-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="mt-2 text-muted">Chargement des détails...</p>
                    </div>
                </div>
                <!-- Pas de footer, les boutons seront dans le contenu -->
            </div>
        </div>
    </div>

    <!-- Modal Modification Réservation -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header" style="background: white; border-bottom: 1px solid #e0e0e0; padding: 1.5rem;">
                    <h5 class="modal-title" id="editModalLabel" style="color: #064420; font-weight: 700;">
                        Modification de la réservation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="editModalBody" style="padding: 2rem; background: #f8f9fa;">
                    <!-- Contenu chargé dynamiquement -->
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Recherche en temps réel
        document.querySelector('.search-input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.reservation-card');
            
            cards.forEach(card => {
                const title = card.querySelector('.reservation-title').textContent.toLowerCase();
                const location = card.querySelector('.reservation-info span').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || location.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
        
        // Gestion des modals
        document.addEventListener('DOMContentLoaded', function() {
            // Attacher l'événement aux boutons de détails
            document.querySelectorAll('.btn-details').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const reservationId = this.href.split('id=')[1];
                    showReservationDetails(reservationId);
                });
            });
            
            // Attacher l'événement aux boutons de modification
            document.querySelectorAll('.btn-modify').forEach(btn => {
                if (btn.href && btn.href.includes('reservation/edit')) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const reservationId = this.href.split('id=')[1];
                        showReservationEditForm(reservationId);
                    });
                }
            });
            
            // Gestion des boutons de modification dans le modal de détails
            document.addEventListener('click', function(e) {
                if (e.target && e.target.matches('#detailsModalBody .btn-primary') && e.target.href && e.target.href.includes('reservation/edit')) {
                    e.preventDefault();
                    const reservationId = e.target.href.split('id=')[1];
                    // Fermer le modal de détails
                    bootstrap.Modal.getInstance(document.getElementById('detailsModal')).hide();
                    // Ouvrir le modal de modification
                    showReservationEditForm(reservationId);
                }
            });
        });
        
        function showReservationDetails(reservationId) {
            // Réinitialiser le contenu du modal avec l'indicateur de chargement (déjà présent dans le HTML)
            const detailsModalBody = document.getElementById('detailsModalBody');
            
            // Afficher le modal avec animation fluide
            const detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
            detailsModal.show();
            
            // Ajouter une classe pour l'animation de chargement
            detailsModalBody.classList.add('loading');
            
            // Charger les détails via AJAX avec un léger délai pour une meilleure UX
            setTimeout(() => {
                fetch(`<?php echo $baseUrl; ?>reservation/details?id=${reservationId}&format=json`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur réseau');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Transition fluide pour le contenu
                            detailsModalBody.style.opacity = '0';
                            setTimeout(() => {
                                detailsModalBody.innerHTML = data.html;
                                detailsModalBody.style.opacity = '1';
                                detailsModalBody.classList.remove('loading');
                            }, 200);
                        } else {
                            throw new Error(data.message || 'Erreur lors du chargement des détails');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        detailsModalBody.innerHTML = `
                            <div class="alert alert-danger mx-3 my-4 d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-3 fs-3"></i>
                                <div>
                                    <h5 class="alert-heading">Erreur de chargement</h5>
                                    <p class="mb-0">${error.message || 'Impossible de charger les détails de la réservation'}</p>
                                </div>
                            </div>
                        `;
                        detailsModalBody.classList.remove('loading');
                    });
            }, 300); // Délai pour une meilleure expérience utilisateur
        }
        
        function showReservationEditForm(reservationId) {
            // Préparer le modal avec un indicateur de chargement amélioré
            const editModalBody = document.getElementById('editModalBody');
            editModalBody.innerHTML = `
                <div class="text-center p-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-2 text-muted">Préparation du formulaire de modification...</p>
                </div>
            `;
            
            // Ajouter une classe pour l'animation
            editModalBody.classList.add('loading');
            
            // Afficher le modal
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
            
            // Charger le formulaire de modification via AJAX avec un léger délai pour une meilleure UX
            setTimeout(() => {
                fetch(`<?php echo $baseUrl; ?>reservation/edit?id=${reservationId}&format=json`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur réseau');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Transition fluide pour le contenu
                            editModalBody.style.opacity = '0';
                            setTimeout(() => {
                                editModalBody.innerHTML = data.html;
                                editModalBody.style.opacity = '1';
                                editModalBody.classList.remove('loading');
                                
                                // Initialiser les événements du formulaire de modification
                                initEditForm();
                            }, 200);
                        } else {
                            let errorMessage = data.message || 'Erreur lors du chargement du formulaire';
                            if (data.canModify === false) {
                                errorMessage = 'Cette réservation ne peut plus être modifiée (moins de 48h avant le début du match).';
                            }
                            throw new Error(errorMessage);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        editModalBody.innerHTML = `
                            <div class="alert alert-danger mx-3 my-4 d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-3 fs-3"></i>
                                <div>
                                    <h5 class="alert-heading">Impossible de modifier</h5>
                                    <p class="mb-0">${error.message || 'Erreur lors du chargement du formulaire'}</p>
                                </div>
                            </div>
                            <div class="text-center mb-3">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle me-2"></i>Fermer
                                </button>
                            </div>
                        `;
                        editModalBody.classList.remove('loading');
                    });
            }, 300);
        }
        
        function initEditForm() {
            // Récupérer le formulaire de modification
            const editForm = document.getElementById('editReservationForm');
            if (!editForm) return;
            
            // Gérer le changement de date
            const dateInput = document.getElementById('date_reservation');
            if (dateInput) {
                dateInput.addEventListener('change', function() {
                    const terrainId = document.getElementById('terrain_id').value;
                    // Vider la sélection courante
                    const hd = editForm.querySelector('#heure_debut');
                    const hf = editForm.querySelector('#heure_fin');
                    if (hd) hd.value = '';
                    if (hf) hf.value = '';
                    if (terrainId && this.value) {
                        loadCreneaux(terrainId, this.value);
                    }
                });

                // Charger immédiatement les créneaux pour la date actuelle si présente
                const terrainId = document.getElementById('terrain_id')?.value;
                if (terrainId && dateInput.value) {
                    loadCreneaux(terrainId, dateInput.value);
                }
            }
            
            // Gérer la soumission du formulaire
            editForm.addEventListener('submit', function(e) {
                const heureDebutEl = editForm.querySelector('#heure_debut');
                const heureFinEl = editForm.querySelector('#heure_fin');
                const heureDebut = heureDebutEl ? heureDebutEl.value : '';
                const heureFin = heureFinEl ? heureFinEl.value : '';
                
                if (!heureDebut || !heureFin) {
                    e.preventDefault();
                    alert('Veuillez sélectionner un créneau horaire');
                    return false;
                }
            });
        }
        
        function loadCreneaux(terrainId, date) {
            console.log('Chargement créneaux - Terrain:', terrainId, 'Date:', date);
            
            const modalBody = document.getElementById('editModalBody');
            const creneauxList = modalBody ? modalBody.querySelector('#creneauxList') : document.getElementById('creneauxList');
            if (!creneauxList) return;
            
            creneauxList.innerHTML = '<div class="alert alert-info">Chargement des créneaux...</div>';
            
            const url = `<?php echo $baseUrl; ?>terrain/creneaux?id=${terrainId}${date ? '&date=' + date : ''}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Afficher les heures d'ouverture/fermeture
                        if (data.heure_ouverture && data.heure_fermeture) {
                            const heuresInfo = modalBody ? modalBody.querySelector('#heuresInfo') : document.getElementById('heuresInfo');
                            if (heuresInfo) {
                                const openEl = modalBody ? modalBody.querySelector('#heure_ouverture_display') : document.getElementById('heure_ouverture_display');
                                const closeEl = modalBody ? modalBody.querySelector('#heure_fermeture_display') : document.getElementById('heure_fermeture_display');
                                if (openEl) openEl.textContent = data.heure_ouverture;
                                if (closeEl) closeEl.textContent = data.heure_fermeture;
                                heuresInfo.style.display = 'block';
                            }
                        }
                        
                        creneauxList.innerHTML = '';
                        
                        if (data.creneaux && data.creneaux.length > 0) {
                            const heureDebutInput = modalBody ? modalBody.querySelector('#heure_debut') : document.getElementById('heure_debut');
                            const heureFinInput = modalBody ? modalBody.querySelector('#heure_fin') : document.getElementById('heure_fin');
                            const normTime = (t) => (t || '').toString().trim().substring(0,5);
                            const currentStart = normTime(creneauxList.dataset.currentStart || heureDebutInput?.value || '');
                            const currentEnd = normTime(creneauxList.dataset.currentEnd || heureFinInput?.value || '');
                            
                            data.creneaux.forEach(creneau => {
                                const creneauDiv = document.createElement('div');
                                creneauDiv.className = 'creneau-item';
                                // Attributs data pour délégation
                                creneauDiv.dataset.start = creneau.heure_ouverture;
                                creneauDiv.dataset.end = creneau.heure_fermeture;
                                
                                const isCurrent = currentStart && currentEnd && currentStart === creneau.heure_ouverture && currentEnd === creneau.heure_fermeture;
                                if (creneau.disponible == 0 && !isCurrent) {
                                    creneauDiv.classList.add('disabled');
                                }
                                
                                creneauDiv.innerHTML = `
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span style="font-size: 1.05rem;">
                                            <strong>${creneau.heure_ouverture} - ${creneau.heure_fermeture}</strong>
                                        </span>
                                        <span class="badge ${(creneau.disponible == 1 || isCurrent) ? 'bg-success' : 'bg-danger'}">
                                            ${(creneau.disponible == 1) ? 'Disponible' : (isCurrent ? 'Votre créneau' : 'Réservé')}
                                        </span>
                                    </div>
                                `;
                                
                                // Préselectionner si correspond au créneau actuel
                                if (isCurrent) {
                                    creneauDiv.classList.add('selected');
                                    // Si les champs cachés sont vides, les remplir avec le créneau actuel
                                    if (heureDebutInput && !heureDebutInput.value) heureDebutInput.value = creneau.heure_ouverture;
                                    if (heureFinInput && !heureFinInput.value) heureFinInput.value = creneau.heure_fermeture;
                                }
                                
                                creneauxList.appendChild(creneauDiv);
                            });

                            // Délégation d'événements: un seul handler pour tous les items
                            creneauxList.onclick = (e) => {
                                const item = e.target.closest('.creneau-item');
                                if (!item || item.classList.contains('disabled')) return;
                                const data = {
                                    heure_ouverture: item.dataset.start,
                                    heure_fermeture: item.dataset.end
                                };
                                selectCreneau(item, data);
                                // Mémoriser la sélection courante sur le conteneur
                                creneauxList.dataset.currentStart = data.heure_ouverture;
                                creneauxList.dataset.currentEnd = data.heure_fermeture;
                            };
                        } else {
                            creneauxList.innerHTML = '<div class="alert alert-warning">Aucun créneau disponible pour cette date.</div>';
                        }
                    } else {
                        creneauxList.innerHTML = '<div class="alert alert-danger">Erreur : ' + (data.message || 'Impossible de charger les créneaux') + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Erreur chargement créneaux:', error);
                    creneauxList.innerHTML = '<div class="alert alert-danger">Erreur de connexion au serveur.</div>';
                });
        }
        
        function selectCreneau(element, creneauData) {
            // Retirer la sélection des autres créneaux dans ce conteneur uniquement
            const container = element.parentElement || document;
            container.querySelectorAll('.creneau-item').forEach(item => {
                item.classList.remove('selected');
            });
            
            // Sélectionner ce créneau
            element.classList.add('selected');
            
            // Cibler d'abord les champs cachés dans le modal d'édition, sinon fallback
            const heureDebutInput = document.querySelector('#editModalBody #heure_debut') || document.querySelector('#reservationModal #heure_debut') || document.getElementById('heure_debut');
            const heureFinInput = document.querySelector('#editModalBody #heure_fin') || document.querySelector('#reservationModal #heure_fin') || document.getElementById('heure_fin');
            if (heureDebutInput && heureFinInput) {
                heureDebutInput.value = creneauData.heure_ouverture;
                heureFinInput.value = creneauData.heure_fermeture;
            }
            
            // Calculer le prix
            calculateTotalPrice();
        }
        
        function calculateTotalPrice() {
            // Chercher en priorité dans le modal d'édition
            const context = document.getElementById('editModalBody') || document;
            const prixHeureInput = context.querySelector('#prix_heure') || document.getElementById('prix_heure');
            const heureDebutInput = context.querySelector('#heure_debut') || document.getElementById('heure_debut');
            const heureFinInput = context.querySelector('#heure_fin') || document.getElementById('heure_fin');
            const prixHeure = parseFloat((prixHeureInput && prixHeureInput.value) || 0);
            const heureDebut = (heureDebutInput && heureDebutInput.value) || '';
            const heureFin = (heureFinInput && heureFinInput.value) || '';
            
            if (!heureDebut || !heureFin) return;
            
            // Calculer la différence d'heures
            const debut = new Date('2000-01-01 ' + heureDebut);
            const fin = new Date('2000-01-01 ' + heureFin);
            const diffHeures = (fin - debut) / (1000 * 60 * 60);
            
            let total = prixHeure * diffHeures;
            
            // Prix des options
            const checkboxes = context.querySelectorAll('#optionsList input[type="checkbox"]:checked');
            checkboxes.forEach(cb => {
                const price = parseFloat(cb.dataset.price) || 0;
                total += price;
            });
            
            // Afficher le prix
            const prixSection = context.querySelector('#prixTotalSection') || document.getElementById('prixTotalSection');
            const prixDisplay = context.querySelector('#prixTotalDisplay') || document.getElementById('prixTotalDisplay');
            
            if (prixSection && prixDisplay && total > 0) {
                prixSection.style.display = 'block';
                prixDisplay.textContent = total.toFixed(2) + ' MAD';
            }
        }
    </script>
</body>
</html>