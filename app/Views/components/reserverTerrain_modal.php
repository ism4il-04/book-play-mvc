<!-- Modal de réservation -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 15px; border: none;">
      <div class="modal-header" style="background: white; border-bottom: 1px solid #e0e0e0; padding: 1.5rem;">
        <h5 class="modal-title" id="reservationModalLabel" style="color: #064420; font-weight: 700;">
          Formulaire de Réservation
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body" style="padding: 2rem; background: #f8f9fa;">
        <form id="reservationForm" method="POST" action="<?php echo BASE_URL; ?>reservation/create">
          <input type="hidden" name="terrain_id" id="modal_terrain_id">

          <!-- 1️⃣ Date de réservation -->
          <div class="mb-4">
            <label class="form-label reservation-modal-label">Date de réservation *</label>
            <input type="date" class="form-control reservation-modal-input" name="date_reservation" id="date_reservation" required>
          </div>

          <!-- 2️⃣ Créneaux disponibles -->
          <div class="mb-4">
            <label class="form-label reservation-modal-label">Créneaux horaires disponibles *</label>
            <div id="heuresInfo" class="alert alert-secondary" style="display: none; margin-bottom: 10px;">
              <strong>Heures d'ouverture :</strong> <span id="heure_ouverture_display"></span>
              <strong style="margin-left: 15px;">Heures de fermeture :</strong> <span id="heure_fermeture_display"></span>
            </div>
            <div id="creneauxList" class="alert alert-info">
              Veuillez sélectionner une date pour voir les créneaux disponibles
            </div>
            <input type="hidden" name="heure_debut" id="heure_debut" required>
            <input type="hidden" name="heure_fin" id="heure_fin" required>
          </div>

          <!-- 3️⃣ Options supplémentaires -->
          <div class="mb-4" id="optionsSection" style="display: none;">
            <label class="form-label reservation-modal-label">Options supplémentaires</label>
            <div id="optionsList" class="options-list-reservation"></div>
          </div>

          <!-- 4️⃣ Commentaire général -->
          <div class="mb-4">
            <label class="form-label reservation-modal-label">Commentaire (optionnel)</label>
            <textarea class="form-control reservation-modal-input" name="commentaire" id="commentaire" rows="3" placeholder="Ajoutez des précisions pour votre réservation..."></textarea>
          </div>

          <!-- 5️⃣ Informations du client -->
          <div class="mb-4">
            <label class="form-label reservation-modal-label">Informations du client</label>
            <div class="row g-3">
              <div class="col-md-6">
                <input type="text" class="form-control reservation-modal-input" name="nom" id="client_nom"
                       placeholder="Nom *" value="<?php echo htmlspecialchars($_SESSION['user']['nom'] ?? $_SESSION['user']['name'] ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <input type="text" class="form-control reservation-modal-input" name="prenom" id="client_prenom"
                       placeholder="Prénom *" value="<?php echo htmlspecialchars($_SESSION['user']['prenom'] ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <input type="email" class="form-control reservation-modal-input" name="email" id="client_email"
                       placeholder="Email *" value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <input type="tel" class="form-control reservation-modal-input" name="telephone" id="client_telephone"
                       placeholder="Téléphone *" value="<?php echo htmlspecialchars($_SESSION['user']['num_tel'] ?? ''); ?>" required>
              </div>
            </div>
          </div>

          <!-- 6️⃣ Prix total -->
          <div class="mb-4" id="prixTotalSection" style="display: none;">
            <div class="alert alert-info d-flex justify-content-between align-items-center"
              style="background: #e8f5e9; border: 1px solid #4caf50; border-radius: 10px;">
              <span style="font-weight: 600; color: #2e7d32;">Prix total estimé:</span>
              <span style="font-size: 1.2rem; font-weight: 700; color: #1b5e20;" id="prixTotalDisplay">0 MAD</span>
            </div>
          </div>

          <!-- Boutons -->
          <div class="d-flex gap-3">
            <button type="button" class="btn-reservation-modal-cancel" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn-reservation-modal-confirm">Confirmer la réservation</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
.reservation-modal-label {
  color: #666;
  font-weight: 600;
  margin-bottom: 0.5rem;
}
.reservation-modal-input {
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  transition: 0.3s;
}
.reservation-modal-input:focus {
  border-color: #00bcd4;
  box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.1);
  outline: none;
}
.options-list-reservation {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.option-item-reservation {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 1rem;
  transition: 0.3s;
}
.option-item-reservation:hover {
  border-color: #00bcd4;
  background: #f8f9fa;
}
.option-header {
  display: flex;
  align-items: center;
  gap: 0.8rem;
}
.option-header input[type="checkbox"] {
  width: 20px;
  height: 20px;
  cursor: pointer;
  accent-color: #064420;
}
.option-header label {
  flex: 1;
  cursor: pointer;
  font-weight: 500;
  margin: 0;
}
.option-price {
  color: #28a745;
  font-weight: 600;
  font-size: 0.95rem;
}
.option-description {
  font-size: 0.85rem;
  color: #666;
  margin-top: 0.5rem;
  padding-left: 2rem;
}
.creneau-item {
  padding: 1rem;
  background: white;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  cursor: pointer;
  margin-bottom: 0.6rem;
  transition: all 0.3s;
}
.creneau-item:hover:not(.disabled) {
  border-color: #00bcd4;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.creneau-item.selected {
  border-color: #064420;
  background: #e8f5e9;
  font-weight: 600;
}
.creneau-item.disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background: #f5f5f5;
}
.btn-reservation-modal-cancel, 
.btn-reservation-modal-confirm {
  flex: 1;
  padding: 0.9rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}
.btn-reservation-modal-cancel {
  background: #6c757d;
  color: white;
}
.btn-reservation-modal-cancel:hover {
  background: #5a6268;
}
.btn-reservation-modal-confirm {
  background: #064420;
  color: white;
}
.btn-reservation-modal-confirm:hover {
  background: #053315;
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
// Variables globales
let currentTerrainId = null;
let currentPrixHeure = 0;
let selectedCreneauData = null;

/**
 * Ouvrir le modal de réservation
 */
window.openReservationModal = function(terrainId, terrainNom, prixHeure) {
  console.log('=== OUVERTURE MODAL ===');
  console.log('Terrain ID:', terrainId);
  console.log('Prix heure:', prixHeure);
  
  currentTerrainId = terrainId;
  currentPrixHeure = parseFloat(prixHeure) || 0;
  
  // Remplir l'ID du terrain
  document.getElementById('modal_terrain_id').value = terrainId;
  
  // Charger les informations client depuis la base de données
  loadClientInfo();
  
  // Calculer le prix initial (même sans créneau)
  calculateTotalPrice();
  
  // Réinitialiser le formulaire
  resetForm();

  //Charger les créneaux pour aujourd'hui
  loadCreneaux(terrainId, null);
  
  // Charger les options du terrain
  loadTerrainOptions(terrainId);
  
  // Définir la date minimum (aujourd'hui)
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('date_reservation').min = today;
  
  // Ouvrir le modal
  const modal = new bootstrap.Modal(document.getElementById('reservationModal'));
  modal.show();
};

/**
 * Charger les informations client depuis la base de données
 */
function loadClientInfo() {
  fetch('<?php echo BASE_URL; ?>utilisateur/getUserInfo')
    .then(response => response.json())
    .then(data => {
      if (data.success && data.user) {
        const user = data.user;
        document.getElementById('client_nom').value = user.nom || user.name || '';
        document.getElementById('client_prenom').value = user.prenom || '';
        document.getElementById('client_email').value = user.email || '';
        document.getElementById('client_telephone').value = user.num_tel || '';
      }
    })
    .catch(error => {
      console.error('Erreur chargement infos client:', error);
    });
}

/**
 * Fonction alternative pour boutons avec data attributes
 */
function openReservationModalFromButton(button) {
  const id = button.dataset.id;
  const nom = button.dataset.nom || button.dataset.localisation;
  const prix = button.dataset.prix;
  openReservationModal(id, nom, prix);
}

/**
 * Réinitialiser le formulaire
 */
function resetForm() {
  document.getElementById('reservationForm').reset();
  document.getElementById('modal_terrain_id').value = currentTerrainId;
  document.getElementById('creneauxList').innerHTML = '<div class="alert alert-info">Veuillez sélectionner une date pour voir les créneaux disponibles</div>';
  document.getElementById('heuresInfo').style.display = 'none';
  document.getElementById('optionsSection').style.display = 'none';
  selectedCreneauData = null;
  // Ne pas cacher le prix - il sera recalculé par calculateTotalPrice()
}

/**
 * Charger les options disponibles pour le terrain
 */
function loadTerrainOptions(terrainId) {
  console.log('Chargement des options pour le terrain:', terrainId);
  
  fetch(`<?php echo BASE_URL; ?>terrain/options?id=${terrainId}`)
    .then(response => response.json())
    .then(data => {
      console.log('Options reçues:', data);
      
      const optionsList = document.getElementById('optionsList');
      const optionsSection = document.getElementById('optionsSection');
      
      if (data.success && data.options && data.options.length > 0) {
        optionsList.innerHTML = '';
        
        data.options.forEach(option => {
          const optionDiv = document.createElement('div');
          optionDiv.className = 'option-item-reservation';
          optionDiv.innerHTML = `
            <div class="option-header">
              <input type="checkbox" 
                     id="opt_${option.id_option}" 
                     name="options[]"
                     value="${option.id_option}" 
                     data-price="${option.prix_option}"
                     onchange="toggleOptionComment(this)">
              <label for="opt_${option.id_option}">${option.nom_option}</label>
              <span class="option-price">+${parseFloat(option.prix_option).toFixed(2)} MAD</span>
            </div>
            ${option.description ? `<div class="option-description">${option.description}</div>` : ''}
          `;
          optionsList.appendChild(optionDiv);
        });
        
        optionsSection.style.display = 'block';
      } else {
        optionsSection.style.display = 'none';
      }
    })
    .catch(error => {
      console.error('Erreur chargement options:', error);
      document.getElementById('optionsSection').style.display = 'none';
    });
}

/**
 * Gérer le changement d'état d'une option
 */
function toggleOptionComment(checkbox) {
  // Recalculer le prix
  calculateTotalPrice();
}

/**
 * Charger les créneaux horaires disponibles
 */
function loadCreneaux(terrainId, date) {
  console.log('Chargement créneaux - Terrain:', terrainId, 'Date:', date);
  
  const creneauxList = document.getElementById('creneauxList');
  creneauxList.innerHTML = '<div class="alert alert-info">Chargement des créneaux...</div>';
  
  const url = `<?php echo BASE_URL; ?>terrain/creneaux?id=${terrainId}${date ? '&date=' + date : ''}`;
  console.log('URL API:', url);
  
  fetch(url)
    .then(response => response.json())
    .then(data => {
      console.log('Créneaux reçus:', data);
      
      if (data.success) {
        // Afficher les heures d'ouverture/fermeture
        if (data.heure_ouverture && data.heure_fermeture) {
          document.getElementById('heure_ouverture_display').textContent = data.heure_ouverture;
          document.getElementById('heure_fermeture_display').textContent = data.heure_fermeture;
          document.getElementById('heuresInfo').style.display = 'block';
        }
        
        creneauxList.innerHTML = '';
        
        if (data.creneaux && data.creneaux.length > 0) {
          data.creneaux.forEach(creneau => {
            const creneauDiv = document.createElement('div');
            creneauDiv.className = 'creneau-item';
            
            if (creneau.disponible == 0) {
              creneauDiv.classList.add('disabled');
            }
            
            creneauDiv.innerHTML = `
              <div class="d-flex justify-content-between align-items-center">
                <span style="font-size: 1.05rem;">
                  <strong>${creneau.heure_ouverture} - ${creneau.heure_fermeture}</strong>
                </span>
                <span class="badge ${creneau.disponible == 1 ? 'bg-success' : 'bg-danger'}">
                  ${creneau.disponible == 1 ? 'Disponible' : 'Réservé'}
                </span>
              </div>
            `;
            
            if (creneau.disponible == 1) {
              creneauDiv.onclick = () => selectCreneau(creneauDiv, creneau);
            }
            
            creneauxList.appendChild(creneauDiv);
          });
        } else {
          creneauxList.innerHTML = '<div class="alert alert-warning">Aucun créneau disponible pour cette date.</div>';
        }
      } else {
        creneauxList.innerHTML = '<div class="alert alert-danger">Erreur : ' + (data.message || 'Impossible de charger les créneaux') + '</div>';
      }
    })
    .catch(error => {
      console.error('Erreur chargement créneaux:', error);
      creneauxList.innerHTML = '<div class="alert alert-danger">Erreur de connexion au serveur.</div>';
    });
}

/**
 * Sélectionner un créneau
 */
function selectCreneau(element, creneauData) {
    // Retirer la sélection des autres créneaux
    document.querySelectorAll('.creneau-item').forEach(item => {
        item.classList.remove('selected');
    });
    
    // Sélectionner ce créneau
    element.classList.add('selected');
    
    // Stocker les données
    console.log('Créneau sélectionné:', creneauData);
    document.getElementById('heure_debut').value = creneauData.heure_ouverture;
    document.getElementById('heure_fin').value = creneauData.heure_fermeture;
    
    // Calculer le prix
    calculateTotalPrice();
}

/**
 * Calculer le prix total
 */
function calculateTotalPrice() {
  let total = 0;
  
  // Prix du créneau (terrain base price)
  if (selectedCreneauData) {
    const debut = new Date('2000-01-01 ' + selectedCreneauData.heure_ouverture);
    const fin = new Date('2000-01-01 ' + selectedCreneauData.heure_fermeture);
    const diffHeures = (fin - debut) / (1000 * 60 * 60);
    
    total += currentPrixHeure * diffHeures;
    console.log('Prix créneau:', currentPrixHeure, 'x', diffHeures, '=', currentPrixHeure * diffHeures);
  } else if (currentPrixHeure > 0) {
    // Afficher le prix de base même sans créneau sélectionné (pour 1 heure par défaut)
    total = currentPrixHeure;
    console.log('Prix de base (1h):', currentPrixHeure);
  }
  
  // Prix des options
  const checkboxes = document.querySelectorAll('#optionsList input[type="checkbox"]:checked');
  checkboxes.forEach(cb => {
    const price = parseFloat(cb.dataset.price) || 0;
    total += price;
    console.log('Option:', cb.value, 'Prix:', price);
  });
  
  console.log('Prix total:', total);
  
  // Afficher le prix
  const prixSection = document.getElementById('prixTotalSection');
  const prixDisplay = document.getElementById('prixTotalDisplay');
  
  if (total > 0) {
    prixSection.style.display = 'block';
    prixDisplay.textContent = total.toFixed(2) + ' MAD';
    if (!selectedCreneauData) {
      prixDisplay.textContent += ' (estimation pour 1 heure)';
    }
  } else {
    prixSection.style.display = 'none';
  }
}

/**
 * Initialisation au chargement de la page
 */
document.addEventListener('DOMContentLoaded', function() {
  console.log('Initialisation du modal de réservation');
  
  // Événement changement de date
  const dateInput = document.getElementById('date_reservation');
  if (dateInput) {
    dateInput.addEventListener('change', function() {
      const terrainId = document.getElementById('modal_terrain_id').value;
      if (terrainId && this.value) {
        console.log('Date changée:', this.value);
        loadCreneaux(terrainId, this.value);
      }
    });
  }
  
  // Validation du formulaire
  const form = document.getElementById('reservationForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      const heureDebut = document.getElementById('heure_debut').value;
      const heureFin = document.getElementById('heure_fin').value;
      
      if (!heureDebut || !heureFin) {
        e.preventDefault();
        alert('Veuillez sélectionner un créneau horaire');
        return false;
      }
      
      console.log('Soumission du formulaire avec:', {
        terrain_id: document.getElementById('modal_terrain_id').value,
        date: document.getElementById('date_reservation').value,
        heure_debut: heureDebut,
        heure_fin: heureFin
      });
    });
  }
});
</script>