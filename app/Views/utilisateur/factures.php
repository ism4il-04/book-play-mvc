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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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

        /* Navbar - Version compacte */
        .navbar {
            background: #064420 !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0.5rem 2rem;
            min-height: 60px;
        }

        .navbar-brand {
            font-weight: 700;
            color: white !important;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            padding: 0;
        }

        .navbar-brand img {
            height: 35px;
            margin-right: 8px;
        }

        .navbar-brand .brand-text {
            color: #b9ff00;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 0.4rem 0.8rem !important;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .nav-link:hover, .nav-link.active {
            color: #ffeb3b !important;
            opacity: 0.9;
        }

        .nav-link i {
            font-size: 0.9rem;
        }

        .center-nav {
            margin: 0 auto;
        }

        .navbar-nav.ms-auto {
            margin-left: auto !important;
        }

        /* Dropdown profil */
        .navbar-nav .dropdown-menu {
            margin-top: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .navbar {
                padding: 0.5rem 1rem;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            .navbar-brand img {
                height: 30px;
            }
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

        /* Footer */
        .main-footer {
            background: #2c3e50;
            color: white;
            margin-top: 3rem;
        }

        .footer-content {
            padding: 1.5rem 0;
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
                        <a class="nav-link" href="<?php echo $baseUrl; ?>utilisateur/mesReservations">
                            Mes Réservations
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
        <!-- Header Section -->
        <section class="facture-section">
            <div class="container" id="factures-container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h1><i class="fas fa-file-invoice-dollar"></i> Mes Factures</h1>
                        <p>Consultez et téléchargez vos factures de réservation</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="container" id="factures-list-container">
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
                        <div class="col-12" data-num-facture="<?= (int) $facture['num_facture'] ?>">
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
                                                <?php
                                                $dateRes = !empty($facture['date_reservation']) ? date('d/m/Y', strtotime($facture['date_reservation'])) : '';
                                                $timeRes = !empty($facture['creneau']) ? date('H:i', strtotime($facture['creneau'])) : '';
                                                ?>
                                                <?= $dateRes ?><?= ($dateRes && $timeRes) ? ' à ' : '' ?><?= $timeRes ?>
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
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME ?? 'Book&Play'; ?>. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const BASE_URL = '<?php echo $baseUrl; ?>';
            const FACTURE_POLL_INTERVAL = 5000; // 5s
            const knownIds = new Set();
            let lastNumFacture = 0;

            function initFromDom() {
                const nodes = document.querySelectorAll('[data-num-facture]');
                nodes.forEach(el => {
                    const id = parseInt(el.getAttribute('data-num-facture'), 10);
                    if (!isNaN(id)) {
                        knownIds.add(id);
                        if (id > lastNumFacture) {
                            lastNumFacture = id;
                        }
                    }
                });
            }

            function buildFactureCard(facture) {
                const col = document.createElement('div');
                col.className = 'col-12';
                col.setAttribute('data-num-facture', facture.num_facture);

                const numStr = String(facture.num_facture).padStart(6, '0');
                const dateFact = facture.date_facturation ? new Date(facture.date_facturation) : null;
                const dateReservation = facture.date_reservation ? new Date(facture.date_reservation) : null;
                const creneau = facture.creneau ? new Date('1970-01-01T' + facture.creneau) : null;

                function formatDate(d) {
                    if (!d || isNaN(d.getTime())) return '';
                    const day = String(d.getDate()).padStart(2, '0');
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const year = d.getFullYear();
                    return day + '/' + month + '/' + year;
                }

                function formatTime(t) {
                    if (!t || isNaN(t.getTime())) return '';
                    const h = String(t.getHours()).padStart(2, '0');
                    const m = String(t.getMinutes()).padStart(2, '0');
                    return h + ':' + m;
                }

                const typeReservation = (facture.type === 'tournoi') ? 'Tournoi' : 'Normal';
                const ttcdh = Number(facture.TTC || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const prixHeure = Number(facture.prix_heure || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                col.innerHTML = `
                    <div class="facture-card">
                        <div class="facture-header">
                            <div>
                                <h5 class="mb-0">
                                    <i class="fas fa-file-invoice"></i>
                                    Facture #${numStr}
                                </h5>
                                <small>
                                    Émise le ${formatDate(dateFact)}
                                </small>
                            </div>
                            <span class="badge bg-success">
                                ${ttcdh} DH
                            </span>
                        </div>

                        <div class="facture-info">
                            <div class="facture-details">
                                <div class="detail-item">
                                    <div class="detail-label">Terrain</div>
                                    <div class="detail-value">
                                        <span class="badge bg-primary">
                                            <i class="fas fa-futbol"></i> ${facture.nom_terrain ? facture.nom_terrain : ''}
                                        </span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-label">Type</div>
                                    <div class="detail-value">${facture.type_terrain ? facture.type_terrain : ''}</div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-label">Format</div>
                                    <div class="detail-value">${facture.format_terrain ? facture.format_terrain : ''}</div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-label">Date Réservation</div>
                                    <div class="detail-value">
                                        ${formatDate(dateReservation)} à ${formatTime(creneau)}
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-label">Prix/heure</div>
                                    <div class="detail-value">${prixHeure} DH</div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-label">Type Réservation</div>
                                    <div class="detail-value">
                                        ${typeReservation}
                                    </div>
                                </div>
                            </div>

                            ${facture.commentaire ? `
                            <div class="mt-3">
                                <strong><i class="fas fa-comment"></i> Commentaire:</strong>
                                <p class="mb-0 mt-1 text-muted fst-italic">
                                    "${facture.commentaire}"
                                </p>
                            </div>` : ''}
                        </div>

                        <div class="facture-actions">
                            <div class="prix-info">
                                <i class="fas fa-euro-sign"></i>
                                Total: ${ttcdh} DH
                            </div>

                            <div>
                                <a href="${BASE_URL}facture/download/${facture.num_facture}"
                                   class="btn btn-view" target="_blank">
                                    <i class="fas fa-eye"></i> Voir Facture
                                </a>
                            </div>
                        </div>
                    </div>
                `;

                return col;
            }

            async function fetchFactures(afterNum) {
                try {
                    let url = BASE_URL + 'facture/clientAjax';
                    if (afterNum && afterNum > 0) {
                        url += '?after=' + encodeURIComponent(afterNum);
                    }

                    const res = await fetch(url, {
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!res.ok) return null;
                    const data = await res.json();
                    if (!data || !data.success || !Array.isArray(data.factures)) return null;
                    return data.factures;
                } catch (e) {
                    return null;
                }
            }

            async function pollFactures() {
                const container = document.getElementById('factures-list-container');
                if (!container) return;

                let listContainer = container.querySelector('.row');
                const emptyState = container.querySelector('.empty-state');

                const factures = await fetchFactures(lastNumFacture);
                if (!factures) return;

                const newOnes = factures.filter(f => {
                    const id = parseInt(f.num_facture, 10);
                    return !isNaN(id) && !knownIds.has(id);
                });

                if (!newOnes.length) return;

                if (emptyState && emptyState.parentElement) {
                    emptyState.parentElement.removeChild(emptyState);
                }

                if (!listContainer) {
                    listContainer = document.createElement('div');
                    listContainer.className = 'row';
                    container.appendChild(listContainer);
                }

                newOnes.forEach(f => {
                    const id = parseInt(f.num_facture, 10);
                    const card = buildFactureCard(f);
                    listContainer.insertBefore(card, listContainer.firstChild);
                    if (!isNaN(id)) {
                        knownIds.add(id);
                        if (id > lastNumFacture) {
                            lastNumFacture = id;
                        }
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                initFromDom();
                pollFactures();
                setInterval(pollFactures, FACTURE_POLL_INTERVAL);
            });
        })();
    </script>
</body>
</html>