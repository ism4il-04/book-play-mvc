<?php
    require_once __DIR__ . '/../../../config/config.php';
    $baseUrl = BASE_URL;

    // extract() rend les variables directement disponibles
    $user = $user ?? null;
    $total = $total ?? 0;
    $actifs = $actifs ?? 0;
    $en_attente = $en_attente ?? 0;
    $refusees = $refusees ?? 0;
    $gestionnaires = $gestionnaires ?? [];
    $error = $error ?? null;

    $currentUser = $user;

    $currentPage = 'dashboard';
    include __DIR__ . '/sidebar.php';
?>

<link rel="stylesheet" href="<?php echo $baseUrl; ?>css/dashboard_admin.css">

    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h1 class="card-title mb-0">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Tableau de bord - Administrateur
                            </h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #28a745, #2ecc71);">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-number"><?php echo $total; ?></div>
                        <div class="stat-label">Total Proprietaires</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="stat-number"><?php echo $en_attente; ?></div>
                        <div class="stat-label">Demandes en attente</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #ffc107, #ffb300);">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div class="stat-number"><?php echo $actifs; ?></div>
                        <div class="stat-label">Gestionnaires actifs</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #dc3545, #e74c3c);">
                            <i class="bi bi-ban"></i>
                        </div>
                        <div class="stat-number"><?php echo $refusees; ?></div>
                        <div class="stat-label">Demandes refusées</div>
                    </div>
                </div>
            </div>

            <!-- Gestionnaires-->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="dashboard-card">
                        
                        <div class="card-body p-0">
                            <div class="table-container">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nom Complet</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody id="gestionnaires-tbody">
                                    <?php if (!empty($gestionnaires) && is_array($gestionnaires)): ?>
                                        <?php foreach ($gestionnaires as $g): ?>
                                        <tr data-gestionnaire-id="<?php echo htmlspecialchars($g['id'] ?? ''); ?>">
                                            <td>
                                                <strong>
                                                    <?php 
                                                    echo htmlspecialchars(
                                                        trim(($g['nom'] ?? '') . ' ' . ($g['prenom'] ?? ''))
                                                    ); 
                                                    ?>
                                                </strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($g['email'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($g['num_tel'] ?? ''); ?></td>
                                            <td>
                                                <?php
                                                $statut = strtolower(trim($g['statut_gestionnaire'] ?? ''));
                                                
                                                // Déterminer le badge selon le statut
                                                switch ($statut) {
                                                    case 'accepté':
                                                        $badgeClass = 'bg-success';
                                                        $icon = 'bi-check-circle';
                                                        $label = 'Accepté';
                                                        break;
                                                    case 'en attente':
                                                        $badgeClass = 'bg-warning';
                                                        $icon = 'bi-clock';
                                                        $label = 'En attente';
                                                        break;
                                                    case 'refusé':
                                                        $badgeClass = 'bg-danger';
                                                        $icon = 'bi-x-circle';
                                                        $label = 'Refusé';
                                                        break;
                                                    default:
                                                        $badgeClass = 'bg-secondary';
                                                        $icon = 'bi-question-circle';
                                                        $label = htmlspecialchars($g['status'] ?? 'Non défini');
                                                }
                                                ?>
                                                <span class="badge <?php echo $badgeClass; ?>">
                                                    <i class="bi <?php echo $icon; ?> me-1"></i><?php echo $label; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                                Aucun Proprietaires trouvé
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Script pour la surveillance en temps réel des gestionnaires -->
<script src="<?php echo $baseUrl; ?>js/gestionnaire-realtime.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales pour l'authentification
    window.userAuthenticated = <?php echo (isset($_SESSION['user']) ? 'true' : 'false'); ?>;
    window.userRole = '<?php echo $_SESSION['user']['role'] ?? ''; ?>';
    
    // Vérifier si l'utilisateur est authentifié et est admin
    <?php if (isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'administrateur'): ?>
    
    // Récupérer les IDs des gestionnaires existants
    const existingRows = document.querySelectorAll('#gestionnaires-tbody tr[data-gestionnaire-id]');
    const gestionnaireIds = Array.from(existingRows).map(row => {
        const id = row.getAttribute('data-gestionnaire-id');
        return id ? parseInt(id) : 0;
    }).filter(id => !isNaN(id) && id > 0);
    
    const maxId = gestionnaireIds.length > 0 ? Math.max(...gestionnaireIds) : 0;
    
    // Passer les IDs au script global
    window.gestionnaireIds = gestionnaireIds;
    
    // Initialiser le moniteur de gestionnaires en temps réel
    const gestionnaireMonitor = new GestionnaireRealtimeMonitor({
        baseUrl: '<?php echo $baseUrl; ?>index.php?url=',
        checkEndpoint: 'gestion_gestionnaire/checkNewGestionnaires',
        getEndpoint: 'gestion_gestionnaire/getGestionnaireById',
        containerSelector: '#gestionnaires-tbody',
        renderFunction: renderDashboardGestionnaireRow,
        pollingInterval: 4000, // Vérifier toutes les 4 secondes
        onNewGestionnaire: function(gestionnaire) {
            console.log('Nouveau gestionnaire accepté:', gestionnaire);
            // Mettre à jour les statistiques
            updateDashboardStatistics();
        },
        onGestionnaireUpdated: function(gestionnaire) {
            console.log('Gestionnaire mis à jour:', gestionnaire);
            // Mettre à jour les statistiques
            updateDashboardStatistics();
        }
    });

    // Initialiser et démarrer la surveillance
    gestionnaireMonitor.init(maxId);
    
    console.log('Surveillance des gestionnaires initialisée avec maxId:', maxId);
    
    <?php endif; ?>
});

// Fonction pour mettre à jour les statistiques du dashboard
function updateDashboardStatistics() {
    const tbody = document.querySelector('#gestionnaires-tbody');
    if (!tbody) return;

    const rows = tbody.querySelectorAll('tr[data-gestionnaire-id]');
    let total = rows.length;
    let actifs = 0;

    rows.forEach(row => {
        const badge = row.querySelector('.badge.bg-success');
        if (badge) {
            actifs++;
        }
    });

    // Mettre à jour les cartes de statistiques
    const statCards = document.querySelectorAll('.stat-number');
    if (statCards.length >= 3) {
        statCards[0].textContent = total; // Total Proprietaires
        statCards[2].textContent = actifs; // Gestionnaires actifs
    }
}
</script>

<?php include __DIR__ . '/footer.php'; ?>
