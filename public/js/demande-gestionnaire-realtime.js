/**
 * Module de Surveillance en Temps Réel des Demandes de Gestionnaire
 * Adapté du système gestionnaire-realtime.js pour les nouvelles demandes
 */

class DemandeGestionnaireRealtimeMonitor {
    constructor(config) {
        this.baseUrl = config.baseUrl;
        this.checkEndpoint = config.checkEndpoint || 'gestion_gestionnaire/checkNewDemandes';
        this.getEndpoint = config.getEndpoint || 'gestion_gestionnaire/getGestionnaireById';
        this.containerSelector = config.containerSelector || '#grid-en_attente';
        this.renderFunction = config.renderFunction;
        this.pollingInterval = config.pollingInterval || 5000;
        this.onNewDemande = config.onNewDemande || null;
        this.onDemandeUpdated = config.onDemandeUpdated || null;
        
        this.lastDemandeCount = 0;
        this.demandeSnapshots = {}; // Stocker les snapshots des demandes pour détecter les changements
        this.isInitialized = false;
        this.pollingTimer = null;
        this.recentlyUpdatedDemandes = new Set(); // Demandes récemment mises à jour par l'utilisateur
    }

    /**
     * Initialiser le moniteur avec le nombre actuel de demandes
     */
    init(initialCount = 0) {
        this.lastDemandeCount = initialCount;
        this.isInitialized = false; // Will be set to true after first poll
        this.initialiserSnapshots();
        this.demarrerPolling();
    }
    
    /**
     * Initialiser les snapshots des demandes existantes au chargement
     */
    initialiserSnapshots() {
        const container = document.querySelector(this.containerSelector);
        if (!container) return;
        
        // Chercher les éléments de demande (div.gestionnaire-card)
        const demandeElements = container.querySelectorAll('.gestionnaire-card');
        
        for (const element of demandeElements) {
            // Extraire l'ID depuis les boutons d'action
            const acceptButton = element.querySelector('button[onclick*="acceptGestionnaire"]');
            if (acceptButton) {
                const onclickAttr = acceptButton.getAttribute('onclick');
                const match = onclickAttr.match(/acceptGestionnaire\((\d+)/);
                if (match) {
                    const demandeId = match[1];
                    // Créer un snapshot vide pour l'instant (sera mis à jour lors du premier polling)
                    this.demandeSnapshots[demandeId] = '';
                }
            }
        }
    }

    /**
     * Démarrer le polling pour les nouvelles demandes
     */
    demarrerPolling() {
        if (this.pollingTimer) {
            clearInterval(this.pollingTimer);
        }
        this.pollingTimer = setInterval(() => this.verifierNouvellesDemandes(), this.pollingInterval);
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
     * Vérifier les nouvelles demandes et les modifications
     */
    async verifierNouvellesDemandes() {
        try {
            const response = await fetch(this.baseUrl + this.checkEndpoint);
            const data = await response.json();
            
            console.log('Réponse de checkNewDemandes:', data);
            
            if (data.success && data.newDemandes) {
                console.log('Nouvelles demandes reçues:', data.newDemandes.length);
                
                // Récupérer les IDs des demandes actuellement affichées
                const currentlyDisplayed = new Set();
                const displayedCards = document.querySelectorAll(`${this.containerSelector} .gestionnaire-card`);
                displayedCards.forEach(card => {
                    const acceptButton = card.querySelector('button[onclick*="acceptGestionnaire"]');
                    if (acceptButton) {
                        const onclickAttr = acceptButton.getAttribute('onclick');
                        const match = onclickAttr.match(/acceptGestionnaire\((\d+)/);
                        if (match) {
                            currentlyDisplayed.add(match[1]);
                        }
                    }
                });
                
                console.log('Demandes actuellement affichées:', Array.from(currentlyDisplayed));
                
                // Vérifier les nouvelles demandes
                for (const demande of data.newDemandes) {
                    const demandeId = demande.id.toString();
                    
                    // Si cette demande n'est pas encore affichée
                    if (!currentlyDisplayed.has(demandeId) && this.isInitialized) {
                        console.log('Nouvelle demande détectée:', demandeId);
                        
                        // L'ajouter au DOM
                        this.ajouterDemandeAuDOM(demande);
                        
                        // Mettre à jour le compteur
                        this.mettreAJourCompteur(1);
                        
                        if (this.onNewDemande) {
                            this.onNewDemande(demande);
                        }
                        
                        // Afficher une notification
                        this.afficherNotification('Nouvelle demande de gestionnaire reçue !');
                        
                    } else if (currentlyDisplayed.has(demandeId)) {
                        // Demande déjà affichée, vérifier les changements
                        const currentSnapshot = this.creerSnapshotDemande(demande);
                        const oldSnapshot = this.demandeSnapshots[demandeId];
                        
                        if (oldSnapshot && oldSnapshot !== currentSnapshot) {
                            console.log('Demande modifiée:', demandeId);
                            const existingCard = this.trouverCarteParId(demandeId);
                            if (existingCard) {
                                this.mettreAJourDemandeDansDOM(demande, existingCard);
                                
                                if (this.onDemandeUpdated) {
                                    this.onDemandeUpdated(demande);
                                }
                            }
                        }
                        
                        // Mettre à jour le snapshot
                        this.demandeSnapshots[demandeId] = currentSnapshot;
                    }
                }
                
                // Mettre à jour le nombre total de demandes
                this.lastDemandeCount = data.newDemandes.length;
            }
            
            // Marquer comme initialisé après le premier poll
            if (!this.isInitialized) {
                this.isInitialized = true;
            }
        } catch (error) {
            console.error('Erreur lors de la vérification des nouvelles demandes:', error);
        }
    }
    
    /**
     * Créer un snapshot (signature) d'une demande pour détecter les changements
     */
    creerSnapshotDemande(demande) {
        return `${demande.nom || ''}|${demande.prenom || ''}|${demande.email || ''}|${demande.num_tel || ''}|${demande.nom_terrain || ''}|${demande.date_demande || ''}`;
    }
    
    /**
     * Mettre à jour une demande dans le DOM
     */
    mettreAJourDemandeDansDOM(demande, oldElement) {
        const newElement = this.renderFunction(demande);
        if (newElement) {
            oldElement.replaceWith(newElement);
        }
    }

    /**
     * Ajouter une demande au DOM en utilisant la fonction de rendu fournie
     */
    ajouterDemandeAuDOM(demande) {
        const container = document.querySelector(this.containerSelector);
        if (!container) return;

        // Vérifier si la demande n'existe pas déjà
        const demandeId = demande.id;
        const existingCard = this.trouverCarteParId(demandeId);
        if (existingCard) {
            console.log('Demande déjà présente, mise à jour:', demandeId);
            return; // Ne pas ajouter si déjà présent
        }

        // Supprimer le message "aucune demande" s'il existe
        const noData = container.querySelector('.alert.alert-info');
        if (noData && noData.textContent.includes('Aucun')) {
            noData.remove();
        }

        if (this.renderFunction) {
            const element = this.renderFunction(demande);
            if (element) {
                // Ajouter en haut de la liste avec animation
                element.style.opacity = '0';
                element.style.transform = 'scale(0.95)';
                container.insertBefore(element, container.firstChild);
                
                // Animation d'entrée
                setTimeout(() => {
                    element.style.transition = 'all 0.3s ease-in';
                    element.style.opacity = '1';
                    element.style.transform = 'scale(1)';
                }, 50);
                
                // Stocker le snapshot de la nouvelle demande
                this.demandeSnapshots[demandeId] = this.creerSnapshotDemande(demande);
                
                console.log('Nouvelle demande ajoutée au DOM:', demandeId);
            }
        }
    }

    /**
     * Trouver une carte de demande par son ID
     */
    trouverCarteParId(demandeId) {
        const container = document.querySelector(this.containerSelector);
        if (!container) return null;
        
        const cards = container.querySelectorAll('.gestionnaire-card');
        for (const card of cards) {
            const acceptButton = card.querySelector('button[onclick*="acceptGestionnaire"]');
            if (acceptButton) {
                const onclickAttr = acceptButton.getAttribute('onclick');
                const match = onclickAttr.match(/acceptGestionnaire\((\d+)/);
                if (match && match[1] === demandeId.toString()) {
                    return card;
                }
            }
        }
        return null;
    }

    /**
     * Afficher une notification
     */
    afficherNotification(message) {
        // Créer un élément de notification
        const notification = document.createElement('div');
        notification.className = 'alert alert-info fade show position-fixed demande-notification';
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease-out;
        `;
        
        notification.innerHTML = `
            <i class="bi bi-bell-fill me-2"></i>
            ${this.echapperHtml(message)}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()" aria-label="Close"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Supprimer automatiquement après 5 secondes
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 5000);
    }

    /**
     * Mettre à jour le compteur des demandes en attente
     */
    mettreAJourCompteur(increment) {
        const counter = document.querySelector('.status-tab[data-status="en_attente"] .count-badge');
        if (counter) {
            const currentCount = parseInt(counter.textContent) || 0;
            counter.textContent = Math.max(0, currentCount + increment);
        }
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
     * Marquer une demande comme récemment mise à jour par l'utilisateur
     * Pour éviter la double notification
     */
    marquerDemandeMiseAJour(demandeId) {
        this.recentlyUpdatedDemandes.add(String(demandeId));
        
        // Retirer automatiquement après 5 secondes
        setTimeout(() => {
            this.recentlyUpdatedDemandes.delete(String(demandeId));
        }, 5000);
    }
}

/**
 * Fonction pour rendre une carte de demande de gestionnaire
 */
function renderDemandeGestionnaireCard(demande) {
    const card = document.createElement('div');
    card.className = 'gestionnaire-card';
    
    // Générer les initiales
    const initiales = (demande.prenom?.charAt(0) || '') + (demande.nom?.charAt(0) || '');
    
    // Couleur d'avatar par défaut pour les nouvelles demandes
    const avatarColor = '#17a2b8';
    
    card.innerHTML = `
        <div class="card-avatar">
            <div class="avatar-circle" style="background-color: ${avatarColor}">
                ${initiales.toUpperCase()}
            </div>
        </div>
        <div class="card-content">
            <h3 class="gestionnaire-name">${escapeHtml(demande.prenom + ' ' + demande.nom)}</h3>
            <div class="info-item">
                <i class="bi bi-envelope"></i>
                <span>${escapeHtml(demande.email || '')}</span>
            </div>
            <div class="info-item">
                <i class="bi bi-telephone"></i>
                <span>${escapeHtml(demande.num_tel || 'N/A')}</span>
            </div>
            <div class="info-item">
                <i class="bi bi-buildings"></i>
                <span>${escapeHtml(demande.nom_terrain || 'N/A')}</span>
            </div>
            <div class="info-item">
                <i class="bi bi-calendar"></i>
                <span>Demande le: ${escapeHtml(demande.date_demande || '')}</span>
            </div>
        </div>
        <div class="card-actions">
            <button class="btn btn-accept" onclick="acceptGestionnaire(${demande.id}, ${demande.id_terrain || 'null'})">
                <i class="bi bi-check"></i> Accepter
            </button>
            <button class="btn btn-reject" onclick="rejectGestionnaire(${demande.id}, ${demande.id_terrain || 'null'})">
                <i class="bi bi-x"></i> Refuser
            </button>
        </div>
        <button class="btn btn-details" onclick="voirDetails(${demande.id})">
            <i class="bi bi-eye"></i> Voir détails
        </button>
    `;
    
    return card;
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
    
    .demande-notification {
        font-weight: 500;
        min-width: 300px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
`;
document.head.appendChild(style);

// Exporter pour utilisation dans d'autres scripts
if (typeof window !== 'undefined') {
    window.DemandeGestionnaireRealtimeMonitor = DemandeGestionnaireRealtimeMonitor;
    window.renderDemandeGestionnaireCard = renderDemandeGestionnaireCard;
    
    // Créer une instance globale pour une utilisation facile
    window.demandeGestionnaireMonitor = new DemandeGestionnaireRealtimeMonitor({
        baseUrl: (typeof BASE_URL !== 'undefined' ? BASE_URL + 'index.php?url=' : window.location.origin + '/index.php?url='),
        checkEndpoint: 'gestion_gestionnaire/checkNewDemandes',
        getEndpoint: 'gestion_gestionnaire/getGestionnaireById',
        containerSelector: '#grid-en_attente',
        renderFunction: window.renderDemandeGestionnaireCard || null,
        pollingInterval: 5000
    });
    
    // Only start polling if user is authenticated as admin
    if (window.userAuthenticated && window.userRole === 'administrateur') {
        // Initialize and start polling when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Compter les demandes actuelles
            const currentDemandes = document.querySelectorAll('#grid-en_attente .gestionnaire-card').length;
            window.demandeGestionnaireMonitor.init(currentDemandes);
            window.demandeGestionnaireMonitor.demarrerPolling();
        });
    }
}
