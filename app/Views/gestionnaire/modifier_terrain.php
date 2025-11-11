<?php
$baseUrl = BASE_URL;

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un terrain - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 1rem;
            padding: 0.75rem 1.25rem;
            border: 1px solid #f5c6cb;
            border-radius: 0.25rem;
            background-color: #f8d7da;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>Modifier le terrain</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <?php 
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); // Effacer le message d'erreur après l'affichage
            ?>
        </div>
    <?php endif; ?>
    
    <form action="<?= BASE_URL ?>terrain/updateTerrain/<?= $terrain['id_terrain'] ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informations du terrain</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="nom_terrain" class="form-label">Nom du terrain <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nom_terrain" name="nom_terrain" 
                           value="<?= isset($_SESSION['form_data']['nom_terrain']) ? htmlspecialchars($_SESSION['form_data']['nom_terrain']) : htmlspecialchars($terrain['nom_terrain']) ?>"
                           placeholder="Ex: Complexe Sportif..." required maxlength="255">
                    <div class="invalid-feedback">
                        Veuillez entrer un nom pour le terrain.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="localisation" class="form-label">Localisation <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="localisation" name="localisation" 
                           value="<?= isset($_SESSION['form_data']['localisation']) ? htmlspecialchars($_SESSION['form_data']['localisation']) : htmlspecialchars($terrain['localisation']) ?>"
                           placeholder="Adresse complète" required>
                    <div class="invalid-feedback">
                        Veuillez entrer la localisation du terrain.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="prix" class="form-label">Prix/heure <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="prix" name="prix"
                           value="<?= isset($_SESSION['form_data']['prix']) ? htmlspecialchars($_SESSION['form_data']['prix']) : htmlspecialchars($terrain['prix_heure']) ?>"
                           placeholder="prix/heure" required>
                    <div class="invalid-feedback">
                        Veuillez entrer le prix par heure.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Image du terrain</label>
                    <?php if (!empty($terrain['image'])): ?>
                        <div class="mb-2">
                            <img src="<?= BASE_URL ?>images/<?= htmlspecialchars($terrain['image']) ?>" 
                                 alt="Image actuelle" 
                                 style="max-width: 200px; border-radius: 8px;">
                            <p class="text-muted small">Image actuelle</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="form-text">Formats acceptés: JPG, JPEG, PNG, GIF (max 5MB). Laissez vide pour conserver l'image actuelle.</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type_terrain" class="form-label">Type de terrain <span class="text-danger">*</span></label>
                        <select class="form-select" id="type_terrain" name="type_terrain" required>
                            <option value="" disabled>Sélectionner un type</option>
                            <?php
                            $types = ['Gazon naturel', 'Gazon synthétique', 'Terre / Sol', 'Terrain couvert / Salle'];
                            $selectedType = isset($_SESSION['form_data']['type_terrain']) ? $_SESSION['form_data']['type_terrain'] : $terrain['type_terrain'];
                            foreach ($types as $type):
                            ?>
                                <option value="<?= $type ?>" <?= $selectedType === $type ? 'selected' : '' ?>><?= $type ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner un type de terrain.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="format_terrain" class="form-label">Format du terrain <span class="text-danger">*</span></label>
                        <select class="form-select" id="format_terrain" name="format_terrain" required>
                            <option value="" disabled>Sélectionner un format</option>
                            <?php
                            $formats = ['5v5', '6v6', '7v7', '8v8', '9v9', '10v10', '11v11'];
                            $selectedFormat = isset($_SESSION['form_data']['format_terrain']) ? $_SESSION['form_data']['format_terrain'] : $terrain['format_terrain'];
                            foreach ($formats as $format):
                            ?>
                                <option value="<?= $format ?>" <?= $selectedFormat === $format ? 'selected' : '' ?>><?= $format ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner un format de terrain.
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select class="form-select" id="statut" name="statut" required>
                        <?php
                        $statuts = ['disponible' => 'Disponible', 'non disponible' => 'Indisponible'];
                        $selectedStatut = isset($_SESSION['form_data']['statut']) ? $_SESSION['form_data']['statut'] : $terrain['statut'];
                        foreach ($statuts as $value => $label):
                        ?>
                            <option value="<?= $value ?>" <?= $selectedStatut === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        Veuillez sélectionner un statut.
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= BASE_URL ?>terrain/gestionnaireTerrains" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>
        </div>
    </form>
    <?php 
    // Effacer les données du formulaire après l'affichage
    if (isset($_SESSION['form_data'])) {
        unset($_SESSION['form_data']);
    }
    ?>
</div>

<script>
// Validation du formulaire
(function () {
    'use strict'
    
    // Récupérer tous les formulaires auxquels nous voulons appliquer des styles de validation personnalisés
    const forms = document.querySelectorAll('.needs-validation')
    
    // Parcourir et empêcher la soumission si invalide
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
