<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/gestionnaire-modal.css">
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

                    <!-- Informations du terrain -->
                    <div class="section-title">Informations du terrain</div>

                    <div class="mb-3">
                        <label for="nom_terrain" class="form-label">Nom du terrain</label>
                        <input type="text" class="form-control" id="nom_terrain" name="nom_terrain" placeholder="Ex: Complexe Sportif..." required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="localisation" class="form-label">Localisation</label>
                        <input type="text" class="form-control" id="localisation" name="localisation" placeholder="Adresse complète">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="type_terrain" class="form-label">Type de terrain</label>
                            <select class="form-select" id="type_terrain" name="type_terrain">
                                <option value="">Sélectionner un type</option>
                                <option value="Gazon naturel">Gazon naturel</option>
                                <option value="Gazon synthétique">Gazon synthétique</option>
                                <option value="Terre / Sol">Terre / Sol</option>
                                <option value="Terrain couvert / Salle">Terrain couvert / Salle</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="format_terrain" class="form-label">Format du terrain</label>
                            <select class="form-select" id="format_terrain" name="format_terrain">
                                <option value="">Sélectionner un format</option>
                                <option value="5v5">5v5</option>
                                <option value="6v6">6v6</option>
                                <option value="7v7">7v7</option>
                                <option value="8v8">8v8</option>
                                <option value="9v9">9v9</option>
                                <option value="10v10">10v10</option>
                                <option value="11v11">11v11</option>
                            </select>
                        </div>
                    </div>

                    <!-- Documents administratifs -->
                    <div class="section-title">Documents administratifs</div>
                    
                    <div class="mb-3">
                        <label class="form-label">Documents (permis d'exploitation, certificat de propriété, etc.)</label>
                        <div class="document-upload">
                            <div class="upload-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                            </div>
                            <input type="file" class="form-control d-none" id="documents" name="documents[]" multiple accept=".pdf,.png,.jpg,.jpeg">
                            <label for="documents" class="upload-label">Cliquez pour télécharger</label>
                            <small class="upload-hint">PDF, PNG, JPG jusqu'à 10MB</small>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="gestionnaireForm" class="btn btn-submit">Envoyer la demande</button>
            </div>
        </div>
    </div>
</div>