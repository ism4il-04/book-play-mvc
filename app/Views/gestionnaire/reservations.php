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
            <a href="<?php echo $baseUrl; ?>reservations" class="nav-item active">
                <i class="fas fa-calendar-check"></i>
                <span>Demandes de Réservation</span>
            </a>
            <a href="<?php echo $baseUrl; ?>tournois" class="nav-item">
                <i class="fas fa-trophy"></i>
                <span>Gestion des Tournois</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['name'] ?? '', 0, 1)); ?>
                </div>
                <div class="user-details">
                    <span class="user-name"><?php echo htmlspecialchars($currentUser['name'] ?? ''); ?></span>
                    <span class="user-role">Gestionnaire</span>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-navbar">
            <div class="navbar-left">
                <h1>Demandes de réservation</h1>
                <p class="subtitle">Acceptez ou refusez les demandes en attente</p>
            </div>
        </header>

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


