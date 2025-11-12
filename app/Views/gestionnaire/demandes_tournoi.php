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
    <title>Demandes de Tournois - <?php echo APP_NAME; ?></title>
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
            <a href="<?php echo $baseUrl; ?>reservations" class="nav-item">
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
            <h1><i class="fas fa-trophy"></i> Demandes de Tournois</h1>
            <p>Gérez les demandes de tournois pour vos terrains</p>
        </div>

        <div class="demandes-container">
            <?php if (!empty($demandes)): ?>
                <?php foreach ($demandes as $demande): ?>
                    <?php
                        $statusSlug = tournoi_status_class($demande['status']);
                        $statusBadgeClass = 'status-' . $statusSlug;
                    ?>
                    <div class="demande-card <?= htmlspecialchars($statusSlug) ?>">
                        <div class="demande-header">
                            <div class="demande-title">
                                <h3><?= htmlspecialchars($demande['nom_tournoi']) ?></h3>
                                <?php if (!empty($demande['slogan'])): ?>
                                    <p class="slogan"><?= htmlspecialchars($demande['slogan']) ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="status-badge <?= $statusBadgeClass ?>">
                                <?= htmlspecialchars($demande['status']) ?>
                            </span>
                        </div>

                        <div class="client-info">
                            <strong><i class="fas fa-user"></i> Client :</strong>
                            <span><?= htmlspecialchars($demande['client_prenom'] . ' ' . $demande['client_nom']) ?></span><br>
                            <strong><i class="fas fa-envelope"></i> Email :</strong>
                            <span><?= htmlspecialchars($demande['client_email']) ?></span><br>
                            <?php if (!empty($demande['client_tel'])): ?>
                                <strong><i class="fas fa-phone"></i> Téléphone :</strong>
                                <span><?= htmlspecialchars($demande['client_tel']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="demande-info">
                            <div class="info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span><strong>Date début :</strong> <?= date('d/m/Y', strtotime($demande['date_debut'])) ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar-check"></i>
                                <span><strong>Date fin :</strong> <?= date('d/m/Y', strtotime($demande['date_fin'])) ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-users"></i>
                                <span><strong>Nombre d'équipes :</strong> <?= htmlspecialchars($demande['nb_equipes']) ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-map-marked-alt"></i>
                                <span><strong>Terrains :</strong> <?= htmlspecialchars($demande['nombre_terrains'] ?? 1) ?> terrain(s)</span>
                            </div>
                        </div>

                        <?php if (!empty($demande['prixPremiere']) || !empty($demande['prixDeuxieme']) || !empty($demande['prixTroisieme'])): ?>
                            <div class="demande-prizes">
                                <strong>🏆 Récompenses :</strong><br>
                                <?php if (!empty($demande['prixPremiere'])): ?>
                                    1ère place : <?= htmlspecialchars($demande['prixPremiere']) ?><br>
                                <?php endif; ?>
                                <?php if (!empty($demande['prixDeuxieme'])): ?>
                                    2ème place : <?= htmlspecialchars($demande['prixDeuxieme']) ?><br>
                                <?php endif; ?>
                                <?php if (!empty($demande['prixTroisieme'])): ?>
                                    3ème place : <?= htmlspecialchars($demande['prixTroisieme']) ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($demande['status'] === 'en attente'): ?>
                            <div class="demande-actions">
                                <button class="btn btn-accept" onclick="updateStatus(<?= $demande['id_tournoi'] ?>, 'accepté')">
                                    <i class="fas fa-check"></i> Accepter
                                </button>
                                <button class="btn btn-refuse" onclick="updateStatus(<?= $demande['id_tournoi'] ?>, 'refusé')">
                                    <i class="fas fa-times"></i> Refuser
                                </button>
                                <button class="btn btn-view" onclick="viewDetails(<?= $demande['id_tournoi'] ?>)">
                                    <i class="fas fa-eye"></i> Voir détails
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="demande-actions">
                                <button class="btn btn-view" onclick="viewDetails(<?= $demande['id_tournoi'] ?>)">
                                    <i class="fas fa-eye"></i> Voir détails
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-trophy"></i>
                    <h3>Aucune demande de tournoi</h3>
                    <p>Vous n'avez pas encore de demandes de tournois pour vos terrains.</p>
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

        function viewDetails(tournoiId) {
            window.location.href = '<?= $baseUrl ?>tournoi/details?id=' + tournoiId;
        }
    </script>
</body>
</html>

