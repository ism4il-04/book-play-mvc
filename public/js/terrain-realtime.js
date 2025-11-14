/**
 * Module de Surveillance en Temps Réel des Terrains (Réutilisable)
 * Gère les mises à jour automatiques lorsque de nouveaux terrains sont ajoutés
 */

class TerrainRealtimeMonitor {
    constructor(config) {
        this.baseUrl = config.baseUrl;
        this.checkEndpoint = config.checkEndpoint || 'terrain/checkNewTerrains';
        this.getEndpoint = config.getEndpoint || 'terrain/getTerrainById';
        this.containerSelector = config.containerSelector || '.activities-list';
        this.renderFunction = config.renderFunction;
        this.pollingInterval = config.pollingInterval || 3000;
        this.onNewTerrain = config.onNewTerrain || null;
        this.onTerrainUpdated = config.onTerrainUpdated || null;
        this.onNewTerrainNotification = config.onNewTerrainNotification || 'Nouveau terrain ajouté !';
        
        this.lastTerrainId = 0;
        this.terrainSnapshots = {}; // Stocker les snapshots des terrains pour détecter les changements
        this.isInitialized = false;
        this.pollingTimer = null;
        this.recentlyUpdatedTerrains = new Set(); // Terrains récemment mis à jour par l'utilisateur
    }

    /**
     * Initialiser le moniteur avec l'ID du dernier terrain actuel
     */
    init(initialLastId = 0) {
        this.lastTerrainId = initialLastId;
        this.isInitialized = false; // Will be set to true after first poll
        this.initialiserSnapshots();
        this.demarrerPolling();
    }
    
    /**
     * Initialiser les snapshots des terrains existants au chargement
     */
    initialiserSnapshots() {
        const container = document.querySelector(this.containerSelector);
        if (!container) return;
        
        // Chercher les éléments de terrain (activity-item pour dashboard/gestion, col-* pour home, gestionnaire-card pour admin)
        const terrainElements = container.querySelectorAll('.activity-item, [class*="col-"], .gestionnaire-card');
        
        for (const element of terrainElements) {
            // Méthode 1: Utiliser data-terrain-id (pour toutes les pages)
            let terrainId = element.getAttribute('data-terrain-id');
            
            if (terrainId) {
                // Créer un snapshot vide pour l'instant (sera mis à jour lors du premier polling)
                this.terrainSnapshots[terrainId] = '';
                continue;
            }
            
            // Méthode 2: Extraire depuis le bouton edit (pour gestion_terrains uniquement)
            const editButton = element.querySelector('button[onclick*="openEditModal"]');
            if (!editButton) continue;
            
            const onclickAttr = editButton.getAttribute('onclick');
            const match = onclickAttr.match(/"id_terrain":(\d+)/);
            if (!match) continue;
            
            terrainId = match[1];
            
            // Extraire les données du terrain depuis le DOM pour créer le snapshot initial
            try {
                const terrainDataMatch = onclickAttr.match(/openEditModal\((.*?)\)/);
                if (terrainDataMatch) {
                    const terrainData = JSON.parse(terrainDataMatch[1]);
                    this.terrainSnapshots[terrainId] = this.creerSnapshotTerrain(terrainData);
                }
            } catch (e) {
                console.warn('Impossible de créer le snapshot initial pour le terrain', terrainId);
            }
        }
    }

    /**
     * Démarrer le polling pour les nouveaux terrains
     */
    demarrerPolling() {
        if (this.pollingTimer) {
            clearInterval(this.pollingTimer);
        }
        this.pollingTimer = setInterval(() => this.verifierNouveauxTerrains(), this.pollingInterval);
    }

    /**
     * Arrêter le polling
     */
    arreterPolling() {
        if (this.pollingTimer) {
            clearInterval(this.pollingTimer);
            this.pollingTimer = null;
        }
    }

    /**
     * Vérifier les nouveaux terrains et les modifications
     */
    async verifierNouveauxTerrains() {
        try {
            const response = await fetch(this.baseUrl + this.checkEndpoint, {
                credentials: 'same-origin' // Include session cookies for authentication
            });
            const data = await response.json();
            
            if (data.lastId !== undefined) {
                if (this.isInitialized && data.lastId > this.lastTerrainId) {
                    await this.recupererEtAfficherNouveauTerrain(data.lastId);
                }
                this.lastTerrainId = data.lastId;
            }
            
            // Vérifier les modifications des terrains existants
            if (this.isInitialized) {
                await this.verifierModificationsTerrains();
            } else {
                this.isInitialized = true;
            }
        } catch (error) {
            console.error('Erreur lors de la vérification des nouveaux terrains:', error);
        }
    }
    
    /**
     * Vérifier les modifications des terrains existants
     */
    async verifierModificationsTerrains() {
        try {
            const container = document.querySelector(this.containerSelector);
            if (!container) return;
            
            // Récupérer tous les terrains affichés
            // Pour home page: chercher .col-* à l'intérieur du container
            // Pour dashboard/gestion: chercher .activity-item
            // Pour admin demandes: chercher .gestionnaire-card
            const terrainElements = container.querySelectorAll('.activity-item, [class*="col-"], .gestionnaire-card');
            
            for (const element of terrainElements) {
                // Méthode 1: Extraire l'ID depuis data-terrain-id
                let terrainId = element.getAttribute('data-terrain-id');
                
                // Méthode 2: Extraire l'ID depuis le bouton modifier (pour gestion_terrains)
                if (!terrainId) {
                    const editButton = element.querySelector('button[onclick*="openEditModal"]');
                    if (editButton) {
                        const onclickAttr = editButton.getAttribute('onclick');
                        const match = onclickAttr.match(/"id_terrain":(\d+)/);
                        if (match) {
                            terrainId = match[1];
                        }
                    }
                }
                
                if (!terrainId) continue;
                
                // Récupérer les données actuelles du terrain depuis le serveur
                const response = await fetch(this.baseUrl + this.getEndpoint + '/' + terrainId, {
                    credentials: 'same-origin' // Include session cookies for authentication
                });
                const result = await response.json();
                
                if (result.success && result.terrain) {
                    const currentSnapshot = this.creerSnapshotTerrain(result.terrain);
                    const oldSnapshot = this.terrainSnapshots[terrainId];
                    
                    // Si le snapshot a changé, mettre à jour le DOM
                    if (oldSnapshot && oldSnapshot !== currentSnapshot) {
                        this.mettreAJourTerrainDansDOM(result.terrain, element);
                        
                        // Ne pas afficher de notification si c'est l'utilisateur qui vient de modifier
                        if (!this.recentlyUpdatedTerrains.has(terrainId)) {
                            this.afficherNotification('Un terrain a été modifié !');
                        }
                        
                        if (this.onTerrainUpdated) {
                            this.onTerrainUpdated(result.terrain);
                        }
                    }
                    
                    // Mettre à jour le snapshot
                    this.terrainSnapshots[terrainId] = currentSnapshot;
                    
                    // Retirer de la liste des terrains récemment mis à jour
                    this.recentlyUpdatedTerrains.delete(terrainId);
                } else if (!result.success || result.removed) {
                    // Le terrain n'existe plus ou n'est plus disponible, le retirer du DOM
                    element.remove();
                    delete this.terrainSnapshots[terrainId];
                    this.afficherNotification('Un terrain a été supprimé');
                }
            }
        } catch (error) {
            console.error('Erreur lors de la vérification des modifications:', error);
        }
    }
    
    /**
     * Créer un snapshot (signature) d'un terrain pour détecter les changements
     */
    creerSnapshotTerrain(terrain) {
        return `${terrain.nom_terrain}|${terrain.localisation}|${terrain.type_terrain}|${terrain.format_terrain}|${terrain.prix_heure}|${terrain.statut}|${terrain.image || ''}|${terrain.justificatif || ''}`;
    }
    
    /**
     * Mettre à jour un terrain dans le DOM
     */
    mettreAJourTerrainDansDOM(terrain, oldElement) {
        const newElement = this.renderFunction(terrain);
        if (newElement) {
            oldElement.replaceWith(newElement);
        }
    }

    /**
     * Récupérer et afficher un nouveau terrain spécifique
     */
    async recupererEtAfficherNouveauTerrain(terrainId) {
        try {
            const response = await fetch(this.baseUrl + this.getEndpoint + '/' + terrainId, {
                credentials: 'same-origin' // Include session cookies for authentication
            });
            const data = await response.json();
            
            if (data.success && data.terrain) {
                this.ajouterTerrainAuDOM(data.terrain);
                
                if (this.onNewTerrain) {
                    this.onNewTerrain(data.terrain);
                }
                
                this.afficherNotification(this.onNewTerrainNotification);
            }
        } catch (error) {
            console.error('Erreur lors de la récupération du nouveau terrain:', error);
        }
    }

    /**
     * Ajouter un terrain au DOM en utilisant la fonction de rendu fournie
     */
    ajouterTerrainAuDOM(terrain) {
        const container = document.querySelector(this.containerSelector);
        if (!container) return;

        // Supprimer le message "aucune donnée" s'il existe
        const noData = container.querySelector('.no-data, .alert-info');
        if (noData) {
            noData.remove();
        }

        if (this.renderFunction) {
            const element = this.renderFunction(terrain);
            if (element) {
                container.insertBefore(element, container.firstChild);
                
                // Stocker le snapshot du nouveau terrain
                this.terrainSnapshots[terrain.id_terrain] = this.creerSnapshotTerrain(terrain);
            }
        }
    }

    /**
     * Afficher une notification
     */
    afficherNotification(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success terrain-notification';
        alertDiv.style.cssText = 'padding: 15px; margin: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; display: flex; align-items: center; gap: 10px; position: fixed; top: 80px; right: 20px; z-index: 9999; animation: slideIn 0.3s ease-out;';
        alertDiv.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${this.echapperHtml(message)}</span>
        `;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => alertDiv.remove(), 300);
        }, 3000);
    }

    /**
     * Échapper le HTML pour prévenir les attaques XSS
     */
    echapperHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Mettre à jour l'ID du dernier terrain (utile après l'ajout d'un nouveau terrain)
     */
    mettreAJourDernierId(newId) {
        if (newId > this.lastTerrainId) {
            this.lastTerrainId = newId;
        }
    }
    
    /**
     * Marquer un terrain comme récemment mis à jour par l'utilisateur
     * Pour éviter la double notification
     */
    marquerTerrainMisAJour(terrainId) {
        this.recentlyUpdatedTerrains.add(String(terrainId));
        
        // Retirer automatiquement après 5 secondes
        setTimeout(() => {
            this.recentlyUpdatedTerrains.delete(String(terrainId));
        }, 5000);
    }
}

// Exporter pour utilisation dans d'autres scripts
if (typeof window !== 'undefined') {
    window.TerrainRealtimeMonitor = TerrainRealtimeMonitor;
    
    // Créer une instance globale pour une utilisation facile
    window.terrainMonitor = new TerrainRealtimeMonitor({
        baseUrl: (typeof BASE_URL !== 'undefined' ? BASE_URL + 'index.php?url=' : window.location.origin + '/index.php?url='),
        checkEndpoint: 'terrain/checkNewTerrains',
        getEndpoint: 'terrain/getAvailableTerrainById',
        containerSelector: '.activities-list',
        renderFunction: window.renderGestionTerrain || null,
        pollingInterval: 5000
    });
    
    // Only start polling if user is authenticated
    if (window.userAuthenticated) {
        // Initialize and start polling when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const terrainIds = window.terrainIds || [];
            const maxId = terrainIds.length > 0 ? Math.max(...terrainIds) : 0;
            window.terrainMonitor.init(maxId);
            window.terrainMonitor.demarrerPolling();
        });
    }
}
