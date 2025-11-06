<?php
$baseUrl = BASE_URL;

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/gestionnaire-modal.css">
    <title>Ajouter un terrain - <?php echo APP_NAME; ?></title>
    <style>
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
    <h2>Ajouter un nouveau terrain</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <?php 
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); // Effacer le message d'erreur après l'affichage
            ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>terrain/store" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informations du terrain</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="nom_terrain" class="form-label">Nom du terrain <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nom_terrain" name="nom_terrain" 
                           value="<?= isset($_SESSION['form_data']['nom_terrain']) ? htmlspecialchars($_SESSION['form_data']['nom_terrain']) : '' ?>"
                           placeholder="Ex: Complexe Sportif..." required maxlength="255">
                    <div class="invalid-feedback">
                        Veuillez entrer un nom pour le terrain.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="localisation" class="form-label">Localisation <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="localisation" name="localisation" 
                           value="<?= isset($_SESSION['form_data']['localisation']) ? htmlspecialchars($_SESSION['form_data']['localisation']) : '' ?>"
                           placeholder="Adresse complète" required>
                    <div class="invalid-feedback">
                        Veuillez entrer la localisation du terrain.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="localisation" class="form-label">Prix/heure <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="prix" name="prix"
                           value="<?= isset($_SESSION['form_data']['prix']) ? htmlspecialchars($_SESSION['form_data']['prix']) : '' ?>"
                           placeholder="prix/heure" required>
                    <div class="invalid-feedback">
                        Veuillez entrer le prix par heure.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Image du terrain <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                    <div class="form-text">Formats acceptés: JPG, JPEG, PNG, GIF (max 5MB)</div>
                    <div class="invalid-feedback">
                        Veuillez sélectionner une image pour le terrain.
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type_terrain" class="form-label">Type de terrain <span class="text-danger">*</span></label>
                        <select class="form-select" id="type_terrain" name="type_terrain" required>
                            <option value="" disabled selected>Sélectionner un type</option>
                            <option value="Gazon naturel" <?= (isset($_SESSION['form_data']['type_terrain']) && $_SESSION['form_data']['type_terrain'] === 'Gazon naturel') ? 'selected' : '' ?>>Gazon naturel</option>
                            <option value="Gazon synthétique" <?= (isset($_SESSION['form_data']['type_terrain']) && $_SESSION['form_data']['type_terrain'] === 'Gazon synthétique') ? 'selected' : '' ?>>Gazon synthétique</option>
                            <option value="Terre / Sol" <?= (isset($_SESSION['form_data']['type_terrain']) && $_SESSION['form_data']['type_terrain'] === 'Terre / Sol') ? 'selected' : '' ?>>Terre / Sol</option>
                            <option value="Terrain couvert / Salle" <?= (isset($_SESSION['form_data']['type_terrain']) && $_SESSION['form_data']['type_terrain'] === 'Terrain couvert / Salle') ? 'selected' : '' ?>>Terrain couvert / Salle</option>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner un type de terrain.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="format_terrain" class="form-label">Format du terrain <span class="text-danger">*</span></label>
                        <select class="form-select" id="format_terrain" name="format_terrain" required>
                            <option value="" disabled selected>Sélectionner un format</option>
                            <option value="5v5" <?= (isset($_SESSION['form_data']['format_terrain']) && $_SESSION['form_data']['format_terrain'] === '5v5') ? 'selected' : '' ?>>5v5</option>
                            <option value="6v6" <?= (isset($_SESSION['form_data']['format_terrain']) && $_SESSION['form_data']['format_terrain'] === '6v6') ? 'selected' : '' ?>>6v6</option>
                            <option value="7v7" <?= (isset($_SESSION['form_data']['format_terrain']) && $_SESSION['form_data']['format_terrain'] === '7v7') ? 'selected' : '' ?>>7v7</option>
                            <option value="8v8" <?= (isset($_SESSION['form_data']['format_terrain']) && $_SESSION['form_data']['format_terrain'] === '8v8') ? 'selected' : '' ?>>8v8</option>
                            <option value="9v9" <?= (isset($_SESSION['form_data']['format_terrain']) && $_SESSION['form_data']['format_terrain'] === '9v9') ? 'selected' : '' ?>>9v9</option>
                            <option value="10v10" <?= (isset($_SESSION['form_data']['format_terrain']) && $_SESSION['form_data']['format_terrain'] === '10v10') ? 'selected' : '' ?>>10v10</option>
                            <option value="11v11" <?= (isset($_SESSION['form_data']['format_terrain']) && $_SESSION['form_data']['format_terrain'] === '11v11') ? 'selected' : '' ?>>11v11</option>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner un format de terrain.
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="/terrain/index" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Ajouter le terrain
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
</body>
</html>
