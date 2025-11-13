<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;

$terrains = $terrains ?? [];
$filters = $filters ?? [
    'search' => '',
    'taille' => '',
    'type' => ''
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terrains disponibles - <?php echo APP_NAME; ?></title>
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: #f5f5f5;
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
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 1.5rem;
        }
        
        /* Search Section */
        .search-section {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }
        
        .search-container {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 1rem;
            align-items: center;
        }
        
        .search-box input,
        .filter-select {
            padding: 0.8rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
            width: 100%;
        }
        
        .search-box input:focus,
        .filter-select:focus {
            outline: none;
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.1);
        }
        
        .btn-filter {
            background: var(--primary-green);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .btn-filter:hover {
            background: #053318;
        }
        
        /* Terrain Cards Grid */
        .terrains-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        /* Terrain Card */
        .terrain-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .terrain-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .terrain-image {
            width: 100%;
            height: 190px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .terrain-content {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .terrain-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.8rem;
        }
        
        .terrain-location {
            display: flex;
            align-items: flex-start;
            color: #7f8c8d;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            gap: 0.5rem;
        }
        
        .terrain-location i {
            margin-top: 3px;
            color: var(--accent-cyan);
        }
        
        .terrain-badges {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        
        .badge-primary {
            background: #3498db;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-info {
            background: #1abc9c;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .terrain-contact {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #7f8c8d;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        
        .terrain-contact i {
            color: var(--accent-cyan);
        }
        
        .terrain-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #ecf0f1;
            margin-top: auto;
        }
        
        .terrain-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-green);
        }
        
        .terrain-price small {
            font-size: 0.75rem;
            font-weight: 400;
            color: #7f8c8d;
        }
        
        .btn-reserve {
            background: var(--primary-green);
            color: white;
            border: 2px solid var(--primary-green);
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-reserve:hover {
            background: white;
            color: var(--primary-green);
        }
        
        /* Alert */
        .alert-info {
            background: #e3f2fd;
            border: 1px solid #90caf9;
            color: #1976d2;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
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
            
            .search-container {
                grid-template-columns: 1fr;
            }
            
            .terrains-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }
            
            .search-section {
                padding: 1.2rem;
            }
            
            .terrains-grid {
                grid-template-columns: 1fr;
            }
            
            .terrain-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
            
            .btn-reserve {
                width: 100%;
                text-align: center;
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
                <ul class="navbar-nav center-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $baseUrl; ?>utilisateur/dashboard">
                            Terrains
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>utilisateur/mesReservations">
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
        <h2 class="page-title">Terrains disponibles</h2>

        <!-- Search Section -->
        <div class="search-section">
            <form method="get" action="">
                <div class="search-container">
                    <div class="search-box">
                        <input type="search" name="search" class="form-control" placeholder="Rechercher un terrain..." value="<?php echo htmlspecialchars($filters['search'] ?? '', ENT_QUOTES); ?>">
                    </div>
                    <select name="taille" class="filter-select form-select">
                        <option value="">Toutes les tailles</option>
                        <?php $selTaille = $filters['taille'] ?? ''; ?>
                        <option value="5v5" <?php echo '5v5' === $selTaille ? 'selected' : ''; ?>>5v5</option>
                        <option value="6v6" <?php echo '6v6' === $selTaille ? 'selected' : ''; ?>>6v6</option>
                        <option value="7v7" <?php echo '7v7' === $selTaille ? 'selected' : ''; ?>>7v7</option>
                        <option value="8v8" <?php echo '8v8' === $selTaille ? 'selected' : ''; ?>>8v8</option>
                        <option value="9v9" <?php echo '9v9' === $selTaille ? 'selected' : ''; ?>>9v9</option>
                        <option value="10v10" <?php echo '10v10' === $selTaille ? 'selected' : ''; ?>>10v10</option>
                        <option value="11v11" <?php echo '11v11' === $selTaille ? 'selected' : ''; ?>>11v11</option>
                    </select>
                    <select name="type" class="filter-select form-select">
                        <option value="">Tous les types</option>
                        <?php $selType = $filters['type'] ?? ''; ?>
                        <option value="Gazon naturel" <?php echo 'Gazon naturel' === $selType ? 'selected' : ''; ?>>Gazon naturel</option>
                        <option value="Gazon synthétique" <?php echo 'Gazon synthétique' === $selType ? 'selected' : ''; ?>>Gazon synthétique</option>
                        <option value="Terre / Sol" <?php echo 'Terre / Sol' === $selType ? 'selected' : ''; ?>>Terre / Sol</option>
                        <option value="Terrain couvert / Salle" <?php echo 'Terrain couvert / Salle' === $selType ? 'selected' : ''; ?>>Terrain couvert / Salle</option>
                    </select>
                    <button class="btn-filter" type="submit">Filtrer</button>
                </div>
            </form>
        </div>

        <!-- Terrains Grid -->
        <?php if (!empty($terrains)) { ?>
            <div class="terrains-grid">
                <?php foreach ($terrains as $terrain) { ?>
                    <?php
                    $imageFile = isset($terrain['image']) ? trim($terrain['image']) : '';
                    $safeFile = '' !== $imageFile ? basename($imageFile) : '';
                    $rootDir = realpath(__DIR__ . '/../../../');
                    $absolutePath = $rootDir . '/public/images/' . $safeFile;
                    $imageExists = ('' !== $safeFile) && file_exists($absolutePath);
                    $imageUrl = $imageExists ? ($baseUrl . 'images/' . rawurlencode($safeFile)) : ($baseUrl . 'images/terrain.png');
                    $altText = htmlspecialchars($terrain['localisation'] ?? ($terrain['nom'] ?? 'Terrain'));
                    ?>
                    <div class="terrain-card">
                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo $altText; ?>" class="terrain-image">
                        
                        <div class="terrain-content">
                            <h3 class="terrain-title"><?php echo htmlspecialchars($terrain['nom_terrain'] ?? $terrain['localisation'] ?? 'Lieu inconnu'); ?></h3>
                            
                            <div class="terrain-location">
                                <i class="bi bi-geo-alt"></i>
                                <span><?php echo htmlspecialchars($terrain['localisation'] ?? 'Localisation inconnue'); ?></span>
                            </div>
                            
                            <div class="terrain-badges">
                                <span class="badge-primary"><?php echo htmlspecialchars($terrain['format_terrain'] ?? '-'); ?></span>
                                <span class="badge-info"><?php echo htmlspecialchars($terrain['type_terrain'] ?? '-'); ?></span>
                            </div>
                            
                            <div class="terrain-contact">
                                <i class="bi bi-envelope"></i>
                                <span>gestionnaire@bookplay.ma</span>
                            </div>
                                <div class="terrain-footer">
                                    <div class="terrain-price">
                                        <?php echo htmlspecialchars($terrain['prix_heure']); ?> <small>MAD/heure</small>
                                    </div>
                                    <a href="<?php echo $baseUrl; ?>terrain/reserverTerrain/<?php echo (int)($terrain['id_terrain'] ?? 0); ?>" class="btn-reserve">
                                        Réserver
                                    </a>
                                </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="alert alert-info">
                Aucun terrain disponible pour le moment.
            </div>
        <?php } ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>