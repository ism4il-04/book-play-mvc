<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Tournoi - Book&Play</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8eef3 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #064420 0%, #0a5c3c 100%);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .form-container {
            background: white;
            padding: 40px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.1);
        }

        .form-section {
            margin-bottom: 35px;
            padding-bottom: 35px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title {
            font-size: 20px;
            color: #064420;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::before {
            content: '';
            width: 4px;
            height: 24px;
            background: linear-gradient(135deg, #CEFE24, #b9ff00);
            border-radius: 2px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #CEFE24;
            box-shadow: 0 0 0 3px rgba(206, 254, 36, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 400;
            cursor: pointer;
            margin-top: 5px;
        }

        .checkbox-label input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .gestionnaire-select {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .terrains-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .terrain-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .terrain-card:hover {
            border-color: #CEFE24;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .terrain-card.selected {
            border-color: #064420;
            background: linear-gradient(135deg, rgba(206, 254, 36, 0.1), rgba(185, 255, 0, 0.05));
        }

        .terrain-card input[type="radio"],
        .terrain-card input[type="checkbox"] {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 24px;
            height: 24px;
            cursor: pointer;
        }
        
        .creneau-item,
        .equipe-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 2px solid #e0e0e0;
        }
        
        .creneau-item-header,
        .equipe-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .creneau-item-header h4,
        .equipe-item-header h4 {
            margin: 0;
            color: #064420;
        }
        
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-remove:hover {
            background: #c82333;
        }

        .terrain-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #f0f0f0;
        }

        .terrain-name {
            font-weight: 600;
            color: #064420;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .terrain-details {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }

        .terrain-detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-info {
            background: #e3f2fd;
            color: #1565c0;
            border-left: 4px solid #1976d2;
        }

        .alert-warning {
            background: #fff3e0;
            color: #e65100;
            border-left: 4px solid #f57c00;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 35px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #CEFE24 0%, #b9ff00 100%);
            color: #064420;
            box-shadow: 0 4px 15px rgba(206, 254, 36, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(206, 254, 36, 0.4);
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #666;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #064420;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .terrains-grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚽ Demande de Tournoi</h1>
            <p>Organisez votre tournoi sur les terrains de Book&Play</p>
        </div>

        <div class="form-container">
            <div id="alerts"></div>

            <form id="tournoiForm">
                <!-- SECTION 1: Informations du tournoi -->
                <div class="form-section">
                    <h2 class="section-title">📋 Informations du Tournoi</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom_tournoi">Nom du tournoi *</label>
                            <input type="text" id="nom_tournoi" name="nom_tournoi" 
                                   placeholder="Ex: Coupe d'Été 2025" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="slogan">Slogan</label>
                            <input type="text" id="slogan" name="slogan" 
                                   placeholder="Ex: Le meilleur tournoi de la région">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="type_tournoi">Type de tournoi *</label>
                            <select id="type_tournoi" name="type_tournoi" required>
                                <option value="">Sélectionnez</option>
                                <option value="elimination">Tournoi à élimination directe</option>
                                <option value="poule">Tournoi par poules</option>
                                <option value="mixte">Tournoi mixte (poules + élimination)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nb_equipes">Nombre d'équipes *</label>
                            <select id="nb_equipes" name="nb_equipes" required>
                                <option value="">Sélectionnez</option>
                                <option value="4">4 équipes</option>
                                <option value="8">8 équipes</option>
                                <option value="16">16 équipes</option>
                                <option value="32">32 équipes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Dates et heures -->
                <div class="form-section">
                    <h2 class="section-title">📅 Dates et Horaires</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_debut">Date de début *</label>
                            <input type="date" id="date_debut" name="date_debut" 
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="date_fin">Date de fin *</label>
                            <input type="date" id="date_fin" name="date_fin" required>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        ℹ️ <strong>Info:</strong> Vous devrez définir les créneaux de chaque match après la sélection du terrain
                    </div>
                </div>

                <!-- SECTION 2.5: Créneaux des matchs -->
                <div class="form-section" id="creneauxSection" style="display: none;">
                    <h2 class="section-title">⏰ Créneaux des Matchs</h2>
                    <div class="alert alert-info">
                        ℹ️ <strong>Définissez les créneaux</strong> pour chaque match du tournoi
                    </div>
                    <div id="creneauxContainer"></div>
                    <button type="button" class="btn btn-secondary" id="addCreneauBtn" style="margin-top: 15px;">
                        <i class="fas fa-plus"></i> Ajouter un créneau
                    </button>
                </div>

                <!-- SECTION 3: Prix -->
                <div class="form-section">
                    <h2 class="section-title">🏆 Prix et Récompenses</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="prix_premiere">1ère place</label>
                            <input type="text" id="prix_premiere" name="prix_premiere" 
                                   placeholder="Ex: Trophée + 1000 DH" value="Trophée">
                        </div>
                        <div class="form-group">
                            <label for="prix_deuxieme">2ème place</label>
                            <input type="text" id="prix_deuxieme" name="prix_deuxieme" 
                                   placeholder="Ex: Médaille + 500 DH" value="Médaille">
                        </div>
                        <div class="form-group">
                            <label for="prix_troisieme">3ème place</label>
                            <input type="text" id="prix_troisieme" name="prix_troisieme" 
                                   placeholder="Ex: Médaille" value="Médaille">
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: Sélection du terrain -->
                <div class="form-section">
                    <h2 class="section-title">🏟️ Sélection du Terrain</h2>
                    
                    <div class="alert alert-info">
                        ℹ️ <strong>Sélectionnez un terrain</strong> où se déroulera le tournoi *
                    </div>

                    <?php if (!empty($terrains)): ?>
                        <div id="terrainsGrid" class="terrains-grid">
                            <?php foreach ($terrains as $terrain): ?>
                                <?php
                                $imageSrc = !empty($terrain['image']) 
                                    ? $baseUrl . 'images/' . $terrain['image'] 
                                    : $baseUrl . 'images/default-terrain.png';
                                ?>
                                <div class="terrain-card" data-terrain-id="<?= $terrain['id_terrain'] ?>" data-gestionnaire-id="<?= $terrain['id_gestionnaire'] ?>">
                                    <input type="radio" name="terrain_id" value="<?= $terrain['id_terrain'] ?>" required>
                                    <img src="<?= $imageSrc ?>" alt="<?= htmlspecialchars($terrain['nom_terrain']) ?>" class="terrain-image" 
                                         onerror="this.src='<?= $baseUrl ?>images/default-terrain.png'">
                                    <div class="terrain-name"><?= htmlspecialchars($terrain['nom_terrain']) ?></div>
                                    <div class="terrain-details">
                                        <div class="terrain-detail-item">
                                            📍 <?= htmlspecialchars($terrain['localisation']) ?>
                                        </div>
                                        <div class="terrain-detail-item">
                                            ⚽ <?= htmlspecialchars($terrain['type_terrain']) ?> - <?= htmlspecialchars($terrain['format_terrain']) ?>
                                        </div>
                                        <div class="terrain-detail-item">
                                            💰 <?= htmlspecialchars($terrain['prix_heure']) ?> DH/heure
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">🏟️</div>
                            <p>Aucun terrain disponible pour le moment</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- SECTION 5: Équipes participantes -->
                <div class="form-section" id="equipesSection" style="display: none;">
                    <h2 class="section-title">👥 Équipes Participantes</h2>
                    <div class="alert alert-info">
                        ℹ️ <strong>Ajoutez les équipes</strong> qui participeront au tournoi
                    </div>
                    <div id="equipesContainer"></div>
                </div>

                <!-- Boutons -->
                <div class="button-group">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                        ← Retour
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitText">Envoyer la demande</span>
                        <span id="submitLoading" style="display: none;">
                            <div class="spinner" style="width: 20px; height: 20px; border-width: 2px;"></div>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Éléments du DOM
        const form = document.getElementById('tournoiForm');
        const submitBtn = document.getElementById('submitBtn');
        const alertsDiv = document.getElementById('alerts');

        // Validation des dates
        const dateDebutInput = document.getElementById('date_debut');
        const dateFinInput = document.getElementById('date_fin');

        dateDebutInput.addEventListener('change', function() {
            dateFinInput.min = this.value;
            if (dateFinInput.value && dateFinInput.value < this.value) {
                dateFinInput.value = this.value;
            }
        });

        let creneauCounter = 0;
        let equipeCounter = 0;

        // Ajouter les event listeners aux cartes de terrain
        document.querySelectorAll('.terrain-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.type !== 'radio') {
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    // Désélectionner les autres
                    document.querySelectorAll('input[name="terrain_id"]').forEach(r => {
                        if (r !== radio) {
                            r.closest('.terrain-card').classList.remove('selected');
                        }
                    });
                    this.classList.add('selected');
                    
                    // Afficher la section créneaux
                    document.getElementById('creneauxSection').style.display = 'block';
                } else {
                    // Désélectionner les autres
                    document.querySelectorAll('input[name="terrain_id"]').forEach(r => {
                        if (r !== e.target) {
                            r.closest('.terrain-card').classList.remove('selected');
                        }
                    });
                    this.classList.toggle('selected', e.target.checked);
                    if (e.target.checked) {
                        document.getElementById('creneauxSection').style.display = 'block';
                    }
                }
            });
        });

        // Gérer le nombre d'équipes
        const nbEquipesSelect = document.getElementById('nb_equipes');
        nbEquipesSelect.addEventListener('change', function() {
            const nbEquipes = parseInt(this.value);
            if (nbEquipes > 0) {
                generateEquipesFields(nbEquipes);
                document.getElementById('equipesSection').style.display = 'block';
            } else {
                document.getElementById('equipesSection').style.display = 'none';
            }
        });

        // Ajouter un créneau
        document.getElementById('addCreneauBtn').addEventListener('click', function() {
            addCreneauField();
        });

        function addCreneauField() {
            creneauCounter++;
            const container = document.getElementById('creneauxContainer');
            const creneauDiv = document.createElement('div');
            creneauDiv.className = 'creneau-item';
            creneauDiv.id = `creneau-${creneauCounter}`;
            creneauDiv.innerHTML = `
                <div class="creneau-item-header">
                    <h4>Match ${creneauCounter}</h4>
                    <button type="button" class="btn-remove" onclick="removeCreneau(${creneauCounter})">×</button>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Date *</label>
                        <input type="date" name="creneaux[${creneauCounter}][date]" 
                               min="${document.getElementById('date_debut').value}" 
                               max="${document.getElementById('date_fin').value}" required>
                    </div>
                    <div class="form-group">
                        <label>Heure de début *</label>
                        <input type="time" name="creneaux[${creneauCounter}][heure_debut]" required>
                    </div>
                    <div class="form-group">
                        <label>Heure de fin *</label>
                        <input type="time" name="creneaux[${creneauCounter}][heure_fin]" required>
                    </div>
                </div>
            `;
            container.appendChild(creneauDiv);
        }

        function removeCreneau(id) {
            const element = document.getElementById(`creneau-${id}`);
            if (element) {
                element.remove();
            }
        }

        function generateEquipesFields(nbEquipes) {
            const container = document.getElementById('equipesContainer');
            container.innerHTML = '';
            equipeCounter = 0;
            
            for (let i = 1; i <= nbEquipes; i++) {
                equipeCounter++;
                const equipeDiv = document.createElement('div');
                equipeDiv.className = 'equipe-item';
                equipeDiv.id = `equipe-${equipeCounter}`;
                equipeDiv.innerHTML = `
                    <div class="equipe-item-header">
                        <h4>Équipe ${i}</h4>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom de l'équipe *</label>
                            <input type="text" name="equipes[${equipeCounter}][nom_equipe]" 
                                   placeholder="Ex: Équipe ${i}" required>
                        </div>
                        <div class="form-group">
                            <label>Nombre de joueurs *</label>
                            <input type="number" name="equipes[${equipeCounter}][nbr_joueurs]" 
                                   min="5" max="22" value="11" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Liste des joueurs (séparés par des virgules)</label>
                        <textarea name="equipes[${equipeCounter}][liste_joueurs]" 
                                  placeholder="Joueur 1, Joueur 2, Joueur 3, ..." rows="2"></textarea>
                    </div>
                `;
                container.appendChild(equipeDiv);
            }
        }

        // Soumission du formulaire
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Vérifier qu'un terrain est sélectionné
            const selectedTerrain = document.querySelector('input[name="terrain_id"]:checked');
            
            if (!selectedTerrain) {
                showAlert('warning', 'Veuillez sélectionner un terrain');
                return;
            }

            // Vérifier qu'au moins un créneau est défini
            const creneaux = document.querySelectorAll('.creneau-item');
            if (creneaux.length === 0) {
                showAlert('warning', 'Veuillez ajouter au moins un créneau de match');
                return;
            }

            // Vérifier que toutes les équipes sont remplies
            const nbEquipes = parseInt(nbEquipesSelect.value);
            const equipes = document.querySelectorAll('.equipe-item');
            if (equipes.length !== nbEquipes) {
                showAlert('warning', 'Veuillez remplir toutes les équipes');
                return;
            }

            // Désactiver le bouton
            submitBtn.disabled = true;
            document.getElementById('submitText').style.display = 'none';
            document.getElementById('submitLoading').style.display = 'inline-block';
            
            // Récupérer le gestionnaire_id
            const terrainCard = selectedTerrain.closest('.terrain-card');
            const gestionnaireId = terrainCard.getAttribute('data-gestionnaire-id');
            
            // Préparer les données
            const formData = new FormData(form);
            formData.append('terrain_id', selectedTerrain.value);
            formData.append('gestionnaire_id', gestionnaireId);
            
            // Collecter les créneaux
            const creneauxData = [];
            creneaux.forEach(creneau => {
                const date = creneau.querySelector('input[name*="[date]"]').value;
                const heureDebut = creneau.querySelector('input[name*="[heure_debut]"]').value;
                const heureFin = creneau.querySelector('input[name*="[heure_fin]"]').value;
                if (date && heureDebut && heureFin) {
                    creneauxData.push({ date, heure_debut: heureDebut, heure_fin: heureFin });
                }
            });
            formData.append('creneaux', JSON.stringify(creneauxData));
            
            // Collecter les équipes
            const equipesData = [];
            equipes.forEach(equipe => {
                const nomEquipe = equipe.querySelector('input[name*="[nom_equipe]"]').value;
                const nbrJoueurs = equipe.querySelector('input[name*="[nbr_joueurs]"]').value;
                const listeJoueurs = equipe.querySelector('textarea[name*="[liste_joueurs]"]').value;
                if (nomEquipe && nbrJoueurs) {
                    equipesData.push({
                        nom_equipe: nomEquipe,
                        nbr_joueurs: nbrJoueurs,
                        liste_joueurs: listeJoueurs ? listeJoueurs.split(',').map(j => j.trim()) : []
                    });
                }
            });
            formData.append('equipes', JSON.stringify(equipesData));

            try {
                const response = await fetch('<?= $baseUrl ?>tournoi/submitDemande', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.href = '<?= $baseUrl ?>tournoi/mesDemandes';
                    }, 2000);
                } else {
                    showAlert('error', data.message);
                    submitBtn.disabled = false;
                    document.getElementById('submitText').style.display = 'inline';
                    document.getElementById('submitLoading').style.display = 'none';
                }
            } catch (error) {
                showAlert('error', 'Erreur lors de l\'envoi de la demande');
                submitBtn.disabled = false;
                document.getElementById('submitText').style.display = 'inline';
                document.getElementById('submitLoading').style.display = 'none';
                console.error('Erreur:', error);
            }
        });

        // Afficher une alerte
        function showAlert(type, message) {
            const icons = {
                info: 'ℹ️',
                warning: '⚠️',
                success: '✅',
                error: '❌'
            };

            alertsDiv.innerHTML = `
                <div class="alert alert-${type}">
                    ${icons[type]} ${message}
                </div>
            `;

            // Scroll vers le haut
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Supprimer après 5 secondes
            setTimeout(() => {
                alertsDiv.innerHTML = '';
            }, 5000);
        }
    </script>
</body>
</html>

