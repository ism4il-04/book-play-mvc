<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;

if (!function_exists('tournoi_status_class')) {
    function tournoi_status_class(string $status): string
    {
        $transliterated = strtr($status, [
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'É' => 'e', 'È' => 'e', 'Ê' => 'e', 'Ë' => 'e',
            'à' => 'a', 'À' => 'a',
            'ù' => 'u', 'Ù' => 'u', 'û' => 'u', 'Û' => 'u',
            'î' => 'i', 'Ï' => 'i', 'ï' => 'i', 'Î' => 'i',
            'ô' => 'o', 'Ô' => 'o',
            'ç' => 'c', 'Ç' => 'c'
        ]);

        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $transliterated));
        return trim($slug, '-');
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Tournoi - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/navbar_gestionnaire.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/footer_gestionnaire.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/gestionnaire_tournois.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="tournoi-layout">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="<?php echo $baseUrl; ?>images/logo.png" alt="Logo" class="logo">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a href="<?php echo $baseUrl; ?>dashboard/gestionnaire" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo $baseUrl; ?>terrain/gestionnaireTerrains" class="nav-item">
                <i class="fas fa-map-marked-alt"></i>
                <span>Gestion des Terrains</span>
            </a>
            <a href="<?php echo $baseUrl; ?>facture" class="nav-item">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Gestion des Factures</span>
            </a>
            <a href="<?php echo $baseUrl; ?>factures" class="nav-item">
                <i class="fas fa-calendar-check"></i>
                <span>Demandes de Réservation</span>
            </a>
            <a href="<?php echo $baseUrl; ?>tournoi?section=demandes" class="nav-item active">
                <i class="fas fa-trophy"></i>
                <span>Tournois & Demandes</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['name'] ?? 'G', 0, 1)); ?>
                </div>
                <div class="user-details">
                    <span class="user-name"><?php echo htmlspecialchars($currentUser['name'] ?? 'Gestionnaire'); ?></span>
                    <span class="user-role">Gestionnaire</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-trophy"></i> <?= htmlspecialchars($tournoi['nom_tournoi']) ?></h1>
                <?php if (!empty($tournoi['slogan'])): ?>
                    <p class="page-slogan"><?= htmlspecialchars($tournoi['slogan']) ?></p>
                <?php endif; ?>
            </div>
            <?php $statusClass = 'status-' . tournoi_status_class($tournoi['status']); ?>
            <span class="status-badge <?= $statusClass ?>">
                <?= htmlspecialchars($tournoi['status']) ?>
            </span>
        </div>

        <div class="details-container">
            <!-- Informations générales -->
            <div class="detail-card">
                <h3><i class="fas fa-info-circle"></i> Informations Générales</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <div class="info-item-content">
                            <div class="info-item-label">Date de début</div>
                            <div class="info-item-value"><?= date('d/m/Y', strtotime($tournoi['date_debut'])) ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar-check"></i>
                        <div class="info-item-content">
                            <div class="info-item-label">Date de fin</div>
                            <div class="info-item-value"><?= date('d/m/Y', strtotime($tournoi['date_fin'])) ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-users"></i>
                        <div class="info-item-content">
                            <div class="info-item-label">Nombre d'équipes</div>
                            <div class="info-item-value"><?= htmlspecialchars($tournoi['nb_equipes']) ?></div>
                        </div>
                    </div>
                    <?php if ($terrain): ?>
                        <div class="info-item">
                            <i class="fas fa-map-marked-alt"></i>
                            <div class="info-item-content">
                                <div class="info-item-label">Terrain</div>
                                <div class="info-item-value"><?= htmlspecialchars($terrain['nom_terrain']) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informations client -->
            <div class="detail-card">
                <h3><i class="fas fa-user"></i> Informations Client</h3>
                <div class="client-card">
                    <div class="info-item info-item-spaced">
                        <i class="fas fa-user-circle"></i>
                        <div class="info-item-content">
                            <div class="info-item-label">Nom complet</div>
                            <div class="info-item-value"><?= htmlspecialchars($tournoi['client_prenom'] . ' ' . $tournoi['client_nom']) ?></div>
                        </div>
                    </div>
                    <div class="info-item info-item-spaced">
                        <i class="fas fa-envelope"></i>
                        <div class="info-item-content">
                            <div class="info-item-label">Email</div>
                            <div class="info-item-value"><?= htmlspecialchars($tournoi['client_email']) ?></div>
                        </div>
                    </div>
                    <?php if (!empty($tournoi['client_tel'])): ?>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <div class="info-item-content">
                                <div class="info-item-label">Téléphone</div>
                                <div class="info-item-value"><?= htmlspecialchars($tournoi['client_tel']) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Équipes participantes -->
            <div class="detail-card full-width">
                <h3><i class="fas fa-users"></i> Équipes Participantes (<?= count($equipes) ?>)</h3>
                <div class="equipe-list">
                    <?php if (!empty($equipes)): ?>
                        <?php foreach ($equipes as $equipe): ?>
                            <div class="equipe-item">
                                <div class="equipe-header">
                                    <div class="equipe-name"><?= htmlspecialchars($equipe['nom_equipe']) ?></div>
                                    <div class="equipe-joueurs">
                                        <i class="fas fa-users"></i> <?= htmlspecialchars($equipe['nbr_joueurs']) ?> joueurs
                                    </div>
                                </div>
                                <?php 
                                $joueurs = json_decode($equipe['liste_joueurs'] ?? '[]', true);
                                if (!empty($joueurs) && is_array($joueurs)):
                                ?>
                                    <div class="equipe-players">
                                        <strong class="equipe-players-title">Liste des joueurs :</strong>
                                        <div class="joueurs-badges">
                                            <?php foreach ($joueurs as $joueur): ?>
                                                <span class="joueur-badge">
                                                    <?= htmlspecialchars(trim($joueur)) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-placeholder">Aucune équipe enregistrée</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Créneaux des matchs -->
            <div class="detail-card full-width">
                <h3><i class="fas fa-clock"></i> Créneaux des Matchs (<?= count($creneaux) ?>)</h3>
                <div class="creneaux-list">
                    <?php if (!empty($creneaux)): ?>
                        <?php foreach ($creneaux as $creneau): ?>
                            <?php $creneauStatusClass = 'status-' . tournoi_status_class($creneau['status']); ?>
                            <div class="creneau-item">
                                <div>
                                    <div class="creneau-date">
                                        <i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($creneau['date_reservation'])) ?>
                                    </div>
                                    <div class="creneau-time">
                                        <i class="fas fa-clock"></i> <?= date('H:i', strtotime($creneau['creneau'])) ?>
                                    </div>
                                    <?php if (!empty($creneau['nom_terrain'])): ?>
                                        <div class="creneau-terrain">
                                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($creneau['nom_terrain']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <span class="status-badge creneau-status <?= $creneauStatusClass ?>">
                                    <?= htmlspecialchars($creneau['status']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-placeholder">Aucun créneau défini</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Prix et récompenses -->
            <?php if (!empty($tournoi['prixPremiere']) || !empty($tournoi['prixDeuxieme']) || !empty($tournoi['prixTroisieme'])): ?>
                <div class="detail-card full-width">
                    <h3><i class="fas fa-trophy"></i> Prix et Récompenses</h3>
                    <div class="prizes-section">
                        <?php if (!empty($tournoi['prixPremiere'])): ?>
                            <div class="prize-item">
                                <span class="prize-icon">🥇</span>
                                <div class="prize-text">1ère place: <?= htmlspecialchars($tournoi['prixPremiere']) ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($tournoi['prixDeuxieme'])): ?>
                            <div class="prize-item">
                                <span class="prize-icon">🥈</span>
                                <div class="prize-text">2ème place: <?= htmlspecialchars($tournoi['prixDeuxieme']) ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($tournoi['prixTroisieme'])): ?>
                            <div class="prize-item">
                                <span class="prize-icon">🥉</span>
                                <div class="prize-text">3ème place: <?= htmlspecialchars($tournoi['prixTroisieme']) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="actions-bar">
            <a href="<?= $baseUrl ?>tournoi?section=demandes" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <?php if ($tournoi['status'] === 'en attente'): ?>
                <div class="actions-group">
                    <button class="btn btn-accept" onclick="updateStatus(<?= $tournoi['id_tournoi'] ?>, 'accepté')">
                        <i class="fas fa-check"></i> Accepter le tournoi
                    </button>
                    <button class="btn btn-refuse" onclick="updateStatus(<?= $tournoi['id_tournoi'] ?>, 'refusé')">
                        <i class="fas fa-times"></i> Refuser le tournoi
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function updateStatus(tournoiId, status) {
            if (!confirm(`Êtes-vous sûr de vouloir ${status === 'accepté' ? 'accepter' : 'refuser'} ce tournoi ?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('tournoi_id', tournoiId);
            formData.append('status', status);

            fetch('<?= $baseUrl ?>tournoi/updateStatus', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        }
    </script>
</body>
</html>

