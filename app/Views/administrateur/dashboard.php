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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/dashboard_admin.css">
</head>
<body>
    <?php
    $currentPage = 'dashboard';
    $userName = $currentUser['name'] ?? ($currentUser['nom'] ?? 'Admin');
    include __DIR__ . '/sidebar.php';
    ?>
    
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
                        <div class="stat-label">Total Gestionnaires</div>
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

            <!-- Gestionnaires acceptés -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2 class="card-title mb-0">
                                <i class="bi bi-people me-2"></i>
                                Gestionnaires Acceptés
                            </h2>
                        </div>
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
                                    <tbody>
                                    <?php if (!empty($gestionnaires) && is_array($gestionnaires)): ?>
                                        <?php foreach ($gestionnaires as $g): ?>
                                        <tr>
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
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Accepté
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                                Aucun gestionnaire accepté pour le moment
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

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>