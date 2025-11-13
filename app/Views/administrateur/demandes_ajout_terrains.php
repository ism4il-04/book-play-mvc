<?php
    require_once __DIR__ . '/../../../config/config.php';
    $baseUrl = BASE_URL;

    $demandesTerrains = $demandes_terrains ?? [];
    $nbrDemandes = $nbrDemandes ?? 0;
    $error = $error ?? null;

    include __DIR__ . '/sidebar.php';
?>
<link rel="stylesheet" href="<?php echo $baseUrl; ?>css/Gestion_gestionnaire.css">
    <div class="main-content">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="header-section mb-4">
                <h1 class="page-title">Demandes d'ajout de terrains</h1>
                <p class="page-subtitle">Gérez les demandes d'ajout de terrains des gestionnaires actifs</p>
            </div>

            <!-- Status Tabs -->
            <div class="status-tabs mb-4">
                <span class="status-tab active" data-status="en_attente" onclick="switchTab('en_attente')">
                    <span class="status-badge pending">
                        <i class="bi bi-clock"></i> En attente
                    </span>
                    <span class="count-badge"><?php echo htmlspecialchars($nbrDemandes); ?></span>
                </span>
            </div>

            <!-- Demandes En Attente -->
            <div class="gestionnaires-grid" id="grid-en_attente">
                <?php if (empty($demandesTerrains)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Aucune demande d'ajout de terrain en attente
                    </div>
                <?php else: ?>
                    <?php foreach ($demandesTerrains as $demande): ?>
                        <div class="gestionnaire-card" data-terrain-id="<?php echo htmlspecialchars($demande['id_terrain']); ?>">
                            <div class="card-avatar">
                                <div class="avatar-circle" style="background-color: <?php echo '#17a2b8'; ?>">
                                    <?php echo strtoupper(substr($demande['prenom'], 0, 1) . substr($demande['nom'], 0, 1)); ?>
                                </div>
                            </div>
                            <div class="card-content">
                                <h3 class="gestionnaire-name"><?php echo htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']); ?></h3>
                                <div class="info-item">
                                    <i class="bi bi-envelope"></i>
                                    <span><?php echo htmlspecialchars($demande['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-telephone"></i>
                                    <span><?php echo htmlspecialchars($demande['num_tel'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-building"></i>
                                    <span><?php echo htmlspecialchars($demande['nom_terrain'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-geo-alt"></i>
                                    <span><?php echo htmlspecialchars($demande['localisation'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-arrows-angle-expand"></i>
                                    <span><?php echo htmlspecialchars($demande['format_terrain'] ?? 'N/A'); ?> - <?php echo htmlspecialchars($demande['type_terrain'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-currency-dollar"></i>
                                    <span><?php echo htmlspecialchars($demande['prix_heure'] ?? 'N/A'); ?> dh/h</span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-calendar"></i>
                                    <span>ID Terrain: <?php echo htmlspecialchars($demande['id_terrain']); ?></span>
                                </div>
                            </div>
                            <div class="card-actions">
                                <button class="btn btn-accept" onclick="accepterTerrain(<?php echo $demande['id_terrain']; ?>)">
                                    <i class="bi bi-check"></i> Accepter
                                </button>
                                <button class="btn btn-reject" onclick="refuserTerrain(<?php echo $demande['id_terrain']; ?>)">
                                    <i class="bi bi-x"></i> Refuser
                                </button>
                            </div>
                            <button class="btn btn-details" onclick="voirDetailsTerrain(<?php echo $demande['id_terrain']; ?>)">
                                <i class="bi bi-eye"></i> Voir détails
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal pour les détails du terrain -->
    <div class="modal fade" id="detailsTerrainModal" tabindex="-1" aria-labelledby="detailsTerrainModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsTerrainModalLabel">Détails de la demande de terrain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Profile Section -->
                    <div class="profile-section">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle" id="terrainAvatarCircle">
                                <!-- Initiales -->
                            </div>
                            <div class="profile-info ms-3">
                                <h4 id="terrainFullName"><!-- Nom complet --></h4>
                                <p class="profile-role mb-0">Gestionnaire de terrain</p>
                                <span class="status-badge" id="terrainStatusBadge"><!-- Statut --></span>
                            </div>
                        </div>
                    </div>

                    <!-- Informations Cards Row -->
                    <div class="row">
                        <!-- Terrain Information -->
                        <div class="col-md-6 mb-3">
                            <div class="info-card">
                                <h5 class="info-card-title">
                                    <i class="bi bi-buildings"></i>
                                    Informations du terrain
                                </h5>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-building"></i>
                                        <span>Nom du terrain</span>
                                    </div>
                                    <div class="info-value" id="terrainNomValue"><!-- Nom terrain --></div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-geo-alt"></i>
                                        <span>Localisation</span>
                                    </div>
                                    <div class="info-value" id="terrainLocalisationValue"><!-- Localisation --></div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-arrows-angle-expand"></i>
                                        <span>Format du terrain</span>
                                    </div>
                                    <div class="info-value" id="terrainFormatValue"><!-- Format --></div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-grid-3x3-gap"></i>
                                        <span>Type de terrain</span>
                                    </div>
                                    <div class="info-value" id="terrainTypeValue"><!-- Type terrain --></div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-currency-dollar"></i>
                                        <span>Prix par heure</span>
                                    </div>
                                    <div class="info-value" id="terrainPrixValue"><!-- Prix --></div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-calendar"></i>
                                        <span>ID Terrain</span>
                                    </div>
                                    <div class="info-value" id="terrainDateValue"><!-- ID Terrain --></div>
                                </div>
                            </div>
                        </div>

                        <!-- Gestionnaire Information -->
                        <div class="col-md-6 mb-3">
                            <div class="info-card">
                                <h5 class="info-card-title">
                                    <i class="bi bi-person-circle"></i>
                                    Informations du gestionnaire
                                </h5>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-person"></i>
                                        <span>Nom complet</span>
                                    </div>
                                    <div class="info-value" id="gestionnaireNomValue"><!-- Nom --></div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-envelope"></i>
                                        <span>Email</span>
                                    </div>
                                    <div class="info-value" id="gestionnaireEmailValue"><!-- Email --></div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-telephone"></i>
                                        <span>Téléphone</span>
                                    </div>
                                    <div class="info-value" id="gestionnaireTelValue"><!-- Téléphone --></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="documents-card" id="terrainDocumentsCard" style="display: none;">
                        <h5 class="info-card-title">
                            <i class="bi bi-file-earmark-text"></i>
                            Documents justificatifs
                        </h5>
                        <div class="documents-list">
                            <!-- Les documents seront ajoutés dynamiquement ici -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-fermer" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $baseUrl; ?>js/terrain-realtime.js"></script>
    <script>
        // Fonction pour basculer entre les onglets
        function switchTab(status) {
            // Masquer toutes les grilles
            document.getElementById('grid-en_attente').style.display = 'none';

            // Afficher la grille sélectionnée
            document.getElementById('grid-' + status).style.display = 'grid';

            // Retirer la classe active de tous les onglets
            document.querySelectorAll('.status-tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Ajouter la classe active à l'onglet sélectionné
            document.querySelector('.status-tab[data-status="' + status + '"]').classList.add('active');
        }

        // Fonction pour rendre une carte de demande de terrain pour l'admin
        function renderAdminTerrainDemandCard(demande) {
            const col = document.createElement('div');
            col.className = 'gestionnaire-card';
            col.setAttribute('data-terrain-id', demande.id_terrain);

            col.innerHTML = `
                <div class="card-avatar">
                    <div class="avatar-circle" style="background-color: ${'#17a2b8'};">
                        ${demande.prenom.charAt(0).toUpperCase()}${demande.nom.charAt(0).toUpperCase()}
                    </div>
                </div>
                <div class="card-content">
                    <h3 class="gestionnaire-name">${demande.prenom} ${demande.nom}</h3>
                    <div class="info-item">
                        <i class="bi bi-envelope"></i>
                        <span>${demande.email}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-telephone"></i>
                        <span>${demande.num_tel || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-building"></i>
                        <span>${demande.nom_terrain || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-geo-alt"></i>
                        <span>${demande.localisation || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-arrows-angle-expand"></i>
                        <span>${demande.format_terrain || 'N/A'} - ${demande.type_terrain || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-currency-dollar"></i>
                        <span>${demande.prix_heure || 'N/A'} dh/h</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-calendar"></i>
                        <span>ID Terrain: ${demande.id_terrain}</span>
                    </div>
                </div>
                <div class="card-actions">
                    <button class="btn btn-accept" onclick="accepterTerrain(${demande.id_terrain})">
                        <i class="bi bi-check"></i> Accepter
                    </button>
                    <button class="btn btn-reject" onclick="refuserTerrain(${demande.id_terrain})">
                        <i class="bi bi-x"></i> Refuser
                    </button>
                </div>
                <button class="btn btn-details" onclick="voirDetailsTerrain(${demande.id_terrain})">
                    <i class="bi bi-eye"></i> Voir détails
                </button>
            `;

            return col;
        }

        function accepterTerrain(idTerrain) {
            // Appel AJAX pour accepter
            fetch('<?php echo BASE_URL; ?>Gestion_gestionnaire/accepterTerrain', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_terrain: idTerrain })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Supprimer la carte du DOM
                    const card = document.querySelector(`button[onclick*="accepterTerrain(${idTerrain})"]`).closest('.gestionnaire-card');
                    if (card) {
                        card.remove();
                        // Mettre à jour le compteur
                        updateDemandCount(-1);
                    }
                    alert(data.message);
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de l\'acceptation');
            });
        }

        function refuserTerrain(idTerrain) {
            // Appel AJAX pour refuser
            fetch('<?php echo BASE_URL; ?>Gestion_gestionnaire/refuserTerrain', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_terrain: idTerrain })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Supprimer la carte du DOM
                    const card = document.querySelector(`button[onclick*="refuserTerrain(${idTerrain})"]`).closest('.gestionnaire-card');
                    if (card) {
                        card.remove();
                        // Mettre à jour le compteur
                        updateDemandCount(-1);
                    }
                    alert(data.message);
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors du refus');
            });
        }

        function updateDemandCount(change) {
            const countBadge = document.querySelector('.status-tab[data-status="en_attente"] .count-badge');
            if (countBadge) {
                const currentCount = parseInt(countBadge.textContent) || 0;
                const newCount = Math.max(0, currentCount + change);
                countBadge.textContent = newCount;

                // Masquer le message "aucune donnée" si on a des demandes
                const grid = document.getElementById('grid-en_attente');
                const noData = grid.querySelector('.alert-info');
                if (newCount === 0 && !noData) {
                    const noDataDiv = document.createElement('div');
                    noDataDiv.className = 'alert alert-info';
                    noDataDiv.innerHTML = '<i class="bi bi-info-circle"></i> Aucune demande d\'ajout de terrain en attente';
                    grid.appendChild(noDataDiv);
                } else if (newCount > 0 && noData) {
                    noData.remove();
                }
            }
        }

        function voirDetailsTerrain(idTerrain) {
            // Pour l'instant, récupérer les données depuis la liste existante
            // Dans une vraie implémentation, on ferait un appel AJAX
            const cards = document.querySelectorAll('.gestionnaire-card');
            let terrainData = null;

            cards.forEach(card => {
                const button = card.querySelector('.btn-details');
                if (button && button.onclick.toString().includes(idTerrain)) {
                    // Extraire les données de la carte
                    const nameElement = card.querySelector('.gestionnaire-name');
                    const emailElement = card.querySelectorAll('.info-item')[0];
                    const telElement = card.querySelectorAll('.info-item')[1];
                    const terrainNomElement = card.querySelectorAll('.info-item')[2];
                    const localisationElement = card.querySelectorAll('.info-item')[3];
                    const formatTypeElement = card.querySelectorAll('.info-item')[4];
                    const prixElement = card.querySelectorAll('.info-item')[5];
                    const idElement = card.querySelectorAll('.info-item')[6];

                    terrainData = {
                        nom_terrain: terrainNomElement ? terrainNomElement.textContent.replace('N/A', '') : '',
                        localisation: localisationElement ? localisationElement.textContent.replace('N/A', '') : '',
                        format_terrain: formatTypeElement ? formatTypeElement.textContent.split(' - ')[0].replace('N/A', '') : '',
                        type_terrain: formatTypeElement ? formatTypeElement.textContent.split(' - ')[1].replace('N/A', '') : '',
                        prix_heure: prixElement ? prixElement.textContent.replace(' dh/h', '').replace('N/A', '') : '',
                        id_terrain: idElement ? idElement.textContent.replace('ID Terrain: ', '') : '',
                        prenom: nameElement ? nameElement.textContent.split(' ')[0] : '',
                        nom: nameElement ? nameElement.textContent.split(' ')[1] : '',
                        email: emailElement ? emailElement.textContent : '',
                        num_tel: telElement ? telElement.textContent.replace('N/A', '') : ''
                    };
                    return;
                }
            });

            if (terrainData) {
                // Remplir le modal avec les données
                remplirTerrainTemplate(terrainData);
                // Afficher la modal
                new bootstrap.Modal(document.getElementById('detailsTerrainModal')).show();
            } else {
                alert('Erreur: Données du terrain non trouvées');
            }
        }

        function remplirTerrainTemplate(terrain) {
            // Avatar et nom
            document.getElementById('terrainAvatarCircle').textContent =
                (terrain.prenom.charAt(0) + terrain.nom.charAt(0)).toUpperCase();
            document.getElementById('terrainFullName').textContent = terrain.prenom + ' ' + terrain.nom;

            // Badge de statut
            const statusBadge = document.getElementById('terrainStatusBadge');
            statusBadge.textContent = 'En attente';
            statusBadge.className = 'status-badge en-attente';

            // Informations du terrain
            document.getElementById('terrainNomValue').textContent = terrain.nom_terrain || 'N/A';
            document.getElementById('terrainLocalisationValue').textContent = terrain.localisation || 'N/A';
            document.getElementById('terrainFormatValue').textContent = terrain.format_terrain || 'N/A';
            document.getElementById('terrainTypeValue').textContent = terrain.type_terrain || 'N/A';
            document.getElementById('terrainPrixValue').textContent = terrain.prix_heure ? terrain.prix_heure + ' dh/h' : 'N/A';
            document.getElementById('terrainDateValue').textContent = terrain.id_terrain || 'N/A';

            // Informations du gestionnaire
            document.getElementById('gestionnaireNomValue').textContent = terrain.prenom + ' ' + terrain.nom;
            document.getElementById('gestionnaireEmailValue').textContent = terrain.email || 'N/A';
            document.getElementById('gestionnaireTelValue').textContent = terrain.num_tel || 'N/A';

            // Masquer la section documents pour l'instant
            document.getElementById('terrainDocumentsCard').style.display = 'none';
        }

        // Initialiser la surveillance en temps réel pour les demandes de terrains
        const terrainDemandsMonitor = new TerrainRealtimeMonitor({
            baseUrl: '<?php echo BASE_URL; ?>',
            checkEndpoint: 'Gestion_gestionnaire/checkNewTerrainDemands',
            containerSelector: '.gestionnaires-grid',
            renderFunction: renderAdminTerrainDemandCard,
            pollingInterval: 5000, // Vérifier toutes les 5 secondes
            getEndpoint: 'Gestion_gestionnaire/getAdminTerrainDemandById', // Use new unique endpoint
            onNewTerrain: function(terrain) {
                // Mettre à jour le compteur de demandes
                const countBadge = document.querySelector('.status-tab[data-status="en_attente"] .count-badge');
                if (countBadge) {
                    const currentCount = parseInt(countBadge.textContent) || 0;
                    countBadge.textContent = currentCount + 1;
                }
            },
            onNewTerrainNotification: 'Nouvelle demande de terrain ajoutée !' // Custom notification message
        });

        // Initialiser au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!empty($demandesTerrains)): ?>
                const terrainIds = [<?php echo implode(',', array_column($demandesTerrains, 'id_terrain')); ?>];
                const maxId = Math.max(...terrainIds);
                terrainDemandsMonitor.init(maxId);
            <?php else: ?>
                terrainDemandsMonitor.init(0);
            <?php endif; ?>

            // Démarrer le polling pour la surveillance en temps réel (init() already calls demarrerPolling())
            // terrainDemandsMonitor.demarrerPolling();
        });
    </script>
