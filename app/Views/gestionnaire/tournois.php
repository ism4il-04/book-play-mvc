<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;
$tournois = $tournois ?? [];
$demandes = $demandes ?? [];
$activeSection = $activeSection ?? 'tournois';
$activeSection = in_array($activeSection, ['tournois', 'demandes'], true) ? $activeSection : 'tournois';

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

$pendingDemandes = array_filter($demandes, static function ($demande) {
    return tournoi_status_class($demande['status'] ?? '') === 'en-attente';
});
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes tournois - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/navbar_gestionnaire.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/footer_gestionnaire.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/gestionnaire_tournois.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="tournoi-layout">
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
            <a href="<?php echo $baseUrl; ?>tournoi" class="nav-item active">
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

    <main class="main-content">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-trophy"></i> Tournois & Demandes</h1>
                <p>Suivez vos tournois créés et répondez aux demandes clients</p>
            </div>
            <a href="<?php echo $baseUrl; ?>tournoi/create" class="btn-primary">
                <i class="fas fa-plus-circle"></i>
                Nouveau tournoi
            </a>
        </div>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>

        <div class="tournoi-stats">
            <div class="stat-card">
                <div class="stat-icon stat-icon-primary">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo count($tournois); ?></div>
                    <div class="stat-label">Tournois programmés</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-secondary">
                    <i class="fas fa-inbox"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo count($demandes); ?></div>
                    <div class="stat-label">Demandes reçues</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-warning">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo count($pendingDemandes); ?></div>
                    <div class="stat-label">En attente de réponse</div>
                </div>
            </div>
        </div>

        <div class="section-switcher" role="tablist" aria-label="Sections tournois et demandes">
            <button type="button" class="switcher-btn<?php echo $activeSection === 'tournois' ? ' active' : ''; ?>" data-section="tournois" role="tab" aria-selected="<?php echo $activeSection === 'tournois' ? 'true' : 'false'; ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>Mes tournois</span>
                <span class="count-badge"><?php echo count($tournois); ?></span>
            </button>
            <button type="button" class="switcher-btn<?php echo $activeSection === 'demandes' ? ' active' : ''; ?>" data-section="demandes" role="tab" aria-selected="<?php echo $activeSection === 'demandes' ? 'true' : 'false'; ?>">
                <i class="fas fa-user-friends"></i>
                <span>Demandes clients</span>
                <span class="count-badge"><?php echo count($pendingDemandes); ?></span>
            </button>
        </div>

        <section class="tournoi-panel<?php echo $activeSection === 'tournois' ? ' active' : ''; ?>" id="section-tournois" role="tabpanel">
            <?php if (!empty($tournois)): ?>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Tournoi</th>
                                <th>Dates</th>
                                <th>Équipes</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tournois as $tournoi): ?>
                                <?php $statusClass = 'status-' . tournoi_status_class($tournoi['status']); ?>
                                <tr>
                                    <td>
                                        <div class="table-title"><?php echo htmlspecialchars($tournoi['nom_tournoi']); ?></div>
                                        <?php if (!empty($tournoi['slogan'])): ?>
                                            <span class="tournoi-subtitle"><?php echo htmlspecialchars($tournoi['slogan']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="table-meta"><i class="fas fa-calendar-alt"></i>
                                            <?php echo date('d/m/Y', strtotime($tournoi['date_debut'])); ?> - <?php echo date('d/m/Y', strtotime($tournoi['date_fin'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-meta"><i class="fas fa-users"></i> <?php echo (int)($tournoi['nb_equipes'] ?? 0); ?></div>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($tournoi['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a class="btn-secondary" href="<?php echo $baseUrl; ?>tournoi/manage/<?php echo $tournoi['id_tournoi']; ?>">
                                                <i class="fas fa-calendar-day"></i> Gérer
                                            </a>
                                            <a class="btn-secondary" href="<?php echo $baseUrl; ?>tournoi/details?id=<?php echo $tournoi['id_tournoi']; ?>">
                                                <i class="fas fa-eye"></i> Détails
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-trophy"></i>
                    <h3>Aucun tournoi pour le moment</h3>
                    <p>Créez votre premier tournoi pour commencer à programmer vos matchs.</p>
                    <a href="<?php echo $baseUrl; ?>tournoi/create" class="btn-primary">
                        <i class="fas fa-plus-circle"></i>
                        Créer un tournoi
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <section class="tournoi-panel<?php echo $activeSection === 'demandes' ? ' active' : ''; ?>" id="section-demandes" role="tabpanel">
            <?php if (!empty($demandes)): ?>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Tournoi</th>
                                <th>Client</th>
                                <th>Dates</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($demandes as $demande): ?>
                                <?php 
                                    $statusSlug = tournoi_status_class($demande['status'] ?? '');
                                    $statusClass = 'status-' . $statusSlug;
                                ?>
                                <tr class="demande-row <?= htmlspecialchars($statusSlug); ?>">
                                    <td>
                                        <div class="table-title"><?= htmlspecialchars($demande['nom_tournoi'] ?? ''); ?></div>
                                        <?php if (!empty($demande['slogan'])): ?>
                                            <span class="tournoi-subtitle"><?= htmlspecialchars($demande['slogan']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="table-meta"><i class="fas fa-user"></i> <?= htmlspecialchars(($demande['client_prenom'] ?? '') . ' ' . ($demande['client_nom'] ?? '')); ?></div>
                                        <div class="table-meta"><i class="fas fa-envelope"></i> <?= htmlspecialchars($demande['client_email'] ?? ''); ?></div>
                                    </td>
                                    <td>
                                        <div class="table-meta"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($demande['date_debut'])); ?> - <?= date('d/m/Y', strtotime($demande['date_fin'])); ?></div>
                                        <div class="table-meta"><i class="fas fa-users"></i> <?= htmlspecialchars($demande['nb_equipes'] ?? 0); ?> équipes</div>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $statusClass; ?>">
                                            <?= htmlspecialchars($demande['status'] ?? ''); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a class="btn-secondary" href="<?= $baseUrl; ?>tournoi/details?id=<?= $demande['id_tournoi']; ?>">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                            <?php if ($statusSlug === 'en-attente'): ?>
                                                <button class="btn-secondary" type="button" onclick="updateStatus(<?= $demande['id_tournoi']; ?>, 'accepté')">
                                                    <i class="fas fa-check"></i>
                                                    Accepter
                                                </button>
                                                <button class="btn-secondary" type="button" onclick="updateStatus(<?= $demande['id_tournoi']; ?>, 'refusé')">
                                                    <i class="fas fa-times"></i>
                                                    Refuser
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Aucune demande client</h3>
                    <p>Vous serez notifié ici dès qu'un client soumettra un nouveau tournoi.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        (function() {
            const buttons = document.querySelectorAll('.switcher-btn');
            const panels = {
                tournois: document.getElementById('section-tournois'),
                demandes: document.getElementById('section-demandes')
            };

            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    const target = button.getAttribute('data-section');

                    buttons.forEach((btn) => {
                        btn.classList.toggle('active', btn === button);
                        btn.setAttribute('aria-selected', btn === button ? 'true' : 'false');
                    });

                    Object.entries(panels).forEach(([key, panel]) => {
                        panel.classList.toggle('active', key === target);
                    });
                });
            });
        })();

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


