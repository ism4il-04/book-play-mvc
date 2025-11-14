/**
 * Module de Surveillance en Temps Réel des Gestionnaires Acceptés (Réutilisable)
 * Adapté du système terrain-realtime.js pour les gestionnaires
 */

class GestionnaireRealtimeMonitor {
    constructor(config) {
        this.baseUrl = config.baseUrl;
        this.checkEndpoint = config.checkEndpoint || 'gestion_gestionnaire/checkNewGestionnaires';
        this.getEndpoint = config.getEndpoint || 'gestion_gestionnaire/getGestionnaireById';
        this.containerSelector = config.containerSelector || '.gestionnaires-list';
        this.renderFunction = config.renderFunction;
        this.pollingInterval = config.pollingInterval || 3000;
        this.onNewGestionnaire = config.onNewGestionnaire || null;
        this.onGestionnaireUpdated = config.onGestionnaireUpdated || null;
        
        this.lastGestionnaireId = 0;
        this.gestionnaireSnapshots = {}; // Stocker les snapshots des gestionnaires pour détecter les changements
        this.isInitialized = false;
        this.pollingTimer = null;
        this.recentlyUpdatedGestionnaires = new Set(); // Gestionnaires récemment mis à jour par l'utilisateur
    }

    /**
     * Initialiser le moniteur avec l'ID du dernier gestionnaire actuel
     */
    init(initialLastId = 0) {
        this.lastGestionnaireId = initialLastId;
        this.isInitialized = false; // Will be set to true after first poll
        this.initialiserSnapshots();
        this.demarrerPolling();
    }
    
    /**
     * Initialiser les snapshots des gestionnaires existants au chargement
     */
    initialiserSnapshots() {
        const container = document.querySelector(this.containerSelector);
        if (!container) return;
        
        // Chercher les éléments de gestionnaire (tr pour dashboard)
        const gestionnaireElements = container.querySelectorAll('tr[data-gestionnaire-id]');
        
        for (const element of gestionnaireElements) {
            // Utiliser data-gestionnaire-id
            let gestionnaireId = element.getAttribute('data-gestionnaire-id');
            
            if (gestionnaireId) {
                // Créer un snapshot vide pour l'instant (sera mis à jour lors du premier polling)
                this.gestionnaireSnapshots[gestionnaireId] = '';
                continue;
            }
        }
    }

    /**
     * Démarrer le polling pour les nouveaux gestionnaires
     */
    demarrerPolling() {
        if (this.pollingTimer) {
            clearInterval(this.pollingTimer);
        }
        this.pollingTimer = setInterval(() => this.verifierNouveauxGestionnaires(), this.pollingInterval);
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
     * Vérifier les nouveaux gestionnaires et les modifications
     */
    async verifierNouveauxGestionnaires() {
        try {
            const response = await fetch(this.baseUrl + this.checkEndpoint);
            const data = await response.json();
            
            console.log('Réponse de checkNewGestionnaires:', data);
            
            if (data.success && data.recentlyAccepted) {
                console.log('Gestionnaires acceptés reçus:', data.recentlyAccepted.length);
                
                // Récupérer les IDs des gestionnaires actuellement affichés
                const currentlyDisplayed = new Set();
                const displayedRows = document.querySelectorAll(`${this.containerSelector} tr[data-gestionnaire-id]`);
                displayedRows.forEach(row => {
                    const id = row.getAttribute('data-gestionnaire-id');
                    if (id) currentlyDisplayed.add(id);
                });
                
                console.log('Gestionnaires actuellement affichés:', Array.from(currentlyDisplayed));
                
                // Vérifier les gestionnaires acceptés
                for (const gestionnaire of data.recentlyAccepted) {
                    const gestionnaireId = gestionnaire.id.toString();
                    
                    // Si ce gestionnaire accepté n'est pas encore affiché
                    if (!currentlyDisplayed.has(gestionnaireId) && this.isInitialized) {
                        console.log('Nouveau gestionnaire accepté détecté:', gestionnaireId);
                        
                        // L'ajouter au DOM
                        this.ajouterGestionnaireAuDOM(gestionnaire);
                        
                        if (this.onNewGestionnaire) {
                            this.onNewGestionnaire(gestionnaire);
                        }
                        
                        // Notification supprimée sur demande de l'utilisateur
                    } else if (currentlyDisplayed.has(gestionnaireId)) {
                        // Gestionnaire déjà affiché, vérifier les changements
                        const currentSnapshot = this.creerSnapshotGestionnaire(gestionnaire);
                        const oldSnapshot = this.gestionnaireSnapshots[gestionnaireId];
                        
                        if (oldSnapshot && oldSnapshot !== currentSnapshot) {
                            console.log('Gestionnaire modifié:', gestionnaireId);
                            const existingRow = document.querySelector(`tr[data-gestionnaire-id="${gestionnaireId}"]`);
                            if (existingRow) {
                                this.mettreAJourGestionnaireDansDOM(gestionnaire, existingRow);
                                
                                // Notification supprimée sur demande de l'utilisateur
                                
                                if (this.onGestionnaireUpdated) {
                                    this.onGestionnaireUpdated(gestionnaire);
                                }
                            }
                        }
                        
                        // Mettre à jour le snapshot
                        this.gestionnaireSnapshots[gestionnaireId] = currentSnapshot;
                    }
                }
            }
            
            // Vérifier les modifications des gestionnaires existants
            if (this.isInitialized) {
                await this.verifierModificationsGestionnaires();
            } else {
                this.isInitialized = true;
            }
        } catch (error) {
            console.error('Erreur lors de la vérification des nouveaux gestionnaires:', error);
        }
    }
    
    /**
     * Vérifier les modifications des gestionnaires existants
     */
    async verifierModificationsGestionnaires() {
        try {
            const container = document.querySelector(this.containerSelector);
            if (!container) return;
            
            // Récupérer tous les gestionnaires affichés
            const gestionnaireElements = container.querySelectorAll('tr[data-gestionnaire-id]');
            
            for (const element of gestionnaireElements) {
                // Extraire l'ID depuis data-gestionnaire-id
                let gestionnaireId = element.getAttribute('data-gestionnaire-id');
                
                if (!gestionnaireId) continue;
                
                // Récupérer les données actuelles du gestionnaire depuis le serveur
                const response = await fetch(this.baseUrl + this.getEndpoint + '/' + gestionnaireId);
                const result = await response.json();
                
                if (result.success && result.gestionnaire) {
                    const currentSnapshot = this.creerSnapshotGestionnaire(result.gestionnaire);
                    const oldSnapshot = this.gestionnaireSnapshots[gestionnaireId];
                    
                    // Si le snapshot a changé, mettre à jour le DOM
                    if (oldSnapshot && oldSnapshot !== currentSnapshot) {
                        this.mettreAJourGestionnaireDansDOM(result.gestionnaire, element);
                        
                        // Ne pas afficher de notification si c'est l'utilisateur qui vient de modifier
                        if (!this.recentlyUpdatedGestionnaires.has(gestionnaireId)) {
                            this.afficherNotification('Un gestionnaire a été modifié !');
                        }
                        
                        if (this.onGestionnaireUpdated) {
                            this.onGestionnaireUpdated(result.gestionnaire);
                        }
                    }
                    
                    // Mettre à jour le snapshot
                    this.gestionnaireSnapshots[gestionnaireId] = currentSnapshot;
                    
                    // Retirer de la liste des gestionnaires récemment mis à jour
                    this.recentlyUpdatedGestionnaires.delete(gestionnaireId);
                } else if (!result.success || result.removed) {
                    // Le gestionnaire n'existe plus ou n'est plus disponible, le retirer du DOM
                    element.remove();
                    delete this.gestionnaireSnapshots[gestionnaireId];
                    // Notification supprimée sur demande de l'utilisateur
                }
            }
        } catch (error) {
            console.error('Erreur lors de la vérification des modifications:', error);
        }
    }
    
    /**
     * Créer un snapshot (signature) d'un gestionnaire pour détecter les changements
     */
    creerSnapshotGestionnaire(gestionnaire) {
        const statut = gestionnaire.statut_gestionnaire || gestionnaire.status || '';
        return `${gestionnaire.nom || ''}|${gestionnaire.prenom || ''}|${gestionnaire.email || ''}|${gestionnaire.num_tel || ''}|${statut}`;
    }
    
    /**
     * Mettre à jour un gestionnaire dans le DOM
     */
    mettreAJourGestionnaireDansDOM(gestionnaire, oldElement) {
        const newElement = this.renderFunction(gestionnaire);
        if (newElement) {
            oldElement.replaceWith(newElement);
        }
    }


    /**
     * Ajouter un gestionnaire au DOM en utilisant la fonction de rendu fournie
     */
    ajouterGestionnaireAuDOM(gestionnaire) {
        const container = document.querySelector(this.containerSelector);
        if (!container) return;

        // Vérifier si le gestionnaire n'existe pas déjà
        const gestionnaireId = gestionnaire.id || gestionnaire.id_gestionnaire;
        const existingRow = container.querySelector(`tr[data-gestionnaire-id="${gestionnaireId}"]`);
        if (existingRow) {
            console.log('Gestionnaire déjà présent, mise à jour:', gestionnaireId);
            return; // Ne pas ajouter si déjà présent
        }

        // Supprimer le message "aucune donnée" s'il existe
        const noData = container.querySelector('td[colspan]');
        if (noData && noData.textContent.includes('Aucun')) {
            const row = noData.closest('tr');
            if (row) {
                row.remove();
            }
        }

        if (this.renderFunction) {
            const element = this.renderFunction(gestionnaire);
            if (element) {
                // Ajouter en haut de la liste
                container.insertBefore(element, container.firstChild);
                
                // Stocker le snapshot du nouveau gestionnaire
                this.gestionnaireSnapshots[gestionnaireId] = this.creerSnapshotGestionnaire(gestionnaire);
                
                console.log('Nouveau gestionnaire ajouté au DOM:', gestionnaireId);
            }
        }
    }

    // Fonction de notification supprimée sur demande de l'utilisateur

    /**
     * Échapper le HTML pour prévenir les attaques XSS
     */
    echapperHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Mettre à jour l'ID du dernier gestionnaire (utile après l'ajout d'un nouveau gestionnaire)
     */
    mettreAJourDernierId(newId) {
        if (newId > this.lastGestionnaireId) {
            this.lastGestionnaireId = newId;
        }
    }
    
    /**
     * Marquer un gestionnaire comme récemment mis à jour par l'utilisateur
     * Pour éviter la double notification
     */
    marquerGestionnaireMisAJour(gestionnaireId) {
        this.recentlyUpdatedGestionnaires.add(String(gestionnaireId));
        
        // Retirer automatiquement après 5 secondes
        setTimeout(() => {
            this.recentlyUpdatedGestionnaires.delete(String(gestionnaireId));
        }, 5000);
    }
}

/**
 * Fonction pour rendre une ligne de gestionnaire dans le dashboard
 */
function renderDashboardGestionnaireRow(gestionnaire) {
    const row = document.createElement('tr');
    row.setAttribute('data-gestionnaire-id', gestionnaire.id || gestionnaire.id_gestionnaire);
    
    // Déterminer le badge selon le statut
    let badgeClass, icon, label;
    
    // Essayer différents champs pour le statut
    const statut = (gestionnaire.statut_gestionnaire || gestionnaire.status || '').toLowerCase().trim();
    
    console.log('Statut du gestionnaire:', statut, 'Gestionnaire complet:', gestionnaire);
    
    switch (statut) {
        case 'accepté':
            badgeClass = 'bg-success';
            icon = 'bi-check-circle';
            label = 'Accepté';
            break;
        case 'en attente':
            badgeClass = 'bg-warning';
            icon = 'bi-clock';
            label = 'En attente';
            break;
        case 'refusé':
            badgeClass = 'bg-danger';
            icon = 'bi-x-circle';
            label = 'Refusé';
            break;
        default:
            badgeClass = 'bg-secondary';
            icon = 'bi-question-circle';
            label = statut || 'Non défini';
    }
    
    const nomComplet = `${gestionnaire.nom || ''} ${gestionnaire.prenom || ''}`.trim();
    
    row.innerHTML = `
        <td><strong>${escapeHtml(nomComplet)}</strong></td>
        <td>${escapeHtml(gestionnaire.email || '')}</td>
        <td>${escapeHtml(gestionnaire.num_tel || '')}</td>
        <td>
            <span class="badge ${badgeClass}">
                <i class="bi ${icon} me-1"></i>${label}
            </span>
        </td>
    `;
    
    return row;
}

/**
 * Fonction utilitaire pour échapper le HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Ajouter les animations CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .gestionnaire-notification {
        font-weight: 500;
        min-width: 300px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
`;
document.head.appendChild(style);

// Exporter pour utilisation dans d'autres scripts
if (typeof window !== 'undefined') {
    window.GestionnaireRealtimeMonitor = GestionnaireRealtimeMonitor;
    window.renderDashboardGestionnaireRow = renderDashboardGestionnaireRow;
    
    // Créer une instance globale pour une utilisation facile
    window.gestionnaireMonitor = new GestionnaireRealtimeMonitor({
        baseUrl: (typeof BASE_URL !== 'undefined' ? BASE_URL + 'index.php?url=' : window.location.origin + '/index.php?url='),
        checkEndpoint: 'gestion_gestionnaire/checkNewGestionnaires',
        getEndpoint: 'gestion_gestionnaire/getGestionnaireById',
        containerSelector: '#gestionnaires-tbody',
        renderFunction: window.renderDashboardGestionnaireRow || null,
        pollingInterval: 5000
    });
    
    // Only start polling if user is authenticated as admin
    if (window.userAuthenticated && window.userRole === 'administrateur') {
        // Initialize and start polling when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const gestionnaireIds = window.gestionnaireIds || [];
            const maxId = gestionnaireIds.length > 0 ? Math.max(...gestionnaireIds) : 0;
            window.gestionnaireMonitor.init(maxId);
            window.gestionnaireMonitor.demarrerPolling();
        });
    }
}
