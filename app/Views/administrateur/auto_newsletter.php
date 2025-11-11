<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;
$config = $config ?? [];
$stats = $stats ?? [];
$history = $history ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Automatique - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/dashboard_admin.css">
</head>
<body>
    <?php
    $currentPage = 'newsletter';
    $userName = $currentUser['name'] ?? ($currentUser['nom'] ?? 'Admin');
    include __DIR__ . '/sidebar.php';
    ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h1 class="card-title mb-0">
                                <i class="bi bi-robot me-2"></i>
                                Newsletter Automatique
                            </h1>
                            <a href="<?php echo $baseUrl; ?>newsletter" class="btn btn-outline-primary">
                                <i class="bi bi-pencil-square me-2"></i>
                                Newsletter Manuelle
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['total_subscribers'] ?? 0; ?></div>
                        <div class="stat-label">Abonnés</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                            <i class="bi bi-send-check"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['total_sent'] ?? 0; ?></div>
                        <div class="stat-label">Total Envoyés</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="stat-number">
                            <?php echo $stats['last_sent'] ? date('d/m', strtotime($stats['last_sent'])) : 'N/A'; ?>
                        </div>
                        <div class="stat-label">Dernier Envoi</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="stat-number">
                            <?php echo $stats['next_send'] ? date('d/m', strtotime($stats['next_send'])) : 'N/A'; ?>
                        </div>
                        <div class="stat-label">Prochain Envoi</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Configuration -->
                <div class="col-lg-8 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2 class="card-title mb-0">
                                <i class="bi bi-gear me-2"></i>
                                Configuration
                            </h2>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?php echo $baseUrl; ?>auto_newsletter/saveConfig" method="POST">
                                <!-- Activation -->
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="enabled" 
                                               id="enabled" value="1" 
                                               <?php echo ($config['enabled'] ?? 0) ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="enabled">
                                            Activer la newsletter automatique
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Les newsletters seront envoyées automatiquement selon la planification ci-dessous
                                    </small>
                                </div>

                                <hr class="my-4">

                                <!-- Planification -->
                                <h5 class="mb-3">
                                    <i class="bi bi-calendar3 me-2"></i>Planification
                                </h5>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Fréquence</label>
                                        <select class="form-select" name="frequency">
                                            <option value="daily" <?php echo ($config['frequency'] ?? '') === 'daily' ? 'selected' : ''; ?>>
                                                Quotidienne
                                            </option>
                                            <option value="weekly" <?php echo ($config['frequency'] ?? 'weekly') === 'weekly' ? 'selected' : ''; ?>>
                                                Hebdomadaire
                                            </option>
                                            <option value="monthly" <?php echo ($config['frequency'] ?? '') === 'monthly' ? 'selected' : ''; ?>>
                                                Mensuelle
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Jour de la semaine</label>
                                        <select class="form-select" name="day_of_week">
                                            <?php
                                            $days = [
                                                'monday' => 'Lundi',
                                                'tuesday' => 'Mardi',
                                                'wednesday' => 'Mercredi',
                                                'thursday' => 'Jeudi',
                                                'friday' => 'Vendredi',
                                                'saturday' => 'Samedi',
                                                'sunday' => 'Dimanche'
                                            ];
                                            foreach ($days as $value => $label) {
                                                $selected = ($config['day_of_week'] ?? 'monday') === $value ? 'selected' : '';
                                                echo "<option value='$value' $selected>$label</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Heure d'envoi</label>
                                        <input type="time" class="form-control" name="send_time" 
                                               value="<?php echo $config['send_time'] ?? '09:00'; ?>">
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Contenu -->
                                <h5 class="mb-3">
                                    <i class="bi bi-list-check me-2"></i>Contenu à inclure
                                </h5>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="include_new_terrains" value="1" 
                                                   id="include_new_terrains"
                                                   <?php echo ($config['include_new_terrains'] ?? 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="include_new_terrains">
                                                <strong>Nouveaux terrains</strong><br>
                                                <small class="text-muted">Les 5 derniers terrains ajoutés</small>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="include_tournaments" value="1" 
                                                   id="include_tournaments"
                                                   <?php echo ($config['include_tournaments'] ?? 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="include_tournaments">
                                                <strong>Tournois à venir</strong><br>
                                                <small class="text-muted">Les 3 prochains tournois</small>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="include_promotions" value="1" 
                                                   id="include_promotions"
                                                   <?php echo ($config['include_promotions'] ?? 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="include_promotions">
                                                <strong>Promotions</strong><br>
                                                <small class="text-muted">Offres et codes promo</small>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="include_statistics" value="1" 
                                                   id="include_statistics"
                                                   <?php echo ($config['include_statistics'] ?? 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="include_statistics">
                                                <strong>Statistiques hebdomadaires</strong><br>
                                                <small class="text-muted">Activité de la semaine</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                               <!-- Actions -->
                                    <div class="d-flex gap-3 justify-content-end">
                                        <a href="<?php echo $baseUrl; ?>auto_newsletter/sendTest" 
                                        class="btn btn-outline-secondary">
                                            <i class="bi bi-eye me-2"></i>Test (à moi uniquement)
                                        </a>
                                        
                                        <a href="<?php echo $baseUrl; ?>auto_newsletter/sendNow" 
                                        class="btn btn-success"
                                        onclick="return confirm('⚠️ Attention !\n\nCette action va envoyer la newsletter à TOUS les abonnés maintenant.\n\nNombre d\'abonnés : <?php echo $stats['total_subscribers'] ?? 0; ?>\n\nContinuer ?')">
                                            <i class="bi bi-send-fill me-2"></i>
                                            Envoyer Maintenant à Tous
                                        </a>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-2"></i>Sauvegarder Config
                                        </button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Historique -->
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2 class="card-title mb-0">
                                <i class="bi bi-clock-history me-2"></i>
                                Historique
                            </h2>
                        </div>
                        <div class="card-body p-3" style="max-height: 600px; overflow-y: auto;">
                            <?php if (!empty($history)): ?>
                                <?php foreach ($history as $item): ?>
                                    <div class="p-3 mb-3 bg-light rounded">
                                        <h6 class="mb-2"><?php echo htmlspecialchars($item['subject']); ?></h6>
                                        <small class="text-muted d-block mb-2">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($item['sent_at'])); ?>
                                        </small>
                                        <div class="d-flex gap-3">
                                            <small class="text-success">
                                                <i class="bi bi-check-circle me-1"></i>
                                                <?php echo $item['sent_count']; ?>
                                            </small>
                                            <?php if ($item['failed_count'] > 0): ?>
                                                <small class="text-danger">
                                                    <i class="bi bi-x-circle me-1"></i>
                                                    <?php echo $item['failed_count']; ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-4 mb-3"></i>
                                    <p>Aucune newsletter envoyée</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>