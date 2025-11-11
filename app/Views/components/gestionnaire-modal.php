<link rel="stylesheet" href="<?= BASE_URL ?>css/gestionnaire-modal.css">
<div class="modal fade" id="gestionnaireModal" tabindex="-1" aria-labelledby="gestionnaireModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gestionnaireModalLabel">Devenir Gestionnaire de Terrain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="gestionnaireForm" enctype="multipart/form-data">
                    <!-- Informations personnelles -->
                    <div class="section-title">Informations personnelles</div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="col-md-6">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" placeholder="+212 6 XX XX XX XX">
                        </div>
                    </div>

                    <!-- Terrains Section -->
                    <div class="section-title">Informations des terrains</div>
                    
                    <div id="terrainsContainer">
                        <!-- Terrain 1 (default) -->
                        <div class="terrain-block" data-terrain-index="0">
                            <div class="terrain-header">
                                <h6>Terrain 1</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-terrain" style="display: none;">
                                    <i class="fas fa-times"></i> Supprimer
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nom du terrain</label>
                                <input type="text" class="form-control" name="terrains[0][nom_terrain]" placeholder="Ex: Complexe Sportif..." required maxlength="255">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Localisation</label>
                                <input type="text" class="form-control" name="terrains[0][localisation]" placeholder="Adresse complète" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Type de terrain</label>
                                    <select class="form-select" name="terrains[0][type_terrain]" required>
                                        <option value="">Sélectionner</option>
                                        <option value="Gazon naturel">Gazon naturel</option>
                                        <option value="Gazon synthétique">Gazon synthétique</option>
                                        <option value="Terre / Sol">Terre / Sol</option>
                                        <option value="Terrain couvert / Salle">Terrain couvert / Salle</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Format</label>
                                    <select class="form-select" name="terrains[0][format_terrain]" required>
                                        <option value="">Sélectionner</option>
                                        <option value="5v5">5v5</option>
                                        <option value="6v6">6v6</option>
                                        <option value="7v7">7v7</option>
                                        <option value="8v8">8v8</option>
                                        <option value="9v9">9v9</option>
                                        <option value="10v10">10v10</option>
                                        <option value="11v11">11v11</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Prix/heure (DH)</label>
                                    <input type="number" class="form-control" name="terrains[0][prix_heure]" placeholder="Ex: 200" required min="0">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Justificatifs (permis, certificat, etc.) *</label>
                                <div class="document-upload">
                                    <div class="upload-icon">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17 8 12 3 7 8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                    </div>
                                    <input type="file" class="form-control d-none justificatif-input" name="terrains[0][justificatifs][]" multiple accept=".pdf,.png,.jpg,.jpeg" required>
                                    <label class="upload-label">Cliquez pour télécharger</label>
                                    <small class="upload-hint">PDF, PNG, JPG jusqu'à 10MB</small>
                                    <div class="file-list mt-2"></div>
                                </div>
                            </div>

                            <!-- Options Section -->
                            <div class="mb-3">
                                <label class="form-label">Options disponibles pour ce terrain</label>
                                <div class="options-selection" id="options-container-0">
                                    <!-- Options will be loaded dynamically -->
                                    <div class="text-center text-muted">
                                        <i class="fas fa-spinner fa-spin"></i> Chargement des options...
                                    </div>
                                </div>
                            </div>

                            <!-- Horaires Section -->
                            <div class="mb-3">
                                <label class="form-label">Horaires d'ouverture *</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="heure_ouverture_0" class="form-label small">Heure d'ouverture</label>
                                        <input type="time" class="form-control" id="heure_ouverture_0" name="terrains[0][heure_ouverture]" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="heure_fermeture_0" class="form-label small">Heure de fermeture</label>
                                        <input type="time" class="form-control" id="heure_fermeture_0" name="terrains[0][heure_fermeture]" required>
                                    </div>
                                </div>
                            </div>
                            <hr class="terrain-separator">
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="addTerrainBtn">
                        <i class="fas fa-plus"></i> Ajouter un autre terrain
                    </button>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="gestionnaireForm" class="btn btn-submit">Envoyer la demande</button>
            </div>
        </div>
    </div>
</div>

<style>
.terrain-block {
    border: 2px dashed #e0e0e0;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    background: #f9f9f9;
}
.terrain-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}
.terrain-header h6 {
    margin: 0;
    color: #667eea;
    font-weight: 600;
}
.terrain-separator {
    display: none;
}
.file-list {
    font-size: 0.85rem;
    color: #666;
}
.file-item {
    display: inline-block;
    background: #e8f5e9;
    padding: 4px 8px;
    border-radius: 4px;
    margin: 2px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let terrainCount = 1;
    
    // Function to load options for a specific terrain index
    function loadOptionsForTerrain(terrainIndex) {
        const container = document.getElementById(`options-container-${terrainIndex}`);
        if (!container) return;
        
        fetch('<?= BASE_URL ?>gestionnaire/getOptions', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderOptions(container, data.options, terrainIndex);
            } else {
                container.innerHTML = '<div class="text-danger">Erreur lors du chargement des options</div>';
            }
        })
        .catch(error => {
            console.error('Error loading options:', error);
            container.innerHTML = '<div class="text-danger">Erreur de chargement</div>';
        });
    }
    
    // Function to render options in container
    function renderOptions(container, options, terrainIndex) {
        container.innerHTML = '';
        
        options.forEach(option => {
            const optionHtml = `
                <div class="option-item mb-2" data-option-id="${option.id_option}">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" 
                               name="terrains[${terrainIndex}][options][${option.id_option}][selected]" 
                               value="1" id="option_${terrainIndex}_${option.id_option}">
                        <label class="form-check-label" for="option_${terrainIndex}_${option.id_option}">
                            <strong>${option.nom_option}</strong> - ${option.description}
                        </label>
                    </div>
                    <input type="number" class="form-control form-control-sm mt-1 option-price" 
                           name="terrains[${terrainIndex}][options][${option.id_option}][prix]" 
                           placeholder="Prix (DH)" min="0" step="0.01" style="display: none;">
                </div>
            `;
            container.insertAdjacentHTML('beforeend', optionHtml);
        });
        
        // Attach event handlers for the newly created options
        attachOptionsHandlerForTerrain(terrainIndex);
    }
    
    // Function to attach option handlers for a specific terrain
    function attachOptionsHandlerForTerrain(terrainIndex) {
        const container = document.getElementById(`options-container-${terrainIndex}`);
        if (!container) return;
        
        const checkboxes = container.querySelectorAll('.form-check-input');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const optionItem = this.closest('.option-item');
                const priceInput = optionItem.querySelector('.option-price');
                if (this.checked) {
                    priceInput.style.display = 'block';
                    priceInput.required = true;
                } else {
                    priceInput.style.display = 'none';
                    priceInput.required = false;
                    priceInput.value = '';
                }
            });
        });
    }
    
    // Load options when modal is shown
    const modal = document.getElementById('gestionnaireModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function() {
            loadOptionsForTerrain(0); // Load for first terrain
        });
    }
    
    // Add terrain button
    document.getElementById('addTerrainBtn').addEventListener('click', function() {
        const container = document.getElementById('terrainsContainer');
        const newIndex = terrainCount;
        
        const terrainBlock = document.createElement('div');
        terrainBlock.className = 'terrain-block';
        terrainBlock.setAttribute('data-terrain-index', newIndex);
        terrainBlock.innerHTML = `
            <div class="terrain-header">
                <h6>Terrain ${newIndex + 1}</h6>
                <button type="button" class="btn btn-sm btn-danger remove-terrain">
                    <i class="fas fa-times"></i> Supprimer
                </button>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Nom du terrain</label>
                <input type="text" class="form-control" name="terrains[${newIndex}][nom_terrain]" placeholder="Ex: Complexe Sportif..." required maxlength="255">
            </div>

            <div class="mb-3">
                <label class="form-label">Localisation</label>
                <input type="text" class="form-control" name="terrains[${newIndex}][localisation]" placeholder="Adresse complète" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Type de terrain</label>
                    <select class="form-select" name="terrains[${newIndex}][type_terrain]" required>
                        <option value="">Sélectionner</option>
                        <option value="Gazon naturel">Gazon naturel</option>
                        <option value="Gazon synthétique">Gazon synthétique</option>
                        <option value="Terre / Sol">Terre / Sol</option>
                        <option value="Terrain couvert / Salle">Terrain couvert / Salle</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Format</label>
                    <select class="form-select" name="terrains[${newIndex}][format_terrain]" required>
                        <option value="">Sélectionner</option>
                        <option value="5v5">5v5</option>
                        <option value="6v6">6v6</option>
                        <option value="7v7">7v7</option>
                        <option value="8v8">8v8</option>
                        <option value="9v9">9v9</option>
                        <option value="10v10">10v10</option>
                        <option value="11v11">11v11</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Prix/heure (DH)</label>
                    <input type="number" class="form-control" name="terrains[${newIndex}][prix_heure]" placeholder="Ex: 200" required min="0">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Justificatifs (permis, certificat, etc.) *</label>
                <div class="document-upload">
                    <div class="upload-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                    </div>
                    <input type="file" class="form-control d-none justificatif-input" name="terrains[${newIndex}][justificatifs][]" multiple accept=".pdf,.png,.jpg,.jpeg" required>
                    <label class="upload-label">Cliquez pour télécharger</label>
                    <small class="upload-hint">PDF, PNG, JPG jusqu'à 10MB</small>
                    <div class="file-list mt-2"></div>
                </div>
            </div>

            <!-- Options Section -->
            <div class="mb-3">
                <label class="form-label">Options disponibles pour ce terrain</label>
                <div class="options-selection" id="options-container-${newIndex}">
                    <!-- Options will be loaded dynamically -->
                    <div class="text-center text-muted">
                        <i class="fas fa-spinner fa-spin"></i> Chargement des options...
                    </div>
                </div>
            </div>

            <!-- Horaires Section -->
            <div class="mb-3">
                <label class="form-label">Horaires d'ouverture *</label>
                <div class="row">
                    <div class="col-md-6">
                        <label for="heure_ouverture_${newIndex}" class="form-label small">Heure d'ouverture</label>
                        <input type="time" class="form-control" id="heure_ouverture_${newIndex}" name="terrains[${newIndex}][heure_ouverture]" required>
                    </div>
                    <div class="col-md-6">
                        <label for="heure_fermeture_${newIndex}" class="form-label small">Heure de fermeture</label>
                        <input type="time" class="form-control" id="heure_fermeture_${newIndex}" name="terrains[${newIndex}][heure_fermeture]" required>
                    </div>
                </div>
            </div>
            <hr class="terrain-separator">
        `;
        
        container.appendChild(terrainBlock);
        terrainCount++;
        
        // Show remove button on first terrain if more than one exists
        updateRemoveButtons();
        
        // Attach file upload handler to new input
        attachFileUploadHandler(terrainBlock);
        // Load options for the new terrain
        loadOptionsForTerrain(newIndex);
    });
    
    // Remove terrain
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-terrain')) {
            const terrainBlock = e.target.closest('.terrain-block');
            terrainBlock.remove();
            updateRemoveButtons();
            renumberTerrains();
        }
    });
    
    // Update remove buttons visibility
    function updateRemoveButtons() {
        const blocks = document.querySelectorAll('.terrain-block');
        blocks.forEach((block, index) => {
            const removeBtn = block.querySelector('.remove-terrain');
            if (blocks.length > 1) {
                removeBtn.style.display = 'inline-block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }
    
    // Renumber terrains after removal
    function renumberTerrains() {
        const blocks = document.querySelectorAll('.terrain-block');
        blocks.forEach((block, index) => {
            block.querySelector('.terrain-header h6').textContent = `Terrain ${index + 1}`;
        });
    }
    
    // File upload handling
    function attachFileUploadHandler(block) {
        const fileInput = block.querySelector('.justificatif-input');
        const fileList = block.querySelector('.file-list');
        const uploadLabel = block.querySelector('.upload-label');
        
        uploadLabel.addEventListener('click', function() {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function() {
            fileList.innerHTML = '';
            if (this.files.length > 0) {
                Array.from(this.files).forEach(file => {
                    const fileItem = document.createElement('span');
                    fileItem.className = 'file-item';
                    fileItem.textContent = file.name;
                    fileList.appendChild(fileItem);
                });
            }
        });
    }
    
    // Attach to existing terrain blocks
    document.querySelectorAll('.terrain-block').forEach(block => {
        attachFileUploadHandler(block);
        attachOptionsHandler(block);
    });
    
    // Function to handle options checkboxes
    function attachOptionsHandler(block) {
        const checkboxes = block.querySelectorAll('.form-check-input');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const optionItem = this.closest('.option-item');
                const priceInput = optionItem.querySelector('.option-price');
                if (this.checked) {
                    priceInput.style.display = 'block';
                    priceInput.required = true;
                } else {
                    priceInput.style.display = 'none';
                    priceInput.required = false;
                    priceInput.value = '';
                }
            });
        });
    }
    
    // Form submission
    document.getElementById('gestionnaireForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = document.querySelector('.btn-submit');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Envoi en cours...';
        
        try {
            const response = await fetch('<?= BASE_URL ?>gestionnaire/submitDemand', {
                method: 'POST',
                body: formData
            });
            
            // Get response text first
            const responseText = await response.text();
            console.log('Server response:', responseText);
            
            // Try to parse as JSON
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                console.error('Response was:', responseText);
                alert('Erreur serveur: La réponse n\'est pas au format JSON. Vérifiez la console pour plus de détails.');
                return;
            }
            
            if (result.success) {
                alert('Votre demande a été envoyée avec succès! Vous recevrez une réponse par email.');
                bootstrap.Modal.getInstance(document.getElementById('gestionnaireModal')).hide();
                this.reset();
            } else {
                alert('Erreur: ' + (result.message || 'Une erreur est survenue'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erreur lors de l\'envoi de la demande: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Envoyer la demande';
        }
    });
});
</script>