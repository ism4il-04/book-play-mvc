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
            <div id="creneauxList" class="alert alert-info">
              Veuillez sélectionner une date pour voir les créneaux disponibles.
            </div>
            <input type="hidden" name="creneau_id" id="selected_creneau" required>
          </div>

          <!-- 3️⃣ Options supplémentaires -->
          <div class="mb-4" id="optionsSection" style="display: none;">
            <label class="form-label reservation-modal-label">Options supplémentaires</label>
            <div id="optionsList" class="options-list-reservation"></div>
          </div>

          <!-- 4️⃣ Informations du client -->
          <div class="mb-4">
            <label class="form-label reservation-modal-label">Informations du client</label>
            <div class="row g-3">
              <div class="col-md-6">
                <input type="text" class="form-control reservation-modal-input" name="nom_client" placeholder="Nom *" required>
              </div>
              <div class="col-md-6">
                <input type="text" class="form-control reservation-modal-input" name="prenom_client" placeholder="Prénom *" required>
              </div>
              <div class="col-md-6">
                <input type="email" class="form-control reservation-modal-input" name="email_client" placeholder="Email *" required>
              </div>
              <div class="col-md-6">
                <input type="tel" class="form-control reservation-modal-input" name="telephone_client" placeholder="Téléphone *" required>
              </div>
            </div>
          </div>

          <!-- 5️⃣ Prix total -->
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
.option-comment {
  margin-top: 0.8rem;
  display: none;
}
.option-comment textarea {
  width: 100%;
  resize: none;
  border-radius: 6px;
  border: 1px solid #ccc;
  padding: 0.5rem;
}
.creneau-item {
  padding: 1rem;
  background: white;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  cursor: pointer;
  margin-bottom: 0.6rem;
}
.creneau-item.selected {
  border-color: #064420;
  background: #e8f5e9;
}
.btn-reservation-modal-cancel, .btn-reservation-modal-confirm {
  flex: 1;
  padding: 0.9rem;
  border: none;
  border-radius: 8px;
  color: white;
  font-weight: 600;
}
.btn-reservation-modal-cancel {
  background: #6c757d;
}
.btn-reservation-modal-confirm {
  background: #064420;
}
</style>

<script>
let currentTerrainId = null;
let currentPrixHeure = 0;
let selectedCreneauData = null;

window.openReservationModal = function(terrainId, terrainNom, prixHeure) {
  currentTerrainId = terrainId;
  currentPrixHeure = parseFloat(prixHeure) || 0;
  document.getElementById('modal_terrain_id').value = terrainId;
  resetForm();
  loadTerrainOptions(terrainId);

  const today = new Date().toISOString().split('T')[0];
  document.getElementById('date_reservation').min = today;

  new bootstrap.Modal(document.getElementById('reservationModal')).show();
};

function resetForm() {
  document.getElementById('reservationForm').reset();
  document.getElementById('creneauxList').innerHTML = '<div class="alert alert-info">Veuillez sélectionner une date.</div>';
  document.getElementById('optionsSection').style.display = 'none';
  document.getElementById('prixTotalSection').style.display = 'none';
  selectedCreneauData = null;
}

function loadTerrainOptions(terrainId) {
  fetch(`<?php echo BASE_URL; ?>api/terrain/options?id=${terrainId}`)
    .then(r => r.json())
    .then(data => {
      const list = document.getElementById('optionsList');
      if (data.success && data.options.length > 0) {
        list.innerHTML = '';
        data.options.forEach(o => {
          const div = document.createElement('div');
          div.className = 'option-item-reservation';
          div.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <input type="checkbox" id="opt_${o.id_option}" value="${o.id_option}" data-price="${o.prix_option}" onchange="toggleOptionComment(this)">
                <label for="opt_${o.id_option}"><strong>${o.nom_option}</strong></label>
              </div>
              <span class="text-success fw-bold">+${parseFloat(o.prix_option).toFixed(2)} MAD</span>
            </div>
            <div class="option-comment" id="comment_${o.id_option}">
              <label class="form-label mt-2">Commentaire sur "${o.nom_option}" :</label>
              <textarea name="comment_option[${o.id_option}]" rows="3" placeholder="${o.description || 'Votre commentaire...'}"></textarea>
            </div>
          `;
          list.appendChild(div);
        });
        document.getElementById('optionsSection').style.display = 'block';
      }
    });
}

function toggleOptionComment(checkbox) {
  const comment = document.getElementById('comment_' + checkbox.value);
  if (checkbox.checked) {
    comment.style.display = 'block';
  } else {
    comment.style.display = 'none';
  }
  calculateTotalPrice();
}

function loadCreneaux(terrainId, date) {
  const list = document.getElementById('creneauxList');
  list.innerHTML = '<div class="alert alert-info">Chargement...</div>';
  fetch(`<?php echo BASE_URL; ?>api/terrain/creneaux?id=${terrainId}&date=${date}`)
    .then(r => r.json())
    .then(data => {
      list.innerHTML = '';
      if (data.success && data.creneaux.length > 0) {
        data.creneaux.forEach(c => {
          const div = document.createElement('div');
          div.className = 'creneau-item ' + (c.disponible == 1 ? '' : 'disabled');
          div.innerHTML = `<strong>${c.heure_ouverture.substring(0,5)} - ${c.heure_fermeture.substring(0,5)}</strong>`;
          if (c.disponible == 1) {
            div.onclick = () => selectCreneau(div, c);
          }
          list.appendChild(div);
        });
      } else {
        list.innerHTML = '<div class="alert alert-warning">Aucun créneau disponible.</div>';
      }
    });
}

function selectCreneau(el, c) {
  document.querySelectorAll('.creneau-item').forEach(i => i.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('selected_creneau').value = c.id_horaires;
  selectedCreneauData = c;
  calculateTotalPrice();
}

function calculateTotalPrice() {
  let total = 0;
  if (selectedCreneauData) {
    const debut = new Date('2000-01-01 ' + selectedCreneauData.heure_ouverture);
    const fin = new Date('2000-01-01 ' + selectedCreneauData.heure_fermeture);
    const diff = (fin - debut) / (1000 * 60 * 60);
    total += currentPrixHeure * diff;
  }
  document.querySelectorAll('#optionsList input:checked').forEach(cb => total += parseFloat(cb.dataset.price));
  const section = document.getElementById('prixTotalSection');
  const display = document.getElementById('prixTotalDisplay');
  if (total > 0) {
    section.style.display = 'block';
    display.textContent = total.toFixed(2) + ' MAD';
  } else {
    section.style.display = 'none';
  }
}

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('date_reservation').addEventListener('change', function() {
    const id = document.getElementById('modal_terrain_id').value;
    if (id && this.value) loadCreneaux(id, this.value);
  });
});
</script>
