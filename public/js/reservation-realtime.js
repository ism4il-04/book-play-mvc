/**
 * Module de Surveillance en Temps Réel des Réservations (Réutilisable)
 * Gère les mises à jour automatiques lorsque de nouvelles réservations sont ajoutées ou modifiées
 */

class ReservationRealtimeMonitor {
    constructor(config) {
        this.baseUrl = config.baseUrl;
        this.checkEndpoint = config.checkEndpoint || 'reservations/checkNewReservations';
        this.getEndpoint = config.getEndpoint || 'reservations/getReservationById';
        this.updateEndpoint = config.updateEndpoint || 'reservations/updateStatus';
        this.containerSelector = config.containerSelector || 'tbody';
        this.renderFunction = config.renderFunction;
        this.pollingInterval = config.pollingInterval || 1000;
        this.onNewReservation = config.onNewReservation || null;
        this.onReservationUpdated = config.onReservationUpdated || null;
        this.filterStatus = config.filterStatus || null;
        
        this.lastReservationId = 0;
        this.reservationSnapshots = {}; // Stocker les snapshots des réservations pour détecter les changements
        this.isInitialized = false;
        this.pollingTimer = null;
        this.recentlyUpdatedReservations = new Set(); // Réservations récemment mises à jour par l'utilisateur
    }

    /**
     * Initialiser le moniteur avec l'ID de la dernière réservation actuelle
     */
    init(initialLastId = 0) {
        this.lastReservationId = initialLastId;
        this.isInitialized = false; // Will be set to true after first poll
        this.initialiserSnapshots();
        this.demarrerPolling();
    }
    
    /**
     * Initialiser les snapshots des réservations existantes au chargement
     */
    initialiserSnapshots() {
        const container = document.querySelector(this.containerSelector);
        if (!container) return;
        
        // Chercher les éléments de réservation (tr avec data-reservation-id)
        const reservationElements = container.querySelectorAll('tr[data-reservation-id]');
        
        for (const element of reservationElements) {
            const reservationId = element.getAttribute('data-reservation-id');
            if (reservationId) {
                // Créer un snapshot vide pour l'instant (sera mis à jour lors du premier polling)
                this.reservationSnapshots[reservationId] = '';
            }
        }
    }

    /**
     * Démarrer le polling pour les nouvelles réservations
     */
    demarrerPolling() {
        if (this.pollingTimer) {
            clearInterval(this.pollingTimer);
        }
        this.pollingTimer = setInterval(() => this.verifierNouvellesReservations(), this.pollingInterval);
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
     * Vérifier les nouvelles réservations et les modifications
     */
    async verifierNouvellesReservations() {
        try {
            const url = this.baseUrl + this.checkEndpoint + (this.filterStatus ? '?status=' + encodeURIComponent(this.filterStatus) : '');
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.lastId !== undefined) {
                if (this.isInitialized && data.lastId > this.lastReservationId) {
                    await this.recupererEtAfficherNouvelleReservation(data.lastId);
                }
                this.lastReservationId = data.lastId;
            }
            
            // Vérifier les modifications des réservations existantes
            if (this.isInitialized) {
                await this.verifierModificationsReservations();
            } else {
                this.isInitialized = true;
            }
        } catch (error) {
            console.error('Erreur lors de la vérification des nouvelles réservations:', error);
        }
    }
    
    /**
     * Vérifier les modifications des réservations existantes
     */
    async verifierModificationsReservations() {
        try {
            const container = document.querySelector(this.containerSelector);
            if (!container) return;
            
            // Récupérer toutes les réservations affichées
            const reservationElements = container.querySelectorAll('tr[data-reservation-id]');
            
            for (const element of reservationElements) {
                const reservationId = element.getAttribute('data-reservation-id');
                if (!reservationId) continue;
                
                // Récupérer les données actuelles de la réservation depuis le serveur
                const response = await fetch(this.baseUrl + this.getEndpoint + '/' + reservationId);
                const result = await response.json();
                
                if (result.success && result.reservation) {
                    const currentSnapshot = this.creerSnapshotReservation(result.reservation);
                    const oldSnapshot = this.reservationSnapshots[reservationId];
                    
                    // Si le snapshot a changé, mettre à jour le DOM
                    if (oldSnapshot && oldSnapshot !== currentSnapshot) {
                        this.mettreAJourReservationDansDOM(result.reservation, element);
                        
                        // Ne pas afficher de notification si c'est l'utilisateur qui vient de modifier
                        if (!this.recentlyUpdatedReservations.has(reservationId)) {
                            this.afficherNotification('Une réservation a été modifiée !');
                        }
                        
                        if (this.onReservationUpdated) {
                            this.onReservationUpdated(result.reservation);
                        }
                    }
                    
                    // Mettre à jour le snapshot
                    this.reservationSnapshots[reservationId] = currentSnapshot;
                    
                    // Retirer de la liste des réservations récemment mises à jour
                    this.recentlyUpdatedReservations.delete(reservationId);
                } else if (!result.success || result.removed) {
                    // La réservation n'existe plus ou n'est plus disponible, la retirer du DOM
                    element.remove();
                    delete this.reservationSnapshots[reservationId];
                    this.afficherNotification('Une réservation a été supprimée');
                }
            }
        } catch (error) {
            console.error('Erreur lors de la vérification des modifications:', error);
        }
    }
    
    /**
     * Créer un snapshot (signature) d'une réservation pour détecter les changements
     */
    creerSnapshotReservation(reservation) {
        return `${reservation.id_reservation}|${reservation.status}|${reservation.date_reservation}|${reservation.creneau}|${reservation.nom_terrain || ''}|${reservation.commentaire || ''}`;
    }
    
    /**
     * Mettre à jour une réservation dans le DOM
     */
    mettreAJourReservationDansDOM(reservation, oldElement) {
        if (this.renderFunction) {
            const newElement = this.renderFunction(reservation);
            if (newElement) {
                oldElement.replaceWith(newElement);
            }
        } else {
            // Mise à jour manuelle si pas de fonction de rendu
            this.mettreAJourElementReservation(reservation, oldElement);
        }
    }
    
    /**
     * Mettre à jour manuellement un élément de réservation
     */
    mettreAJourElementReservation(reservation, element) {
        // Mettre à jour le commentaire
        const commentCell = element.querySelector('.comment-cell');
        if (commentCell) {
            const comment = reservation.commentaire || '';
            if (comment) {
                commentCell.innerHTML = `<span title="${this.echapperHtml(comment)}" style="display: inline-block; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${this.echapperHtml(comment)}</span>`;
            } else {
                commentCell.innerHTML = '<span style="color:#95a5a6;">Aucun commentaire</span>';
            }
        }
        
        // Mettre à jour le statut
        const statusCell = element.querySelector('.status-cell');
        if (statusCell) {
            const status = reservation.status || 'en attente';
            const color = this.getStatusColor(status);
            statusCell.innerHTML = `<span class="status-badge" style="background-color: ${color}; color:#fff; padding:6px 10px; border-radius:12px; font-size:12px;">${this.echapperHtml(this.capitalizeFirst(status))}</span>`;
        }
        
        // Mettre à jour les actions
        const actionsCell = element.querySelector('.actions-cell');
        if (actionsCell) {
            const status = reservation.status || 'en attente';
            if (status === 'en attente') {
                actionsCell.innerHTML = `
                    <button class="btn btn-success btn-sm" onclick="reservationMonitor.updateStatus(${reservation.id_reservation}, 'accepté')" style="margin-right:8px;">
                        <i class="fas fa-check"></i> Accepter
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="reservationMonitor.updateStatus(${reservation.id_reservation}, 'refusé')">
                        <i class="fas fa-times"></i> Refuser
                    </button>
                `;
            } else {
                actionsCell.innerHTML = '<span style="color:#95a5a6; font-size:12px;">Aucune action</span>';
            }
        }
    }
    
    /**
     * Obtenir la couleur du statut
     */
    getStatusColor(status) {
        const colors = {
            'en attente': '#f39c12',
            'accepté': '#27ae60',
            'refusé': '#e74c3c',
            'annulé': '#e74c3c'
        };
        return colors[status] || '#999';
    }
    
    /**
     * Capitaliser la première lettre
     */
    capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    /**
     * Récupérer et afficher une nouvelle réservation spécifique
     */
    async recupererEtAfficherNouvelleReservation(reservationId) {
        try {
            const response = await fetch(this.baseUrl + this.getEndpoint + '/' + reservationId);
            const data = await response.json();
            
            if (data.success && data.reservation) {
                this.ajouterReservationAuDOM(data.reservation);
                
                if (this.onNewReservation) {
                    this.onNewReservation(data.reservation);
                }
                
                this.afficherNotification('Nouvelle réservation reçue !');
            }
        } catch (error) {
            console.error('Erreur lors de la récupération de la nouvelle réservation:', error);
        }
    }

    /**
     * Ajouter une réservation au DOM en utilisant la fonction de rendu fournie
     */
    ajouterReservationAuDOM(reservation) {
        const container = document.querySelector(this.containerSelector);
        if (!container) return;

        // Supprimer le message "aucune donnée" s'il existe
        const noData = container.closest('.table-responsive')?.querySelector('.no-data');
        if (noData) {
            noData.remove();
        }

        if (this.renderFunction) {
            const element = this.renderFunction(reservation);
            if (element) {
                container.insertBefore(element, container.firstChild);
                
                // Stocker le snapshot de la nouvelle réservation
                this.reservationSnapshots[reservation.id_reservation] = this.creerSnapshotReservation(reservation);
            }
        }
    }

    /**
     * Mettre à jour le statut d'une réservation via AJAX
     */
    async updateStatus(reservationId, newStatus) {
        try {
            const response = await fetch(this.baseUrl + this.updateEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: reservationId,
                    status: newStatus
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.afficherNotification(data.message || `Réservation ${newStatus} avec succès`);
                
                // Marquer comme récemment mise à jour pour éviter la double notification
                this.marquerReservationMisAJour(reservationId);
                
                // Forcer la mise à jour immédiate
                await this.verifierModificationsReservations();
            } else {
                this.afficherNotification('Erreur: ' + (data.message || 'Action échouée'), 'error');
            }
        } catch (error) {
            console.error('Erreur lors de la mise à jour du statut:', error);
            this.afficherNotification('Erreur lors de la mise à jour', 'error');
        }
    }

    /**
     * Afficher une notification
     */
    afficherNotification(message, type = 'success') {
        const alertDiv = document.createElement('div');
        const bgColor = type === 'error' ? '#f8d7da' : '#d4edda';
        const textColor = type === 'error' ? '#721c24' : '#155724';
        const borderColor = type === 'error' ? '#f5c6cb' : '#c3e6cb';
        const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
        
        alertDiv.className = 'alert alert-' + type + ' reservation-notification';
        alertDiv.style.cssText = `padding: 15px; margin: 20px; background-color: ${bgColor}; color: ${textColor}; border: 1px solid ${borderColor}; border-radius: 8px; display: flex; align-items: center; gap: 10px; position: fixed; top: 80px; right: 20px; z-index: 9999; animation: slideIn 0.3s ease-out;`;
        alertDiv.innerHTML = `
            <i class="fas ${icon}"></i>
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
     * Mettre à jour l'ID de la dernière réservation (utile après l'ajout d'une nouvelle réservation)
     */
    mettreAJourDernierId(newId) {
        if (newId > this.lastReservationId) {
            this.lastReservationId = newId;
        }
    }
    
    /**
     * Marquer une réservation comme récemment mise à jour par l'utilisateur
     * Pour éviter la double notification
     */
    marquerReservationMisAJour(reservationId) {
        this.recentlyUpdatedReservations.add(String(reservationId));
        
        // Retirer automatiquement après 5 secondes
        setTimeout(() => {
            this.recentlyUpdatedReservations.delete(String(reservationId));
        }, 5000);
    }
    
    /**
     * Mettre à jour le filtre de statut
     */
    setFilterStatus(status) {
        this.filterStatus = status;
        // Réinitialiser pour recharger avec le nouveau filtre
        this.arreterPolling();
        this.reservationSnapshots = {};
        this.initialiserSnapshots();
        this.demarrerPolling();
    }
}

// Exporter pour utilisation dans d'autres scripts
if (typeof window !== 'undefined') {
    window.ReservationRealtimeMonitor = ReservationRealtimeMonitor;
}

