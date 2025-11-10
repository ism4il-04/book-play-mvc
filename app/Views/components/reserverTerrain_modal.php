<!-- Modal de réservation -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header" style="background: white; border-bottom: 1px solid #e0e0e0; padding: 1.5rem;">
                <h5 class="modal-title" id="reservationModalLabel" style="color: #064420; font-weight: 700; font-size: 1.3rem;">
                    Formulaire de Réservation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 2rem; background: #f8f9fa;">
                <form id="reservationForm" method="POST" action="<?php echo BASE_URL; ?>reservation/create">
                    <input type="hidden" name="terrain_id" id="modal_terrain_id">
                    
                    <!-- Date de réservation -->
                    <div class="mb-4">
                        <label class="form-label reservation-modal-label">Date de réservation</label>
                        <input type="date" class="form-control reservation-modal-input" name="date_reservation" required>
                    </div>
                    
                    <!-- Heure de début et fin -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label reservation-modal-label">Heure de début</label>
                            <input type="time" class="form-control reservation-modal-input" name="heure_debut" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label reservation-modal-label">Heure de fin</label>
                            <input type="time" class="form-control reservation-modal-input" name="heure_fin" required>
                        </div>
                    </div>
                    
                    <!-- Options supplémentaires -->
                    <div class="mb-4">
                        <label class="form-label reservation-modal-label">Options supplémentaires</label>
                        <div class="options-list-reservation">
                            <div class="option-item-reservation">
                                <input type="checkbox" id="ballon" name="options[]" value="ballon" data-price="20">
                                <label for="ballon">Ballon</label>
                                <span class="option-price-reservation">+20 MAD</span>
                            </div>
                            <div class="option-item-reservation">
                                <input type="checkbox" id="maillots" name="options[]" value="maillots" data-price="50">
                                <label for="maillots">Maillots</label>
                                <span class="option-price-reservation">+50 MAD</span>
                            </div>
                            <div class="option-item-reservation">
                                <input type="checkbox" id="douche" name="options[]" value="douche" data-price="30">
                                <label for="douche">Douche</label>
                                <span class="option-price-reservation">+30 MAD</span>
                            </div>
                            <div class="option-item-reservation">
                                <input type="checkbox" id="bouteille" name="options[]" value="bouteille_eau" data-price="10">
                                <label for="bouteille">Bouteille d'eau</label>
                                <span class="option-price-reservation">+10 MAD</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Commentaire -->
                    <div class="mb-4">
                        <label class="form-label reservation-modal-label">Commentaire et demandes spécifiques</label>
                        <textarea class="form-control reservation-modal-input" name="commentaire" rows="4" 
                                  placeholder="Ajoutez vos commentaires ou demandes spécifiques..."
                                  style="resize: none;"></textarea>
                    </div>
                    
                    <!-- Informations du client -->
                    <div class="mb-4">
                        <h6 style="color: #666; font-weight: 600; margin-bottom: 1rem;">Informations du client</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label reservation-modal-label-light">Nom</label>
                                <input type="text" class="form-control reservation-modal-input" name="nom" 
                                       value="<?php echo htmlspecialchars($currentUser['nom'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label reservation-modal-label-light">Prénom</label>
                                <input type="text" class="form-control reservation-modal-input" name="prenom" 
                                       value="<?php echo htmlspecialchars($currentUser['prenom'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label reservation-modal-label-light">Email</label>
                                <input type="email" class="form-control reservation-modal-input" name="email" 
                                       value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label reservation-modal-label-light">Téléphone</label>
                                <input type="tel" class="form-control reservation-modal-input" name="telephone" 
                                       value="<?php echo htmlspecialchars($currentUser['telephone'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Prix total -->
                    <div class="mb-4" id="prixTotalSection" style="display: none;">
                        <div class="alert alert-info d-flex justify-content-between align-items-center" 
                             style="background: #e8f5e9; border: 1px solid #4caf50; border-radius: 10px;">
                            <span style="font-weight: 600; color: #2e7d32;">Prix total estimé:</span>
                            <span style="font-size: 1.2rem; font-weight: 700; color: #1b5e20;" id="prixTotalDisplay">0 MAD</span>
                        </div>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="d-flex gap-3">
                        <button type="button" class="btn-reservation-modal-cancel" data-bs-dismiss="modal">
                            Annuler
                        </button>
                        <button type="submit" class="btn-reservation-modal-confirm">
                            Confirmer la réservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles spécifiques au modal de réservation */
.reservation-modal-label {
    color: #666;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
}

.reservation-modal-label-light {
    color: #666;
    font-weight: 500;
    margin-bottom: 0.5rem;
    display: block;
}

.reservation-modal-input {
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    transition: all 0.3s;
}

.reservation-modal-input:focus {
    border-color: #00bcd4;
    box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.1);
    outline: none;
}

.options-list-reservation {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.option-item-reservation {
    display: flex;
    align-items: center;
    padding: 0.8rem 1rem;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    transition: all 0.3s;
    cursor: pointer;
}

.option-item-reservation:hover {
    border-color: #00bcd4;
    background: #f8f9fa;
}

.option-item-reservation input[type="checkbox"] {
    width: 20px;
    height: 20px;
    margin-right: 1rem;
    cursor: pointer;
    accent-color: #064420;
}

.option-item-reservation label {
    flex: 1;
    margin: 0;
    cursor: pointer;
    font-weight: 500;
    color: #333;
}

.option-price-reservation {
    color: #999;
    font-size: 0.9rem;
    font-weight: 500;
}

.btn-reservation-modal-cancel {
    flex: 1;
    padding: 0.9rem;
    background: #1a4d3e;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-reservation-modal-cancel:hover {
    background: #0f3d2f;
}

.btn-reservation-modal-confirm {
    flex: 1;
    padding: 0.9rem;
    background: #b9ff00;
    color: #064420;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-reservation-modal-confirm:hover {
    background: #a5d600;
}

@media (max-width: 768px) {
    .modal-body {
        padding: 1.5rem !important;
    }
    
    .d-flex.gap-3 {
        flex-direction: column;
    }
    
    .btn-reservation-modal-cancel,
    .btn-reservation-modal-confirm {
        width: 100%;
    }
}
</style>

<script>
// Déclaration globale de la fonction
window.openReservationModal = function(terrainId, terrainNom, prixHeure) {
    console.log('=== OUVERTURE MODAL ===');
    console.log('Terrain ID:', terrainId);
    console.log('Terrain Nom:', terrainNom);
    console.log('Prix Heure:', prixHeure);
    
    const modalElement = document.getElementById('reservationModal');
    
    if (!modalElement) {
        console.error('ERREUR: Modal element "reservationModal" non trouvé!');
        alert('Erreur: Le formulaire de réservation n\'est pas disponible.');
        return;
    }
    
    console.log('Modal trouvé:', modalElement);
    
    // Remplir l'ID du terrain
    const terrainIdInput = document.getElementById('modal_terrain_id');
    if (terrainIdInput) {
        terrainIdInput.value = terrainId;
        console.log('Terrain ID rempli:', terrainIdInput.value);
    } else {
        console.error('Input modal_terrain_id non trouvé!');
    }
    
    // Stocker le prix horaire
    modalElement.dataset.prixHeure = prixHeure || 0;
    console.log('Prix horaire stocké:', modalElement.dataset.prixHeure);
    
    // Ouvrir le modal
    try {
        const bsModal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });
        bsModal.show();
        console.log('Modal ouvert avec succès!');
    } catch(error) {
        console.error('ERREUR lors de l\'ouverture du modal:', error);
        alert('Erreur lors de l\'ouverture du formulaire: ' + error.message);
    }
    
    // Calculer le prix initial
    setTimeout(function() {
        if (typeof window.calculateTotalPrice === 'function') {
            window.calculateTotalPrice();
        }
    }, 100);
};

// Fonction de calcul du prix
window.calculateTotalPrice = function() {
    const modal = document.getElementById('reservationModal');
    if (!modal) return;
    
    const prixHeure = parseFloat(modal.dataset.prixHeure) || 0;
    const heureDebut = document.querySelector('input[name="heure_debut"]')?.value;
    const heureFin = document.querySelector('input[name="heure_fin"]')?.value;
    
    let total = 0;
    
    // Calculer les heures
    if (heureDebut && heureFin && prixHeure > 0) {
        const debut = new Date('2000-01-01 ' + heureDebut);
        const fin = new Date('2000-01-01 ' + heureFin);
        const diffHeures = (fin - debut) / (1000 * 60 * 60);
        
        if (diffHeures > 0) {
            total = prixHeure * diffHeures;
        }
    }
    
    // Ajouter les options
    const checkboxes = modal.querySelectorAll('input[type="checkbox"]:checked');
    checkboxes.forEach(cb => {
        const price = parseFloat(cb.dataset.price) || 0;
        total += price;
    });
    
    // Afficher le prix
    const prixSection = document.getElementById('prixTotalSection');
    const prixDisplay = document.getElementById('prixTotalDisplay');
    
    if (prixSection && prixDisplay) {
        if (total > 0) {
            prixSection.style.display = 'block';
            prixDisplay.textContent = total.toFixed(2) + ' MAD';
        } else {
            prixSection.style.display = 'none';
        }
    }
};

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INITIALISATION MODAL ===');
    
    const modal = document.getElementById('reservationModal');
    
    if (!modal) {
        console.error('ERREUR: Modal "reservationModal" non trouvé dans le DOM!');
        return;
    }
    
    console.log('Modal trouvé et prêt!');
    
    // Écouter les changements
    modal.addEventListener('change', window.calculateTotalPrice);
    modal.addEventListener('input', function(e) {
        if (e.target.type === 'time') {
            window.calculateTotalPrice();
        }
    });
    
    // Validation du formulaire
    const form = document.getElementById('reservationForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const heureDebut = document.querySelector('input[name="heure_debut"]')?.value;
            const heureFin = document.querySelector('input[name="heure_fin"]')?.value;
            
            if (heureDebut && heureFin) {
                const debut = new Date('2000-01-01 ' + heureDebut);
                const fin = new Date('2000-01-01 ' + heureFin);
                
                if (fin <= debut) {
                    e.preventDefault();
                    alert('L\'heure de fin doit être après l\'heure de début');
                    return false;
                }
            }
        });
        console.log('Validation formulaire configurée');
    }
});
</script>