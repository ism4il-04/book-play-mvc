<?php
    require_once __DIR__ . '/../../../config/config.php';
    $baseUrl = BASE_URL;

    $gestionnairesEnAttente = $gestionnaires_en_attente ?? [];
    $gestionnairesAccepte = $gestionnaires_accepte ?? [];
    $gestionnairesRefuse = $gestionnaires_refuse ?? [];
    $nbrEnAttente = $nbrEnAttente ?? 0;
    $nbrAccepte = $nbrAccepte ?? 0;
    $nbrRefuse = $nbrRefuse ?? 0;
    $error = $error ?? null;
    
    include __DIR__ . '/sidebar.php';
?>
<link rel="stylesheet" href="<?php echo $baseUrl; ?>css/Gestion_gestionnaire.css">
    <div class="main-content">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="header-section mb-4">
                <h1 class="page-title">Gestion des Proprietaires</h1>
                <p class="page-subtitle">Gérez les demandes et les comptes Proprietaires</p>
            </div>

            <!-- Search and Filter Section -->
            <div class="search-filter-section mb-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" id="searchInput" placeholder="Rechercher par nom, terrain, ville...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-filter">
                            <i class="bi bi-funnel"></i> Filtrer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Status Tabs -->
            <div class="status-tabs mb-4">
                <span class="status-tab active" data-status="en_attente" onclick="switchTab('en_attente')">
                    <span class="status-badge pending">
                        <i class="bi bi-clock"></i> En attente
                    </span>
                    <span class="count-badge"><?php echo htmlspecialchars($nbrEnAttente); ?></span>
                </span>
                <span class="status-tab" data-status="accepte" onclick="switchTab('accepte')">
                    <span class="status-badge active-tab">
                        <i class="bi bi-check-circle"></i> Gestionnaires actifs
                    </span>
                    <span class="count-badge"><?php echo htmlspecialchars($nbrAccepte); ?></span>
                </span>
                <span class="status-tab" data-status="refuse" onclick="switchTab('refuse')">
                    <span class="status-badge rejected">
                        <i class="bi bi-x-circle"></i> Refusés
                    </span>
                    <span class="count-badge"><?php echo htmlspecialchars($nbrRefuse); ?></span>
                </span>
            </div>

            <!-- Gestionnaires En Attente -->
            <div class="gestionnaires-grid" id="grid-en_attente">
                <?php if (empty($gestionnairesEnAttente)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Aucun Proprietaires en attente
                    </div>
                <?php else: ?>
                    <?php foreach ($gestionnairesEnAttente as $gestionnaire): ?>
                        <div class="gestionnaire-card">
                            <div class="card-avatar">
                                <div class="avatar-circle" style="background-color: <?php echo $gestionnaire['avatar_color'] ?? '#17a2b8'; ?>">
                                    <?php echo strtoupper(substr($gestionnaire['prenom'], 0, 1) . substr($gestionnaire['nom'], 0, 1)); ?>
                                </div>
                            </div>
                            <div class="card-content">
                                <h3 class="gestionnaire-name"><?php echo htmlspecialchars($gestionnaire['prenom'] . ' ' . $gestionnaire['nom']); ?></h3>
                                <div class="info-item">
                                    <i class="bi bi-envelope"></i>
                                    <span><?php echo htmlspecialchars($gestionnaire['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-telephone"></i>
                                    <span><?php echo htmlspecialchars($gestionnaire['num_tel'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-calendar"></i>
                                    <span>Demande le: <?php echo htmlspecialchars($gestionnaire['date_demande'] ?? ''); ?></span>
                                </div>
                            </div>
                            <div class="card-actions">
                                <button class="btn btn-accept" onclick="acceptGestionnaire(<?php echo $gestionnaire['id']; ?>)">
                                    <i class="bi bi-check"></i> Accepter
                                </button>
                                <button class="btn btn-reject" onclick="rejectGestionnaire(<?php echo $gestionnaire['id']; ?>)">
                                    <i class="bi bi-x"></i> Refuser
                                </button>
                            </div>
                            <button class="btn btn-details">
                                <i class="bi bi-eye"></i> Voir détails
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Gestionnaires Acceptés -->
            <div class="gestionnaires-grid" id="grid-accepte" style="display: none;">
                <?php if (empty($gestionnairesAccepte)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Aucun Proprietaires accepté
                    </div>
                <?php else: ?>
                    <?php foreach ($gestionnairesAccepte as $gestionnaire): ?>
                        <div class="gestionnaire-card">
                            <div class="card-avatar">
                                <div class="avatar-circle" style="background-color: <?php echo $gestionnaire['avatar_color'] ?? '#28a745'; ?>">
                                    <?php echo strtoupper(substr($gestionnaire['prenom'], 0, 1) . substr($gestionnaire['nom'], 0, 1)); ?>
                                </div>
                            </div>
                            <div class="card-content">
                                <h3 class="gestionnaire-name"><?php echo htmlspecialchars($gestionnaire['prenom'] . ' ' . $gestionnaire['nom']); ?></h3>
                                <div class="info-item">
                                    <i class="bi bi-envelope"></i>
                                    <span><?php echo htmlspecialchars($gestionnaire['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-telephone"></i>
                                    <span><?php echo htmlspecialchars($gestionnaire['num_tel'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-calendar-check"></i>
                                    <span>Actif depuis: <?php echo htmlspecialchars($gestionnaire['date_demande'] ?? ''); ?></span>
                                </div>
                            </div>
                            <button class="btn btn-details" style="width: 100%;">
                                <i class="bi bi-eye"></i> Voir détails
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Gestionnaires Refusés -->
            <div class="gestionnaires-grid" id="grid-refuse" style="display: none;">
                <?php if (empty($gestionnairesRefuse)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Aucun Proprietaires refusé
                    </div>
                <?php else: ?>
                    <?php foreach ($gestionnairesRefuse as $gestionnaire): ?>
                        <div class="gestionnaire-card">
                            <div class="card-avatar">
                                <div class="avatar-circle" style="background-color: <?php echo $gestionnaire['avatar_color'] ?? '#dc3545'; ?>">
                                    <?php echo strtoupper(substr($gestionnaire['prenom'], 0, 1) . substr($gestionnaire['nom'], 0, 1)); ?>
                                </div>
                            </div>
                            <div class="card-content">
                                <h3 class="gestionnaire-name"><?php echo htmlspecialchars($gestionnaire['prenom'] . ' ' . $gestionnaire['nom']); ?></h3>
                                <div class="info-item">
                                    <i class="bi bi-envelope"></i>
                                    <span><?php echo htmlspecialchars($gestionnaire['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-telephone"></i>
                                    <span><?php echo htmlspecialchars($gestionnaire['num_tel'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-x-circle"></i>
                                    <span>Refusé le: <?php echo htmlspecialchars($gestionnaire['date_demande'] ?? ''); ?></span>
                                </div>
                            </div>
                            <button class="btn btn-details" style="width: 100%;">
                                <i class="bi bi-eye"></i> Voir détails
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour basculer entre les onglets
        function switchTab(status) {
            // Masquer toutes les grilles
            document.getElementById('grid-en_attente').style.display = 'none';
            document.getElementById('grid-accepte').style.display = 'none';
            document.getElementById('grid-refuse').style.display = 'none';
            
            // Afficher la grille sélectionnée
            document.getElementById('grid-' + status).style.display = 'grid';
            
            // Retirer la classe active de tous les onglets
            document.querySelectorAll('.status-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Ajouter la classe active à l'onglet sélectionné
            document.querySelector('.status-tab[data-status="' + status + '"]').classList.add('active');
        }

        function acceptGestionnaire(id) {
            // Appel AJAX pour accepter
            fetch('<?php echo BASE_URL; ?>Gestion_gestionnaire/accepter', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger la page pour mettre à jour les listes
                    window.location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de l\'acceptation');
            });
        }

        function rejectGestionnaire(id) {
            // Appel AJAX pour refuser
            fetch('<?php echo BASE_URL; ?>Gestion_gestionnaire/refuser', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger la page pour mettre à jour les listes
                    window.location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors du refus');
            });
        }

        // Recherche en temps réel
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const activeGrid = document.querySelector('.gestionnaires-grid[style*="display: grid"], .gestionnaires-grid:not([style*="display: none"])');
            const cards = activeGrid ? activeGrid.querySelectorAll('.gestionnaire-card') : [];
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
<?php include __DIR__ . '/footer.php'; ?>
