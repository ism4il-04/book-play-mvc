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
                            <input type="text" class="form-control" id="searchInput" placeholder="Rechercher par nom, terrain, email...">
                        </div>
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
                                    <i class="bi bi-buildings"></i>
                                    <span><?php echo htmlspecialchars($gestionnaire['nom_terrain'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-calendar-check"></i>
                                    <span>Actif depuis: <?php echo htmlspecialchars($gestionnaire['date_validation'] ?? 'N/A'); ?></span>
                                </div>
                            </div>
                            <div class="card-actions">
                                <button class="btn btn-details" onclick="voirDetails(<?php echo $gestionnaire['id']; ?>)">
                                    <i class="bi bi-eye"></i>Détails
                                </button>
                                <button class="btn btn-danger" onclick="supprimerGestionnaire(<?php echo $gestionnaire['id']; ?>, '<?php echo htmlspecialchars($gestionnaire['prenom'] . ' ' . $gestionnaire['nom']); ?>')">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </div>
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
                                    <i class="bi bi-buildings"></i>
                                    <span><?php echo htmlspecialchars($gestionnaire['nom_terrain'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-calendar"></i>
                                    <span>Demande le: <?php echo htmlspecialchars($gestionnaire['date_demande'] ?? ''); ?></span>
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
                <div class="modal-body">
                    <!-- Profile Section -->
                    <div class="profile-section">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle" id="avatarCircle">
                                <!-- Initiales -->
                            </div>
                            <div class="profile-info ms-3">
                                <h4 id="fullName"><!-- Nom complet --></h4>
                                <p class="profile-role mb-0">Gestionnaire de terrain</p>
                                <span class="status-badge" id="statusBadge"><!-- Statut --></span>
                            </div>
                        </div>
                    </div>

                    <!-- Informations Cards Row -->
                    <div class="row">
                        <!-- Contact Information -->
                        <div class="col-md-6 mb-3">
                            <div class="info-card">
                                <h5 class="info-card-title">
                                    <i class="bi bi-person-circle"></i>
                                    Informations de contact
                                </h5>
                                
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-envelope"></i>
                                        <span>Email</span>
                                    </div>
                                    <div class="info-value" id="emailValue"><!-- Email --></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-telephone"></i>
                                        <span>Téléphone</span>
                                    </div>
                                    <div class="info-value" id="telephoneValue"><!-- Téléphone --></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-calendar"></i>
                                        <span>Date de demande</span>
                                    </div>
                                    <div class="info-value" id="dateDemandeValue"><!-- Date --></div>
                                </div>
                                
                                <div class="info-item" id="ribItem" style="display: none;">
                                    <div class="info-label">
                                        <i class="bi bi-credit-card"></i>
                                        <span>RIB</span>
                                    </div>
                                    <div class="info-value rib-value" id="ribValue"><!-- RIB --></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Terrain Information -->
                        <div class="col-md-6 mb-3">
                            <div class="info-card">
                                <h5 class="info-card-title">
                                    <i class="bi bi-buildings"></i>
                                    Informations du terrain
                                </h5>
                                
                                <div class="info-item" id="nomTerrainItem" style="display: none;">
                                    <div class="info-label">
                                        <i class="bi bi-building"></i>
                                        <span>Nom du terrain</span>
                                    </div>
                                    <div class="info-value" id="nomTerrainValue"><!-- Nom terrain --></div>
                                </div>
                                
                                <div class="info-item" id="localisationItem" style="display: none;">
                                    <div class="info-label">
                                        <i class="bi bi-geo-alt"></i>
                                        <span>Localisation</span>
                                    </div>
                                    <div class="info-value" id="localisationValue"><!-- Localisation --></div>
                                </div>
                                
                                <div class="info-item" id="formatTerrainItem" style="display: none;">
                                    <div class="info-label">
                                        <i class="bi bi-arrows-angle-expand"></i>
                                        <span>Format du terrain</span>
                                    </div>
                                    <div class="info-value" id="formatTerrainValue"><!-- Format --></div>
                                </div>
                                
                                <div class="info-item" id="typeTerrainItem" style="display: none;">
                                    <div class="info-label">
                                        <i class="bi bi-grid-3x3-gap"></i>
                                        <span>Type de terrain</span>
                                    </div>
                                    <div class="info-value" id="typeTerrainValue"><!-- Type terrain --></div>
                                </div>
                                
                                <div class="info-item" id="prixHeureItem" style="display: none;">
                                    <div class="info-label">
                                        <i class="bi bi-currency-dollar"></i>
                                        <span>Prix par heure</span>
                                    </div>
                                    <div class="info-value" id="prixHeureValue"><!-- Prix --></div>
                                </div>
                                
                                <div class="info-item" id="heureOuvertureItem" style="display: none;">
                                    <div class="info-label">
                                        <i class="bi bi-clock"></i>
                                        <span>Heure d'ouverture</span>
                                    </div>
                                    <div class="info-value" id="heureOuvertureValue"><!-- Heure ouverture --></div>
                                </div>
                                
                                <div class="info-item" id="heureFermetureItem" style="display: none;">
                                    <div class="info-label">
                                        <i class="bi bi-clock-fill"></i>
                                        <span>Heure de fermeture</span>
                                    </div>
                                    <div class="info-value" id="heureFermetureValue"><!-- Heure fermeture --></div>
                                </div>
                                
                                <div class="info-item" id="optionsItem" style="display: none;">
                                    <div class="info-label">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>Options disponibles</span>
                                    </div>
                                    <div class="info-value" id="optionsValue"><!-- Options --></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="documents-card" id="documentsCard" style="display: none;">
                        <h5 class="info-card-title">
                            <i class="bi bi-file-earmark-text"></i>
                            Documents d'enregistrement
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
            // Désactiver le bouton pendant le traitement
            const acceptBtn = document.querySelector(`button[onclick="acceptGestionnaire(${id}, ${idTerrain})"]`);
            const rejectBtn = document.querySelector(`button[onclick="rejectGestionnaire(${id}, ${idTerrain})"]`);
            
            if (acceptBtn) {
                acceptBtn.disabled = true;
                acceptBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Traitement...';
            }
            if (rejectBtn) {
                rejectBtn.disabled = true;
            }

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
                    // Marquer la demande comme récemment mise à jour pour éviter la double notification
                    if (window.demandeGestionnaireMonitor) {
                        window.demandeGestionnaireMonitor.marquerDemandeMiseAJour(id);
                    }
                    
                    // Récupérer l'élément gestionnaire dans la liste "en attente"
                    const gestionnaireCard = acceptBtn.closest('.gestionnaire-card');
                    
                    if (gestionnaireCard) {
                        // Ajouter une animation de sortie
                        gestionnaireCard.style.transition = 'all 0.3s ease-out';
                        gestionnaireCard.style.transform = 'scale(0.95)';
                        gestionnaireCard.style.opacity = '0.7';
                        
                        // Attendre la fin de l'animation avant de déplacer l'élément
                        setTimeout(() => {
                            // Déplacer l'élément vers la section "accepté"
                            moveGestionnaireToAccepted(gestionnaireCard, id, idTerrain);
                            
                            // Mettre à jour les compteurs
                            updateCounters(-1, 1, 0); // -1 en attente, +1 accepté, 0 refusé
                            
                            // Afficher le message de succès
                            showSuccessMessage(data.message, data.email_status);
                        }, 300);
                    }
                } else {
                    // Réactiver les boutons en cas d'erreur
                    if (acceptBtn) {
                        acceptBtn.disabled = false;
                        acceptBtn.innerHTML = '<i class="bi bi-check"></i> Accepter';
                    }
                    if (rejectBtn) {
                        rejectBtn.disabled = false;
                    }
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Réactiver les boutons en cas d'erreur
                if (acceptBtn) {
                    acceptBtn.disabled = false;
                    acceptBtn.innerHTML = '<i class="bi bi-check"></i> Accepter';
                }
                if (rejectBtn) {
                    rejectBtn.disabled = false;
                }
                alert('Une erreur est survenue lors de l\'acceptation');
            });
        }

        function rejectGestionnaire(id, idTerrain) {
            // Désactiver les boutons pendant le traitement
            const rejectBtn = document.querySelector(`button[onclick="rejectGestionnaire(${id}, ${idTerrain})"]`);
            const acceptBtn = document.querySelector(`button[onclick="acceptGestionnaire(${id}, ${idTerrain})"]`);
            
            if (rejectBtn) {
                rejectBtn.disabled = true;
                rejectBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Traitement...';
            }
            if (acceptBtn) {
                acceptBtn.disabled = true;
            }

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
                    // Marquer la demande comme récemment mise à jour pour éviter la double notification
                    if (window.demandeGestionnaireMonitor) {
                        window.demandeGestionnaireMonitor.marquerDemandeMiseAJour(id);
                    }
                    
                    // Récupérer l'élément gestionnaire dans la liste "en attente"
                    const gestionnaireCard = rejectBtn.closest('.gestionnaire-card');
                    
                    if (gestionnaireCard) {
                        // Ajouter une animation de sortie
                        gestionnaireCard.style.transition = 'all 0.3s ease-out';
                        gestionnaireCard.style.transform = 'scale(0.95)';
                        gestionnaireCard.style.opacity = '0.7';
                        
                        // Attendre la fin de l'animation avant de déplacer l'élément
                        setTimeout(() => {
                            // Déplacer l'élément vers la section "refusé"
                            moveGestionnaireToRejected(gestionnaireCard, id, idTerrain);
                            
                            // Mettre à jour les compteurs
                            updateCounters(-1, 0, 1); // -1 en attente, 0 accepté, +1 refusé
                            
                            // Afficher le message de succès
                            showSuccessMessage(data.message, data.email_status);
                        }, 300);
                    }
                } else {
                    // Réactiver les boutons en cas d'erreur
                    if (rejectBtn) {
                        rejectBtn.disabled = false;
                        rejectBtn.innerHTML = '<i class="bi bi-x"></i> Refuser';
                    }
                    if (acceptBtn) {
                        acceptBtn.disabled = false;
                    }
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Réactiver les boutons en cas d'erreur
                if (rejectBtn) {
                    rejectBtn.disabled = false;
                    rejectBtn.innerHTML = '<i class="bi bi-x"></i> Refuser';
                }
                if (acceptBtn) {
                    acceptBtn.disabled = false;
                }
                alert('Une erreur est survenue lors du refus');
            });
        }

        function remettreEnAttente(id, idTerrain) {
            // Désactiver le bouton pendant le traitement
            const cancelBtn = document.querySelector(`button[onclick="remettreEnAttente(${id}, ${idTerrain})"]`);
            if (cancelBtn) {
                cancelBtn.disabled = true;
                cancelBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Traitement...';
            }

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
                    // Récupérer l'élément gestionnaire dans la liste "refusé"
                    const gestionnaireCard = cancelBtn.closest('.gestionnaire-card');
                    
                    if (gestionnaireCard) {
                        // Ajouter une animation de sortie
                        gestionnaireCard.style.transition = 'all 0.3s ease-out';
                        gestionnaireCard.style.transform = 'scale(0.95)';
                        gestionnaireCard.style.opacity = '0.7';
                        
                        // Attendre la fin de l'animation avant de déplacer l'élément
                        setTimeout(() => {
                            // Déplacer l'élément vers la section "en attente"
                            moveGestionnaireToWaiting(gestionnaireCard, id, idTerrain);
                            
                            // Mettre à jour les compteurs
                            updateCounters(1, 0, -1); // +1 en attente, 0 accepté, -1 refusé
                            
                            // Afficher le message de succès
                            showSuccessMessage('Gestionnaire remis en attente avec succès');
                        }, 300);
                    }
                } else {
                    // Réactiver le bouton en cas d'erreur
                    if (cancelBtn) {
                        cancelBtn.disabled = false;
                        cancelBtn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i> Annuler';
                    }
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Réactiver le bouton en cas d'erreur
                if (cancelBtn) {
                    cancelBtn.disabled = false;
                    cancelBtn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i> Annuler';
                }
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
                    // Remplir le template avec les données
                    remplirTemplate(data.gestionnaire);
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

        function supprimerGestionnaire(id, nom) {
            // Confirmation avant suppression
            if (confirm(`Êtes-vous sûr de vouloir supprimer définitivement le gestionnaire "${nom}" ?\n\nCette action est irréversible et supprimera :\n- Le gestionnaire\n- Son terrain\n- Toutes les données associées`)) {
                
                // Désactiver le bouton pendant le traitement
                const deleteBtn = document.querySelector(`button[onclick="supprimerGestionnaire(${id}, '${nom}')"]`);
                if (deleteBtn) {
                    deleteBtn.disabled = true;
                    deleteBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Suppression...';
                }

                // Appel AJAX pour supprimer
                fetch('<?php echo BASE_URL; ?>Gestion_gestionnaire/supprimer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Récupérer l'élément gestionnaire
                        const gestionnaireCard = deleteBtn.closest('.gestionnaire-card');
                        
                        if (gestionnaireCard) {
                            // Animation de suppression
                            gestionnaireCard.style.transition = 'all 0.3s ease-out';
                            gestionnaireCard.style.transform = 'scale(0.8)';
                            gestionnaireCard.style.opacity = '0';
                            
                            // Attendre la fin de l'animation avant de supprimer l'élément
                            setTimeout(() => {
                                // Supprimer l'élément du DOM
                                gestionnaireCard.remove();
                                
                                // Mettre à jour le compteur (décrémenter acceptés)
                                updateCounters(0, -1, 0); // 0 en attente, -1 accepté, 0 refusé
                                
                                // Vérifier s'il ne reste plus de gestionnaires acceptés
                                const acceptedGrid = document.getElementById('grid-accepte');
                                const remainingCards = acceptedGrid.querySelectorAll('.gestionnaire-card');
                                if (remainingCards.length === 0) {
                                    acceptedGrid.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle"></i> Aucun Proprietaires accepté</div>';
                                }
                                
                                // Afficher le message de succès
                                showSuccessMessage('Gestionnaire supprimé avec succès');
                            }, 300);
                        }
                    } else {
                        // Réactiver le bouton en cas d'erreur
                        if (deleteBtn) {
                            deleteBtn.disabled = false;
                            deleteBtn.innerHTML = '<i class="bi bi-trash"></i> Supprimer';
                        }
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    // Réactiver le bouton en cas d'erreur
                    if (deleteBtn) {
                        deleteBtn.disabled = false;
                        deleteBtn.innerHTML = '<i class="bi bi-trash"></i> Supprimer';
                    }
                    alert('Une erreur est survenue lors de la suppression');
                });
            }
        }

        function remplirTemplate(gestionnaire) {
            // Avatar et nom
            document.getElementById('avatarCircle').textContent = 
                gestionnaire.prenom.charAt(0).toUpperCase() + gestionnaire.nom.charAt(0).toUpperCase();
            document.getElementById('fullName').textContent = gestionnaire.prenom + ' ' + gestionnaire.nom;
            
            // Badge de statut
            const statusBadge = document.getElementById('statusBadge');
            statusBadge.textContent = gestionnaire.status;
            statusBadge.className = 'status-badge ' + getStatusBadgeClass(gestionnaire.status);
            
            // Informations de contact
            document.getElementById('emailValue').textContent = gestionnaire.email;
            document.getElementById('telephoneValue').textContent = gestionnaire.num_tel || 'N/A';
            document.getElementById('dateDemandeValue').textContent = gestionnaire.date_demande || 'N/A';
            
            // RIB (afficher/masquer selon disponibilité)
            const ribItem = document.getElementById('ribItem');
            if (gestionnaire.RIB) {
                document.getElementById('ribValue').textContent = gestionnaire.RIB;
                ribItem.style.display = 'block';
            } else {
                ribItem.style.display = 'none';
            }
            
            // Informations du terrain
            afficherSiExiste('nomTerrainItem', 'nomTerrainValue', gestionnaire.nom_terrain);
            afficherSiExiste('localisationItem', 'localisationValue', gestionnaire.localisation);
            afficherSiExiste('formatTerrainItem', 'formatTerrainValue', gestionnaire.format_terrain);
            afficherSiExiste('typeTerrainItem', 'typeTerrainValue', gestionnaire.type_terrain);
            
            // Prix par heure
            const prixItem = document.getElementById('prixHeureItem');
            if (gestionnaire.prix_heure) {
                document.getElementById('prixHeureValue').textContent = gestionnaire.prix_heure + ' dh/h';
                prixItem.style.display = 'block';
            } else {
                prixItem.style.display = 'none';
            }
            
            // Heure d'ouverture
            afficherSiExiste('heureOuvertureItem', 'heureOuvertureValue', gestionnaire.heure_ouverture);
            
            // Heure de fermeture
            afficherSiExiste('heureFermetureItem', 'heureFermetureValue', gestionnaire.heure_fermeture);
            
            // Options disponibles
            const optionsItem = document.getElementById('optionsItem');
            if (gestionnaire.options && gestionnaire.options.length > 0) {
                const optionsText = gestionnaire.options.map(option => 
                    `${option.nom_option} (${option.prix_option} dh)`
                ).join(', ');
                document.getElementById('optionsValue').textContent = optionsText;
                optionsItem.style.display = 'block';
            } else {
                optionsItem.style.display = 'none';
            }
            
            // Documents
            const documentsCard = document.getElementById('documentsCard');
            if (gestionnaire.justificatif) {
                try {
                    // Parser le JSON pour récupérer les noms de fichiers
                    const fichiers = JSON.parse(gestionnaire.justificatif);
                    if (Array.isArray(fichiers) && fichiers.length > 0) {
                        // Vider le contenu existant
                        const documentsContainer = document.querySelector('#documentsCard .documents-list');
                        if (documentsContainer) {
                            documentsContainer.innerHTML = '';
                        }
                        
                        // Créer un élément pour chaque fichier
                        fichiers.forEach((fichier, index) => {
                            const documentItem = document.createElement('div');
                            documentItem.className = 'document-item mb-3';
                            documentItem.innerHTML = `
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <i class="bi bi-file-earmark-pdf document-icon me-3"></i>
                                        <div class="document-info">
                                            <h6 class="mb-1">${fichier}</h6>
                                            <small class="document-type text-muted">Document justificatif</small>
                                        </div>
                                    </div>
                                    <div class="document-actions ms-4">
                                        <button type="button" class="btn-document btn-consulter me-4" onclick="consulterDocument('${fichier}')">
                                            <i class="bi bi-eye"></i>
                                            <span>Consulter</span>
                                        </button>
                                        <button type="button" class="btn-document btn-telecharger" onclick="telechargerDocument('${fichier}')">
                                            <i class="bi bi-download"></i>
                                            <span>Télécharger</span>
                                        </button>
                                    </div>
                                </div>
                            `;
                            
                            if (documentsContainer) {
                                documentsContainer.appendChild(documentItem);
                            }
                        });
                        
                        documentsCard.style.display = 'block';
                    } else {
                        documentsCard.style.display = 'none';
                    }
                } catch (e) {
                    // Si ce n'est pas du JSON valide, afficher tel quel (fallback)
                    const documentsContainer = document.querySelector('#documentsCard .documents-list');
                    if (documentsContainer) {
                        documentsContainer.innerHTML = `
                            <div class="document-item mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <i class="bi bi-file-earmark-pdf document-icon me-3"></i>
                                        <div class="document-info">
                                            <h6 class="mb-1">${gestionnaire.justificatif}</h6>
                                            <small class="document-type text-muted">Document justificatif</small>
                                        </div>
                                    </div>
                                    <div class="document-actions ms-4">
                                        <button type="button" class="btn-document btn-consulter me-4 " onclick="consulterDocument('${gestionnaire.justificatif}')">
                                            <i class="bi bi-eye"></i>
                                            <span>Consulter</span>
                                        </button>
                                        <button type="button" class="btn-document btn-telecharger" onclick="telechargerDocument('${gestionnaire.justificatif}')">
                                            <i class="bi bi-download"></i>
                                            <span>Télécharger</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    documentsCard.style.display = 'block';
                }
            } else {
                documentsCard.style.display = 'none';
            }
        }
        
        function afficherSiExiste(itemId, valueId, valeur) {
            const item = document.getElementById(itemId);
            if (valeur) {
                document.getElementById(valueId).textContent = valeur;
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        }

        function getStatusBadgeClass(status) {
            const statusClasses = {
                'En attente': 'en-attente',
                'Accepté': 'accepte',
                'Accepte': 'accepte',
                'Refusé': 'refuse',
                'Refuse': 'refuse'
            };
            return statusClasses[status] || 'en-attente';
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

        // Fonction pour déplacer un gestionnaire vers la section "accepté"
        function moveGestionnaireToAccepted(gestionnaireCard, id, idTerrain) {
            // Extraire les données du gestionnaire depuis la carte
            const gestionnaireData = extractGestionnaireData(gestionnaireCard);
            
            // Créer la nouvelle carte pour la section "accepté"
            const newCard = createAcceptedGestionnaireCard(gestionnaireData, id, idTerrain);
            
            // Ajouter la nouvelle carte à la section "accepté"
            const acceptedGrid = document.getElementById('grid-accepte');
            
            // Vérifier s'il y a un message "Aucun gestionnaire accepté" et le supprimer
            const noDataAlert = acceptedGrid.querySelector('.alert.alert-info');
            if (noDataAlert) {
                noDataAlert.remove();
            }
            
            // Ajouter la nouvelle carte avec une animation d'entrée
            newCard.style.opacity = '0';
            newCard.style.transform = 'scale(0.95)';
            acceptedGrid.appendChild(newCard);
            
            // Animation d'entrée
            setTimeout(() => {
                newCard.style.transition = 'all 0.3s ease-in';
                newCard.style.opacity = '1';
                newCard.style.transform = 'scale(1)';
            }, 50);
            
            // Supprimer l'ancienne carte de la section "en attente"
            gestionnaireCard.remove();
            
            // Vérifier s'il ne reste plus de gestionnaires en attente
            const enAttenteGrid = document.getElementById('grid-en_attente');
            const remainingCards = enAttenteGrid.querySelectorAll('.gestionnaire-card');
            if (remainingCards.length === 0) {
                enAttenteGrid.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle"></i> Aucun Proprietaires en attente</div>';
            }
        }

        // Fonction pour déplacer un gestionnaire vers la section "refusé"
        function moveGestionnaireToRejected(gestionnaireCard, id, idTerrain) {
            // Extraire les données du gestionnaire depuis la carte
            const gestionnaireData = extractGestionnaireData(gestionnaireCard);
            
            // Créer la nouvelle carte pour la section "refusé"
            const newCard = createRejectedGestionnaireCard(gestionnaireData, id, idTerrain);
            
            // Ajouter la nouvelle carte à la section "refusé"
            const rejectedGrid = document.getElementById('grid-refuse');
            
            // Vérifier s'il y a un message "Aucun gestionnaire refusé" et le supprimer
            const noDataAlert = rejectedGrid.querySelector('.alert.alert-info');
            if (noDataAlert) {
                noDataAlert.remove();
            }
            
            // Ajouter la nouvelle carte avec une animation d'entrée
            newCard.style.opacity = '0';
            newCard.style.transform = 'scale(0.95)';
            rejectedGrid.appendChild(newCard);
            
            // Animation d'entrée
            setTimeout(() => {
                newCard.style.transition = 'all 0.3s ease-in';
                newCard.style.opacity = '1';
                newCard.style.transform = 'scale(1)';
            }, 50);
            
            // Supprimer l'ancienne carte de la section "en attente"
            gestionnaireCard.remove();
            
            // Vérifier s'il ne reste plus de gestionnaires en attente
            const enAttenteGrid = document.getElementById('grid-en_attente');
            const remainingCards = enAttenteGrid.querySelectorAll('.gestionnaire-card');
            if (remainingCards.length === 0) {
                enAttenteGrid.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle"></i> Aucun Proprietaires en attente</div>';
            }
        }

        // Fonction pour extraire les données d'un gestionnaire depuis sa carte
        function extractGestionnaireData(card) {
            const nameElement = card.querySelector('.gestionnaire-name');
            const infoItems = card.querySelectorAll('.info-item span');
            const avatarElement = card.querySelector('.avatar-circle');
            
            // Extraire la date de demande depuis le texte
            let dateDemande = '';
            infoItems.forEach(item => {
                const text = item.textContent.trim();
                if (text.includes('Demande le:')) {
                    dateDemande = text.replace('Demande le:', '').trim();
                }
            });
            
            return {
                nom: nameElement ? nameElement.textContent.trim() : '',
                email: infoItems[0] ? infoItems[0].textContent.trim() : '',
                telephone: infoItems[1] ? infoItems[1].textContent.trim() : '',
                terrain: infoItems[2] ? infoItems[2].textContent.trim() : '',
                dateDemande: dateDemande,
                avatarColor: avatarElement ? avatarElement.style.backgroundColor : '#28a745',
                initiales: avatarElement ? avatarElement.textContent.trim() : ''
            };
        }

        // Fonction pour créer une carte de gestionnaire accepté
        function createAcceptedGestionnaireCard(data, id, idTerrain) {
            const card = document.createElement('div');
            card.className = 'gestionnaire-card';
            
            const currentDate = new Date().toLocaleDateString('fr-FR');
            
            card.innerHTML = `
                <div class="card-avatar">
                    <div class="avatar-circle" style="background-color: ${data.avatarColor}">
                        ${data.initiales}
                    </div>
                </div>
                <div class="card-content">
                    <h3 class="gestionnaire-name">${data.nom}</h3>
                    <div class="info-item">
                        <i class="bi bi-envelope"></i>
                        <span>${data.email}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-telephone"></i>
                        <span>${data.telephone}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-buildings"></i>
                        <span>${data.terrain}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-calendar-check"></i>
                        <span>Actif depuis: ${currentDate}</span>
                    </div>
                </div>
                <div class="card-actions">
                    <button class="btn btn-details" onclick="voirDetails(${id})">
                        <i class="bi bi-eye"></i>Détails
                    </button>
                    <button class="btn btn-danger" onclick="supprimerGestionnaire(${id}, '${data.nom}')">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </div>
            `;
            
            return card;
        }

        // Fonction pour déplacer un gestionnaire vers la section "en attente"
        function moveGestionnaireToWaiting(gestionnaireCard, id, idTerrain) {
            // Extraire les données du gestionnaire depuis la carte
            const gestionnaireData = extractGestionnaireData(gestionnaireCard);
            
            // Créer la nouvelle carte pour la section "en attente"
            const newCard = createWaitingGestionnaireCard(gestionnaireData, id, idTerrain);
            
            // Ajouter la nouvelle carte à la section "en attente"
            const waitingGrid = document.getElementById('grid-en_attente');
            
            // Vérifier s'il y a un message "Aucun gestionnaire en attente" et le supprimer
            const noDataAlert = waitingGrid.querySelector('.alert.alert-info');
            if (noDataAlert) {
                noDataAlert.remove();
            }
            
            // Ajouter la nouvelle carte avec une animation d'entrée
            newCard.style.opacity = '0';
            newCard.style.transform = 'scale(0.95)';
            waitingGrid.appendChild(newCard);
            
            // Animation d'entrée
            setTimeout(() => {
                newCard.style.transition = 'all 0.3s ease-in';
                newCard.style.opacity = '1';
                newCard.style.transform = 'scale(1)';
            }, 50);
            
            // Supprimer l'ancienne carte de la section "refusé"
            gestionnaireCard.remove();
            
            // Vérifier s'il ne reste plus de gestionnaires refusés
            const refusedGrid = document.getElementById('grid-refuse');
            const remainingCards = refusedGrid.querySelectorAll('.gestionnaire-card');
            if (remainingCards.length === 0) {
                refusedGrid.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle"></i> Aucun Proprietaires refusé</div>';
            }
        }

        // Fonction pour créer une carte de gestionnaire en attente
        function createWaitingGestionnaireCard(data, id, idTerrain) {
            const card = document.createElement('div');
            card.className = 'gestionnaire-card';
            
            card.innerHTML = `
                <div class="card-avatar">
                    <div class="avatar-circle" style="background-color: #17a2b8">
                        ${data.initiales}
                    </div>
                </div>
                <div class="card-content">
                    <h3 class="gestionnaire-name">${data.nom}</h3>
                    <div class="info-item">
                        <i class="bi bi-envelope"></i>
                        <span>${data.email}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-telephone"></i>
                        <span>${data.telephone}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-buildings"></i>
                        <span>${data.terrain}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-calendar"></i>
                        <span>Demande le: ${data.dateDemande}</span>
                    </div>
                </div>
                <div class="card-actions">
                    <button class="btn btn-accept" onclick="acceptGestionnaire(${id}, ${idTerrain})">
                        <i class="bi bi-check"></i> Accepter
                    </button>
                    <button class="btn btn-reject" onclick="rejectGestionnaire(${id}, ${idTerrain})">
                        <i class="bi bi-x"></i> Refuser
                    </button>
                </div>
                <button class="btn btn-details" onclick="voirDetails(${id})">
                    <i class="bi bi-eye"></i> Voir détails
                </button>
            `;
            
            return card;
        }

        // Fonction pour créer une carte de gestionnaire refusé
        function createRejectedGestionnaireCard(data, id, idTerrain) {
            const card = document.createElement('div');
            card.className = 'gestionnaire-card';
            
            // Extraire la date de demande depuis les données existantes
            const dateDemandeText = data.dateDemande || new Date().toLocaleDateString('fr-FR');
            
            card.innerHTML = `
                <div class="card-avatar">
                    <div class="avatar-circle" style="background-color: #dc3545">
                        ${data.initiales}
                    </div>
                </div>
                <div class="card-content">
                    <h3 class="gestionnaire-name">${data.nom}</h3>
                    <div class="info-item">
                        <i class="bi bi-envelope"></i>
                        <span>${data.email}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-telephone"></i>
                        <span>${data.telephone}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-buildings"></i>
                        <span>${data.terrain}</span>
                    </div>
                    <div class="info-item">
                        <i class="bi bi-calendar"></i>
                        <span>Demande le: ${dateDemandeText}</span>
                    </div>
                </div>
                <div class="card-actions">
                    <button class="btn btn-accept" onclick="remettreEnAttente(${id}, ${idTerrain})">
                        <i class="bi bi-arrow-counterclockwise"></i> Annuler
                    </button>
                    <button class="btn btn-details" onclick="voirDetails(${id})">
                        <i class="bi bi-eye"></i> Voir détails
                    </button>
                </div>
            `;
            
            return card;
        }

        // Fonction pour mettre à jour les compteurs des onglets
        function updateCounters(enAttenteChange, accepteChange, refuseChange) {
            const enAttenteCounter = document.querySelector('.status-tab[data-status="en_attente"] .count-badge');
            const accepteCounter = document.querySelector('.status-tab[data-status="accepte"] .count-badge');
            const refuseCounter = document.querySelector('.status-tab[data-status="refuse"] .count-badge');
            
            if (enAttenteCounter && enAttenteChange !== 0) {
                const currentCount = parseInt(enAttenteCounter.textContent) || 0;
                enAttenteCounter.textContent = Math.max(0, currentCount + enAttenteChange);
            }
            
            if (accepteCounter && accepteChange !== 0) {
                const currentCount = parseInt(accepteCounter.textContent) || 0;
                accepteCounter.textContent = Math.max(0, currentCount + accepteChange);
            }
            
            if (refuseCounter && refuseChange !== 0) {
                const currentCount = parseInt(refuseCounter.textContent) || 0;
                refuseCounter.textContent = Math.max(0, currentCount + refuseChange);
            }
        }

        // Fonction pour afficher un message de succès
        function showSuccessMessage(message, emailStatus) {
            // Créer un élément de notification
            const notification = document.createElement('div');
            notification.className = 'alert alert-success fade show position-fixed';
            notification.style.cssText = `
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            
            let fullMessage = message;
            if (emailStatus) {
                fullMessage += '<br><small>' + emailStatus + '</small>';
            }
            
            notification.innerHTML = `
                <i class="bi bi-check-circle-fill me-2"></i>
                ${fullMessage}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Ajouter la notification au body
            document.body.appendChild(notification);
            
            // Supprimer automatiquement après 5 secondes
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
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

        // Variables globales pour le système temps réel
        window.userAuthenticated = true;
        window.userRole = 'administrateur';
        window.BASE_URL = '<?php echo $baseUrl; ?>';
    </script>

    <!-- Script de surveillance en temps réel des demandes de gestionnaire -->
    <script src="<?php echo $baseUrl; ?>js/demande-gestionnaire-realtime.js"></script>
<?php include __DIR__ . '/footer.php'; ?>
