<?php
// app/views/public/tournoi_inscription.php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$estComplet = $places_restantes <= 0;
$userConnecte = isset($user) && $user['role'] === 'utilisateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($tournoi['nom_tournoi']) ?> - Inscription</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>css/home.css">
    <style>
    body {
        background: #195156;
        min-height: 100vh;
        padding-bottom: 4vh;
    }
    .inscription-container {
        max-width: 1000px;
        margin: 32px auto;
    }
    .tournoi-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .tournoi-header {
        background: linear-gradient(135deg, #13b7c2 0%, #15aaa9 100%);
        color: white;
        padding: 32px;
        position: relative;
    }
    .tournoi-header h1 {
        font-size: 2.2em;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .tournoi-header .slogan {
        font-size: 1.1em;
        opacity: 0.95;
        font-style: italic;
    }
    .tournoi-details {
        padding: 32px;
    }
    .detail-item {
        margin-bottom: 20px;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #13b7c2;
    }
    .detail-item i {
        color: #13b7c2;
        font-size: 1.3em;
        margin-right: 12px;
    }
    .detail-item strong {
        font-size: 1.05em;
        color: #333;
    }
    .form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        padding: 32px;
    }
    .form-card h2 {
        color: #13b7c2;
        font-weight: 700;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 3px solid #13b7c2;
    }
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    .form-control, .form-select {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 14px;
        transition: border-color 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #13b7c2;
        box-shadow: 0 0 0 0.2rem rgba(19, 183, 194, 0.25);
    }
    .joueur-item {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 12px;
        border: 2px solid #e0e0e0;
        position: relative;
    }
    .joueur-item .btn-remove {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #ff4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
    }
    .joueur-item .btn-remove:hover {
        background: #cc0000;
    }
    .btn-add-joueur {
        background: #13b7c2;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: background 0.2s;
    }
    .btn-add-joueur:hover {
        background: #0e9aa5;
    }
    .btn-submit {
        background: #b9ff00;
        color: #0b2e0f;
        border: none;
        padding: 14px 48px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1.1em;
        transition: all 0.2s;
    }
    .btn-submit:hover {
        background: #a3e600;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(185, 255, 0, 0.4);
    }
    .btn-submit:disabled {
        background: #ccc;
        color: #666;
        cursor: not-allowed;
    }
    .alert-warning {
        background: #fff3cd;
        border-color: #ffc107;
        color: #856404;
    }
    .badge-complet {
        background: #ff4444;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 1em;
    }
    .terrain-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../layouts/home/navbar.php'; ?>

<div class="container inscription-container">
    <!-- Détails du tournoi -->
    <div class="tournoi-card">
        <div class="tournoi-header">
            <h1><i class="bi bi-trophy-fill"></i> <?= htmlspecialchars($tournoi['nom_tournoi']) ?></h1>
            <?php if (!empty($tournoi['slogan'])): ?>
                <p class="slogan"><?= htmlspecialchars($tournoi['slogan']) ?></p>
            <?php endif; ?>
            <?php if ($estComplet): ?>
                <div class="mt-3">
                    <span class="badge-complet"><i class="bi bi-exclamation-circle"></i> Tournoi Complet</span>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="tournoi-details">
            <div class="row">
                <div class="col-md-6">
                    <?php if (!empty($tournoi['image'])): ?>
                        <img src="<?= $baseUrl ?>uploads/terrains/<?= htmlspecialchars($tournoi['image']) ?>" 
                             alt="<?= htmlspecialchars($tournoi['nom_terrain']) ?>" 
                             class="terrain-image">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <i class="bi bi-calendar-event"></i>
                        <strong>Dates:</strong><br>
                        Du <?= date('d/m/Y', strtotime($tournoi['date_debut'])) ?>
                        au <?= date('d/m/Y', strtotime($tournoi['date_fin'])) ?>
                    </div>
                    
                    <div class="detail-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <strong>Lieu:</strong><br>
                        <?= htmlspecialchars($tournoi['nom_terrain'] ?? 'À définir') ?>
                        <?php if (!empty($tournoi['localisation'])): ?>
                            <br><small class="text-muted"><?= htmlspecialchars($tournoi['localisation']) ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="detail-item">
                        <i class="bi bi-people-fill"></i>
                        <strong>Équipes:</strong><br>
                        <?= (int)$tournoi['equipes_inscrites'] ?> / <?= (int)$tournoi['nb_equipes'] ?> inscrites
                        <br><small class="text-success"><?= $places_restantes ?> place(s) restante(s)</small>
                    </div>
                    
                    <div class="detail-item">
                        <i class="bi bi-trophy"></i>
                        <strong>Récompenses:</strong><br>
                        🥇 <?= htmlspecialchars($tournoi['prixPremiere'] ?? 'Trophée') ?><br>
                        🥈 <?= htmlspecialchars($tournoi['prixDeuxieme'] ?? 'Médaille') ?><br>
                        🥉 <?= htmlspecialchars($tournoi['prixTroisieme'] ?? 'Médaille') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire d'inscription -->
    <?php if (!$estComplet): ?>
        <?php if ($userConnecte): ?>
            <div class="form-card">
                <h2><i class="bi bi-pencil-square"></i> Inscrire mon équipe</h2>
                
                <form id="inscriptionForm">
                    <input type="hidden" name="tournoi_id" value="<?= $tournoi['id_tournoi'] ?>">
                    
                    <div class="mb-4">
                        <label for="nom_equipe" class="form-label">
                            <i class="bi bi-flag-fill"></i> Nom de l'équipe *
                        </label>
                        <input type="text" class="form-control" id="nom_equipe" name="nom_equipe" required
                               placeholder="Ex: Les Lions de Tétouan">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="bi bi-people"></i> Liste des joueurs *
                        </label>
                        <div id="joueurs-container">
                            <!-- Les joueurs seront ajoutés ici -->
                        </div>
                        <button type="button" class="btn-add-joueur" onclick="ajouterJoueur()">
                            <i class="bi bi-plus-circle"></i> Ajouter un joueur
                        </button>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Assurez-vous d'avoir au moins 5 joueurs pour valider l'inscription.
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn-submit" id="btnSubmit">
                            <i class="bi bi-check-circle"></i> Valider l'inscription
                        </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Connexion requise</strong><br>
                Vous devez être connecté en tant que client pour vous inscrire à ce tournoi.
                <br><br>
                <a href="<?= $baseUrl ?>auth/login" class="btn btn-primary">Se connecter</a>
                <a href="<?= $baseUrl ?>auth/register" class="btn btn-secondary">Créer un compte</a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-danger text-center">
            <i class="bi bi-x-circle"></i>
            <strong>Tournoi complet</strong><br>
            Ce tournoi a atteint son nombre maximum d'équipes inscrites.
            <br><br>
            <a href="<?= $baseUrl ?>tournoi/listPublic" class="btn btn-primary">Voir les autres tournois</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let joueurCount = 0;

// Ajouter un joueur au formulaire
function ajouterJoueur() {
    joueurCount++;
    const container = document.getElementById('joueurs-container');
    const joueurDiv = document.createElement('div');
    joueurDiv.className = 'joueur-item';
    joueurDiv.id = 'joueur-' + joueurCount;
    joueurDiv.innerHTML = `
        <button type="button" class="btn-remove" onclick="retirerJoueur(${joueurCount})">
            <i class="bi bi-x"></i>
        </button>
        <div class="row">
            <div class="col-md-4 mb-2">
                <input type="text" class="form-control joueur-nom" placeholder="Prénom *" required>
            </div>
            <div class="col-md-4 mb-2">
                <input type="text" class="form-control joueur-prenom" placeholder="Nom *" required>
            </div>
            <div class="col-md-4 mb-2">
                <input type="number" class="form-control joueur-numero" placeholder="N° Maillot" min="1" max="99">
            </div>
        </div>
    `;
    container.appendChild(joueurDiv);
}

// Retirer un joueur
function retirerJoueur(id) {
    const element = document.getElementById('joueur-' + id);
    if (element) {
        element.remove();
    }
}

// Récupérer la liste des joueurs
function getListeJoueurs() {
    const joueurs = [];
    const joueursItems = document.querySelectorAll('.joueur-item');
    
    joueursItems.forEach(item => {
        const nom = item.querySelector('.joueur-nom').value.trim();
        const prenom = item.querySelector('.joueur-prenom').value.trim();
        const numero = item.querySelector('.joueur-numero').value;
        
        if (nom && prenom) {
            joueurs.push({
                nom: nom,
                prenom: prenom,
                numero: numero || null
            });
        }
    });
    
    return joueurs;
}

// Soumettre le formulaire
document.getElementById('inscriptionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const joueurs = getListeJoueurs();
    
    if (joueurs.length < 5) {
        alert('Vous devez inscrire au moins 5 joueurs pour valider l\'inscription.');
        return;
    }
    
    const formData = new FormData();
    formData.append('tournoi_id', document.querySelector('[name="tournoi_id"]').value);
    formData.append('nom_equipe', document.getElementById('nom_equipe').value);
    formData.append('nbr_joueurs', joueurs.length);
    formData.append('liste_joueurs', JSON.stringify(joueurs));
    
    const btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Inscription en cours...';
    
    try {
        const response = await fetch('<?= $baseUrl ?>tournoi/submitInscription', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            try {
                const payload = {
                    tournoi_id: result.tournoi_id,
                    nb_equipes: result.nb_equipes,
                    equipes_inscrites: result.equipes_inscrites,
                    nouveau_statut: result.nouveau_statut
                };
                localStorage.setItem('tournoi:update', JSON.stringify(payload));
                setTimeout(() => localStorage.removeItem('tournoi:update'), 1000);
            } catch (_) {}
            window.location.href = '<?= $baseUrl ?>tournoi/mesDemandes';
        } else {
            alert('Erreur: ' + result.message);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-check-circle"></i> Valider l\'inscription';
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de l\'inscription');
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-check-circle"></i> Valider l\'inscription';
    }
});

// Ajouter 5 joueurs par défaut au chargement
document.addEventListener('DOMContentLoaded', function() {
    for (let i = 0; i < 5; i++) {
        ajouterJoueur();
    }
});
</script>
</body>
</html>