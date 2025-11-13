<?php
// app/views/gestionnaire/factures.php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$user = $user ?? null;

// Les données sont passées depuis le contrôleur
$reservations = $reservations ?? [];
$terrains = $terrains ?? [];
$filters = $filters ?? [];

// Construct user display name
$userName = 'Gestionnaire';
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
    <title>Gestion des Factures - Book&Play</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/navbar_gestionnaire.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/footer_gestionnaire.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/dashboard_gest.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/gestionnaire.css">
    <style>
        .facture-section {
            background: linear-gradient(135deg, #accc77 0%, #27bd95 100%);
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

        .filters-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 30px;
        }

        .reservation-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .reservation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .reservation-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .reservation-info {
            padding: 20px;
        }

        .client-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .client-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .terrain-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin: 2px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-accepte {
            background: #d4edda;
            color: #155724;
        }

        .status-attente {
            background: #fff3cd;
            color: #856404;
        }

        .status-refuse {
            background: #f8d7da;
            color: #721c24;
        }

        .reservation-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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

        .reservation-actions {
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

        .btn-generate {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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

        .facture-exists {
            background: #e8f5e9 !important;
            border-left: 4px solid #28a745;
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

        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .filter-group input,
        .filter-group select {
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            border-color: #667eea;
            outline: none;
        }

        .btn-filter {
            background: linear-gradient(135deg, #accc77 0%, #27bd95 100%);
            border: none;
            color: white;
            padding: 10px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        @media (max-width: 768px) {
            .reservation-header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .reservation-actions {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .filters-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <?php
    $activeItem = 'factures';
    include __DIR__ . '/../components/nav_gestionnaire.php';
    ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <?php
        $title = 'Gestion des Factures';
        $subtitle = 'Générez et consultez les factures de vos réservations';
        include __DIR__ . '/../components/top_navbar_gestionnaire.php';
        ?>

        <!-- Dashboard Content -->
        <div class="dashboard-container">
    <section class="facture-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1><i class="fas fa-file-invoice-dollar"></i> Gestion des Factures</h1>
                    <p>Générez et consultez les factures de vos réservations</p>
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

        <!-- Filtres -->
        <div class="filters-card card">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="fas fa-filter"></i> Filtres</h5>
                <form method="GET" class="filters-form">
                    <div class="filter-group">
                        <label for="date_debut">Date de début</label>
                        <input type="date" id="date_debut" name="date_debut" value="<?= htmlspecialchars($filters['date_debut'] ?? '') ?>">
                    </div>

                    <div class="filter-group">
                        <label for="date_fin">Date de fin</label>
                        <input type="date" id="date_fin" name="date_fin" value="<?= htmlspecialchars($filters['date_fin'] ?? '') ?>">
                    </div>

                    <div class="filter-group">
                        <label for="terrain_id">Terrain</label>
                        <select id="terrain_id" name="terrain_id">
                            <option value="">Tous les terrains</option>
                            <?php foreach ($terrains as $terrain): ?>
                                <option value="<?= $terrain['id_terrain'] ?>" <?= ($filters['terrain_id'] ?? '') == $terrain['id_terrain'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($terrain['nom_terrain']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="status">Statut</label>
                        <select id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="accepté" <?= ($filters['status'] ?? '') === 'accepté' ? 'selected' : '' ?>>Accepté</option>
                            <option value="en attente" <?= ($filters['status'] ?? '') === 'en attente' ? 'selected' : '' ?>>En attente</option>
                            <option value="refusé" <?= ($filters['status'] ?? '') === 'refusé' ? 'selected' : '' ?>>Refusé</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des réservations -->
        <?php if (!empty($reservations)): ?>
            <div class="row">
                <?php foreach ($reservations as $reservation): ?>
                    <div class="col-12">
                        <div class="reservation-card <?= $reservation['num_facture'] ? 'facture-exists' : '' ?>">
                            <div class="reservation-header">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="fas fa-calendar-check"></i>
                                        Réservation #<?= $reservation['id_reservation'] ?>
                                    </h5>
                                    <small>
                                        <?= date('d/m/Y', strtotime($reservation['date_reservation'])) ?> à
                                        <?= date('H:i', strtotime($reservation['creneau'])) ?>
                                    </small>
                                </div>
                                <span class="status-badge status-<?= $reservation['status'] === 'accepté' ? 'accepte' : ($reservation['status'] === 'en attente' ? 'attente' : 'refuse') ?>">
                                    <?= strtoupper($reservation['status']) ?>
                                </span>
                            </div>

                            <div class="reservation-info">
                                <!-- Informations client -->
                                <div class="client-info">
                                    <div class="client-avatar">
                                        <?= strtoupper(substr($reservation['client_prenom'], 0, 1) . substr($reservation['client_nom'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">
                                            <?= htmlspecialchars($reservation['client_prenom'] . ' ' . $reservation['client_nom']) ?>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-envelope"></i> <?= htmlspecialchars($reservation['client_email']) ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i> <?= htmlspecialchars($reservation['client_tel']) ?>
                                        </small>
                                    </div>
                                </div>

                                <!-- Détails de la réservation -->
                                <div class="reservation-details">
                                    <div class="detail-item">
                                        <div class="detail-label">Terrain</div>
                                        <div class="detail-value">
                                            <span class="terrain-badge">
                                                <i class="fas fa-futbol"></i> <?= htmlspecialchars($reservation['nom_terrain']) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">Type</div>
                                        <div class="detail-value"><?= htmlspecialchars($reservation['type_terrain']) ?></div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">Format</div>
                                        <div class="detail-value"><?= htmlspecialchars($reservation['format_terrain']) ?></div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-label">Prix/heure</div>
                                        <div class="detail-value"><?= number_format($reservation['prix_heure'], 2, ',', ' ') ?> DH</div>
                                    </div>
                                </div>

                                <!-- Commentaire si présent -->
                                <?php if (!empty($reservation['commentaire'])): ?>
                                    <div class="mt-3">
                                        <strong><i class="fas fa-comment"></i> Commentaire:</strong>
                                        <p class="mb-0 mt-1 text-muted fst-italic">
                                            "<?= htmlspecialchars($reservation['commentaire']) ?>"
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="reservation-actions">
                                <div class="prix-info">
                                    <i class="fas fa-euro-sign"></i>
                                    <?= number_format($reservation['prix_heure'], 2, ',', ' ') ?> DH
                                    <?php if ($reservation['type'] === 'tournoi'): ?>
                                        <small class="text-muted">(Tournoi)</small>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <?php if ($reservation['num_facture']): ?>
                                        <!-- Facture existe déjà -->
                                        <a href="<?= BASE_URL ?>facture/download/<?= $reservation['num_facture'] ?>"
                                           class="btn btn-view" target="_blank">
                                            <i class="fas fa-eye"></i> Voir Facture #<?= $reservation['num_facture'] ?>
                                        </a>
                                    <?php elseif ($reservation['status'] === 'accepté'): ?>
                                        <!-- Générer facture -->
                                        <button type="button"
                                                class="btn btn-generate"
                                                onclick="generateFacture(<?= $reservation['id_reservation'] ?>)">
                                            <i class="fas fa-file-invoice-dollar"></i> Générer Facture
                                        </button>
                                    <?php else: ?>
                                        <!-- Réservation pas encore acceptée -->
                                        <span class="text-muted">
                                            <i class="fas fa-clock"></i> En attente d'approbation
                                        </span>
                                    <?php endif; ?>
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
                <h3>Aucune réservation trouvée</h3>
                <p>Il n'y a pas de réservations correspondant à vos critères de recherche.</p>
                <a href="<?= BASE_URL ?>facture" class="btn btn-primary">
                    <i class="fas fa-refresh"></i> Actualiser
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tous droits réservés.</p>
            <div class="footer-links">
                <a href="#">Aide</a>
                <a href="#">Conditions d'utilisation</a>
                <a href="#">Confidentialité</a>
            </div>
        </div>
    </footer>
</main>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function generateFacture(reservationId) {
        if (confirm('Êtes-vous sûr de vouloir générer une facture pour cette réservation ?')) {
            // Désactiver le bouton pendant la génération
            const button = document.querySelector(`button[onclick="generateFacture(${reservationId})"]`);
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération...';
            button.disabled = true;

            // Faire la requête AJAX
            fetch('<?= BASE_URL ?>facture/generate/' + reservationId, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mettre à jour le bouton pour devenir "Voir Facture"
                    const container = button.parentElement;
                    container.innerHTML = `
                        <a href="${data.download_url}"
                           class="btn btn-view" target="_blank">
                            <i class="fas fa-eye"></i> Voir Facture #${data.num_facture}
                        </a>
                    `;

                    // Ouvrir le PDF dans un nouvel onglet
                    window.open(data.download_url, '_blank');

                    // Afficher un message de succès
                    showMessage('Facture générée avec succès!', 'success');
                } else {
                    showMessage(data.message || 'Erreur lors de la génération de la facture', 'error');
                    // Réactiver le bouton
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showMessage('Erreur lors de la génération de la facture', 'error');
                // Réactiver le bouton
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }
    }

    function showMessage(message, type) {
        // Créer un élément de message temporaire
        const messageDiv = document.createElement('div');
        messageDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        messageDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        messageDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(messageDiv);

        // Auto-supprimer après 5 secondes
        setTimeout(() => {
            if (messageDiv.parentElement) {
                messageDiv.remove();
            }
        }, 5000);
    }

    // Auto-submit form on filter change (optionnel)
    document.querySelectorAll('select[name="terrain_id"], select[name="status"]').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
</body>
</html>
