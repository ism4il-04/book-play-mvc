<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes de réservation - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/navbar_gestionnaire.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/footer_gestionnaire.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/dashboard_gest.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar Navigation -->
    <?php
    $activeItem = 'reservations';
    include __DIR__ . '/../components/nav_gestionnaire.php';
    ?>

    <main class="main-content">
        <!-- Top Navbar -->
        <?php
        $title = 'Demandes de réservation';
        $subtitle = 'Acceptez ou refusez les demandes en attente';
        include __DIR__ . '/../components/top_navbar_gestionnaire.php';
        ?>

        <div class="dashboard-container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" style="padding: 12px; margin-bottom: 16px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px;">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" style="padding: 12px; margin-bottom: 16px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px;">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="content-section">
                <div class="section-header" style="display:flex; align-items:center; justify-content:space-between;">
                    <h2>Liste des réservations</h2>
                    <form method="get" action="<?php echo $baseUrl; ?>reservations" style="display:flex; gap:8px; align-items:center;">
                        <select name="status" class="form-select">
                            <?php
                                $statuses = [
                                    '' => 'Toutes',
                                    'en attente' => 'En attente',
                                    'accepté' => 'Acceptées',
                                    'refusé' => 'Refusées',
                                    'annulé' => 'Annulées',
                                ];
                                $current = $filter_status ?? '';
                                foreach ($statuses as $value => $label):
                            ?>
                                <option value="<?php echo $value; ?>" <?php echo ($current === $value ? 'selected' : ''); ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrer</button>
                    </form>
                </div>

                <?php if (empty($reservations)): ?>
                    <div class="no-data" style="text-align:center; padding:40px;">
                        <i class="fas fa-calendar-times" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                        <p>Aucune réservation trouvée.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive" style="background:#fff; border-radius:12px; box-shadow:0 3px 10px rgba(0,0,0,0.06); overflow:hidden;">
                        <table class="table" style="width:100%; border-collapse:collapse;">
                            <thead style="background:#f7f9fc;">
                                <tr>
                                    <th style="padding:12px; text-align:left;">Client</th>
                                    <th style="padding:12px; text-align:left;">Terrain</th>
                                    <th style="padding:12px; text-align:left;">Date</th>
                                    <th style="padding:12px; text-align:left;">Creneau</th>
                                    <th style="padding:12px; text-align:left;">Options</th>
                                    <th style="padding:12px; text-align:left;">Statut</th>
                                    <th style="padding:12px; text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $r): ?>
                                    <tr style="border-top:1px solid #eef2f7;">
                                        <td style="padding:12px;">
                                            <?php 
                                                $clientName = trim(($r['client_prenom'] ?? '') . ' ' . ($r['client_nom'] ?? ''));
                                                echo htmlspecialchars($clientName !== '' ? $clientName : ($r['client_email'] ?? ''));
                                            ?>
                                        </td>
                                        <td style="padding:12px;">
                                            <?php echo htmlspecialchars($r['nom_terrain'] ?? ('Terrain #' . $r['id_terrain'])); ?>
                                        </td>
                                        <td style="padding:12px;">
                                            <?php echo htmlspecialchars($r['date_reservation']); ?>
                                        </td>
                                        <td style="padding:12px;">
                                            <?php
                                                $hd = isset($r['creneau']) ? substr($r['creneau'], 0, 5) : '';
                                                echo htmlspecialchars(trim($hd));
                                            ?>
                                        </td>
                                        <td style="padding:12px;">
                                            <?php
                                                $opts = $r['options_selectionnees'] ?? '';
                                                echo $opts ? htmlspecialchars($opts) : '<span style="color:#95a5a6;">Aucune option</span>';
                                            ?>
                                        </td>
                                        <td style="padding:12px;">
                                            <?php
                                                $status = $r['status'] ?? 'en attente';
                                                $color = '#999';
                                                if ($status === 'en attente') $color = '#f39c12';
                                                if ($status === 'accepté') $color = '#27ae60';
                                                if ($status === 'refusé' || $status === 'annulé') $color = '#e74c3c';
                                            ?>
                                            <span class="status-badge" style="background-color: <?php echo $color; ?>; color:#fff; padding:6px 10px; border-radius:12px; font-size:12px;">
                                                <?php echo htmlspecialchars(ucfirst($status)); ?>
                                            </span>
                                        </td>
                                        <td style="padding:12px; text-align:right;">
                                            <?php if (($r['status'] ?? '') === 'en attente'): ?>
                                                <a class="btn btn-success" href="<?php echo $baseUrl; ?>reservations/accept/<?php echo (int)$r['id_reservation']; ?>" style="margin-right:8px;">
                                                    <i class="fas fa-check"></i> Accepter
                                                </a>
                                                <a class="btn btn-danger" href="<?php echo $baseUrl; ?>reservations/refuse/<?php echo (int)$r['id_reservation']; ?>">
                                                    <i class="fas fa-times"></i> Refuser
                                                </a>
                                            <?php else: ?>
                                                <span style="color:#95a5a6; font-size:12px;">Aucune action</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <footer class="main-footer">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tous droits réservés.</p>
            </div>
        </footer>
    </main>

    <script src="<?php echo $baseUrl; ?>js/dashboard_gest.js"></script>
</body>
</html>


