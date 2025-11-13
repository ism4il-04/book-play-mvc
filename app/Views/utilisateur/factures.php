<?php
// app/views/utilisateur/factures.php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$user = $user ?? null;

// Les données sont passées depuis le contrôleur
$factures = $factures ?? [];

// Construct user display name
$userName = 'Client';
if ($user) {
    if (isset($user['name'])) {
        $userName = $user['name'];
    } elseif (isset($user['prenom']) && isset($user['nom'])) {
        $userName = $user['prenom'] . ' ' . $user['nom'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Factures - Book&Play</title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
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
        }

        body {
            background-color: #f8f9fa;
            color: var(--dark);
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
        .facture-section {
            background: linear-gradient(135deg, #6f9566 0%, #387321 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }

        .facture-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .facture-section p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .facture-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .facture-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .facture-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .facture-info {
            padding: 20px;
        }

        .facture-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }

        .detail-item {
            text-align: center;
        }

        .detail-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .facture-actions {
            padding: 20px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .prix-info {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }

        .btn-view {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
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
                        <a class="nav-link" href="<?php echo $baseUrl; ?>utilisateur/dashboard">
                            <i class="fas fa-home me-1"></i> Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>home/terrains">
                            <i class="far fa-calendar-alt me-1"></i> Mes Réservations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $baseUrl; ?>facture/client">
                            <i class="fas fa-file-invoice-dollar me-1"></i> Mes Factures
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
                    <li class="nav-item me-3">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>compte">
                            <i class="far fa-user me-1"></i> Mon Compte
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <?php echo strtoupper(substr($userName, 0, 1)); ?>
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
        <!-- Header Section -->
        <section class="facture-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h1><i class="fas fa-file-invoice-dollar"></i> Mes Factures</h1>
                        <p>Consultez et téléchargez vos factures de réservation</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="container">
            <!-- Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Liste des factures -->
            <?php if (!empty($factures)): ?>
                <div class="row">
                    <?php foreach ($factures as $facture): ?>
                        <div class="col-12">
                            <div class="facture-card">
                                <div class="facture-header">
                                    <div>
                                        <h5 class="mb-0">
                                            <i class="fas fa-file-invoice"></i>
                                            Facture #<?= str_pad($facture['num_facture'], 6, '0', STR_PAD_LEFT) ?>
                                        </h5>
                                        <small>
                                            Émise le <?= date('d/m/Y', strtotime($facture['date_facturation'])) ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-success">
                                        <?= number_format($facture['TTC'], 2, ',', ' ') ?> DH
                                    </span>
                                </div>

                                <div class="facture-info">
                                    <!-- Détails de la réservation -->
                                    <div class="facture-details">
                                        <div class="detail-item">
                                            <div class="detail-label">Terrain</div>
                                            <div class="detail-value">
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-futbol"></i> <?= htmlspecialchars($facture['nom_terrain']) ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="detail-item">
                                            <div class="detail-label">Type</div>
                                            <div class="detail-value"><?= htmlspecialchars($facture['type_terrain']) ?></div>
                                        </div>

                                        <div class="detail-item">
                                            <div class="detail-label">Format</div>
                                            <div class="detail-value"><?= htmlspecialchars($facture['format_terrain']) ?></div>
                                        </div>

                                        <div class="detail-item">
                                            <div class="detail-label">Date Réservation</div>
                                            <div class="detail-value">
                                                <?= date('d/m/Y', strtotime($facture['date_reservation'])) ?> à
                                                <?= date('H:i', strtotime($facture['creneau'])) ?>
                                            </div>
                                        </div>

                                        <div class="detail-item">
                                            <div class="detail-label">Prix/heure</div>
                                            <div class="detail-value"><?= number_format($facture['prix_heure'], 2, ',', ' ') ?> DH</div>
                                        </div>

                                        <div class="detail-item">
                                            <div class="detail-label">Type Réservation</div>
                                            <div class="detail-value">
                                                <?= $facture['type'] === 'tournoi' ? 'Tournoi' : 'Normal' ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Commentaire si présent -->
                                    <?php if (!empty($facture['commentaire'])): ?>
                                        <div class="mt-3">
                                            <strong><i class="fas fa-comment"></i> Commentaire:</strong>
                                            <p class="mb-0 mt-1 text-muted fst-italic">
                                                "<?= htmlspecialchars($facture['commentaire']) ?>"
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="facture-actions">
                                    <div class="prix-info">
                                        <i class="fas fa-euro-sign"></i>
                                        Total: <?= number_format($facture['TTC'], 2, ',', ' ') ?> DH
                                    </div>

                                    <div>
                                        <a href="<?= BASE_URL ?>facture/download/<?= $facture['num_facture'] ?>"
                                           class="btn btn-view" target="_blank">
                                            <i class="fas fa-eye"></i> Voir Facture
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- État vide -->
                <div class="empty-state">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <h3>Aucune facture trouvée</h3>
                    <p>Vous n'avez pas encore de factures générées pour vos réservations.</p>
                    <a href="<?= BASE_URL ?>home/terrains" class="btn btn-success">
                        <i class="fas fa-plus"></i> Faire une réservation
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <footer class="main-footer mt-5">
            <div class="footer-content text-center py-4">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tous droits réservés.</p>
            </div>
        </footer>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
