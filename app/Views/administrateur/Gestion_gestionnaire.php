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
                                    <i class="bi bi-buildings"></i>
                                    <span><?php echo htmlspecialchars($gestionnaire['nom_terrain'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-calendar"></i>
                                    <span>Demande le: <?php echo htmlspecialchars($gestionnaire['date_demande'] ?? ''); ?></span>
                                </div>
                            </div>
                            <div class="card-actions">
                                <button class="btn btn-accept" onclick="acceptGestionnaire(<?php echo $gestionnaire['id']; ?>, <?php echo $gestionnaire['id_terrain'] ?? 'null'; ?>)">
                                    <i class="bi bi-check"></i> Accepter
                                </button>
                                <button class="btn btn-reject" onclick="rejectGestionnaire(<?php echo $gestionnaire['id']; ?>, <?php echo $gestionnaire['id_terrain'] ?? 'null'; ?>)">
                                    <i class="bi bi-x"></i> Refuser
                                </button>
                            </div>
                            <button class="btn btn-details" onclick="voirDetails(<?php echo $gestionnaire['id']; ?>)">
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
                            <button class="btn btn-details" style="width: 100%;" onclick="voirDetails(<?php echo $gestionnaire['id']; ?>)">
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
                                <div class="info-item">
                                    <i class="bi bi-buildings"></i>
                                    <span><?php echo htmlspecialchars($gestionnaire['nom_terrain'] ?? 'N/A'); ?></span>
                                </div>
                            </div>
                            <div class="card-actions">
                                <button class="btn btn-accept" onclick="remettreEnAttente(<?php echo $gestionnaire['id']; ?>, <?php echo $gestionnaire['id_terrain'] ?? 'null'; ?>)">
                                    <i class="bi bi-arrow-counterclockwise"></i> Annuler
                                </button>
                                <button class="btn btn-details" onclick="voirDetails(<?php echo $gestionnaire['id']; ?>)">
                                    <i class="bi bi-eye"></i> Voir détails
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal pour les détails de la demande -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Détails de la demande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Le contenu sera chargé dynamiquement -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
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

        function acceptGestionnaire(id, idTerrain) {
            // Appel AJAX pour accepter
            fetch('<?php echo BASE_URL; ?>Gestion_gestionnaire/accepter', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id, id_terrain: idTerrain })
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

        function rejectGestionnaire(id, idTerrain) {
            // Appel AJAX pour refuser
            fetch('<?php echo BASE_URL; ?>Gestion_gestionnaire/refuser', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id, id_terrain: idTerrain })
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

        function remettreEnAttente(id, idTerrain) {
            // Appel AJAX pour remettre en attente directement
            fetch('<?php echo BASE_URL; ?>Gestion_gestionnaire/remettreEnAttente', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id, id_terrain: idTerrain })
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
                alert('Une erreur est survenue lors de la remise en attente');
            });
        }

        function voirDetails(id) {
            // Appel AJAX pour récupérer les détails
            fetch('<?php echo BASE_URL; ?>Gestion_gestionnaire/getDetails', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remplir la modal avec les données
                    document.getElementById('modalBody').innerHTML = generateDetailsHTML(data.gestionnaire);
                    // Afficher la modal
                    new bootstrap.Modal(document.getElementById('detailsModal')).show();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors du chargement des détails');
            });
        }

        function generateDetailsHTML(gestionnaire) {
            return `
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar-circle me-3" style="background-color: #17a2b8; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 20px;">
                                ${gestionnaire.prenom.charAt(0).toUpperCase()}${gestionnaire.nom.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <h4 class="mb-1">${gestionnaire.prenom} ${gestionnaire.nom}</h4>
                                <p class="text-muted mb-0">Gestionnaire de terrain</p>
                                <span class="badge ${getStatusBadgeClass(gestionnaire.status)}">${gestionnaire.status}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3"><i class="bi bi-person-circle me-2"></i>Informations de contact</h5>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-envelope text-primary me-2"></i>
                                <strong>Email:</strong>
                            </div>
                            <p class="ms-4">${gestionnaire.email}</p>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-telephone text-primary me-2"></i>
                                <strong>Téléphone:</strong>
                            </div>
                            <p class="ms-4">${gestionnaire.num_tel || 'N/A'}</p>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar text-primary me-2"></i>
                                <strong>Date de demande:</strong>
                            </div>
                            <p class="ms-4">${gestionnaire.date_demande || 'N/A'}</p>
                        </div>
                        ${gestionnaire.RIB ? `
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-credit-card text-primary me-2"></i>
                                <strong>RIB:</strong>
                            </div>
                            <p class="ms-4 font-monospace">${gestionnaire.RIB}</p>
                        </div>
                        ` : ''}
                        ${gestionnaire.date_validation ? `
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar-check text-primary me-2"></i>
                                <strong>Date de validation:</strong>
                            </div>
                            <p class="ms-4">${gestionnaire.date_validation}</p>
                        </div>
                        ` : ''}
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3"><i class="bi bi-buildings me-2"></i>Informations du terrain</h5>
                        ${gestionnaire.nom_terrain ? `
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-building text-primary me-2"></i>
                                <strong>Nom du terrain:</strong>
                            </div>
                            <p class="ms-4">${gestionnaire.nom_terrain}</p>
                        </div>
                        ` : ''}
                        ${gestionnaire.localisation ? `
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-geo-alt text-primary me-2"></i>
                                <strong>Localisation:</strong>
                            </div>
                            <p class="ms-4">${gestionnaire.localisation}</p>
                        </div>
                        ` : ''}
                        ${gestionnaire.format_terrain ? `
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-rulers text-primary me-2"></i>
                                <strong>Format du terrain:</strong>
                            </div>
                            <p class="ms-4">${gestionnaire.format_terrain}</p>
                        </div>
                        ` : ''}
                        ${gestionnaire.type_terrain ? `
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-tag text-primary me-2"></i>
                                <strong>Type de terrain:</strong>
                            </div>
                            <p class="ms-4">${gestionnaire.type_terrain}</p>
                        </div>
                        ` : ''}
                        ${gestionnaire.prix_heure ? `
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-currency-dollar text-primary me-2"></i>
                                <strong>Prix par heure:</strong>
                            </div>
                            <p class="ms-4">${gestionnaire.prix_heure} dh/h</p>
                        </div>
                        ` : ''}
                        ${gestionnaire.statut_terrain ? `
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-flag text-primary me-2"></i>
                                <strong>Statut du terrain:</strong>
                            </div>
                            <p class="ms-4">${gestionnaire.statut_terrain}</p>
                        </div>
                        ` : ''}
                    </div>
                </div>

                ${gestionnaire.justificatif ? `
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5 class="text-primary mb-3"><i class="bi bi-file-earmark-text me-2"></i>Documents d'enregistrement</h5>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-pdf text-danger me-3 fs-4"></i>
                                        <div>
                                            <h6 class="mb-1">${gestionnaire.justificatif}</h6>
                                            <small class="text-muted">Document justificatif</small>
                                        </div>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="consulterDocument('${gestionnaire.justificatif}')">
                                            <i class="bi bi-eye me-1"></i>Consulter
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="telechargerDocument('${gestionnaire.justificatif}')">
                                            <i class="bi bi-download me-1"></i>Télécharger
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ` : ''}
            `;
        }

        function getStatusBadgeClass(status) {
            switch(status) {
                case 'accepté': return 'bg-success';
                case 'refusé': return 'bg-danger';
                case 'en attente': return 'bg-warning text-dark';
                default: return 'bg-secondary';
            }
        }

        function getTerrainStatusBadgeClass(etat) {
            switch(etat) {
                case 'acceptée': return 'bg-success';
                case 'refusée': return 'bg-danger';
                case 'en attente': return 'bg-warning text-dark';
                case 'disponible': return 'bg-info';
                case 'non disponible': return 'bg-secondary';
                default: return 'bg-light text-dark';
            }
        }

        function consulterDocument(nomFichier) {
            // Ouvrir le document dans un nouvel onglet pour consultation
            const url = '<?php echo BASE_URL; ?>uploads/justificatifs/' + nomFichier;
            window.open(url, '_blank');
        }

        function telechargerDocument(nomFichier) {
            // Créer un lien de téléchargement et le déclencher
            const url = '<?php echo BASE_URL; ?>uploads/justificatifs/' + nomFichier;
            const link = document.createElement('a');
            link.href = url;
            link.download = nomFichier;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
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
