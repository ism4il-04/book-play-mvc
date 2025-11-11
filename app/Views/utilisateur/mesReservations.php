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
                            Tournoi
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
                            
                            <div class="reservation-footer">
                                <div class="reservation-price">
                                    <span class="label">Facture totale:</span>
                                    <span class="amount"><?php echo htmlspecialchars($reservation['prix_total'] ?? '0'); ?> MAD</span>
                                </div>
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
                                <i class="bi bi-credit-card"></i> Payer
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
    </script>
</body>
</html>