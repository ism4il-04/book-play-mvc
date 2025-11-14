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

                <div class="table-responsive" style="background:#fff; border-radius:12px; box-shadow:0 3px 10px rgba(0,0,0,0.06); overflow:hidden;">
                    <table class="table" style="width:100%; border-collapse:collapse;">
                        <thead style="background:#f7f9fc;">
                            <tr>
                                <th style="padding:12px; text-align:left;">Client</th>
                                <th style="padding:12px; text-align:left;">Terrain</th>
                                <th style="padding:12px; text-align:left;">Date</th>
                                <th style="padding:12px; text-align:left;">Créneau</th>
                                <th style="padding:12px; text-align:left;">Options</th>
                                <th style="padding:12px; text-align:left;">Commentaire</th>
                                <th style="padding:12px; text-align:left;">Statut</th>
                                <th style="padding:12px; text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reservationsTableBody">
                            <?php if (empty($reservations)): ?>
                                <tr>
                                    <td colspan="8" class="no-data" style="text-align:center; padding:40px;">
                                        <i class="fas fa-calendar-times" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                                        <p>Aucune réservation trouvée.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reservations as $r): ?>
                                    <tr data-reservation-id="<?php echo (int)$r['id_reservation']; ?>" style="border-top:1px solid #eef2f7;">
                                        <td style="padding:12px;">
                                            <?php 
                                                $clientName = trim(($r['client_prenom'] ?? '') . ' ' . ($r['client_nom'] ?? ''));
                                                echo htmlspecialchars($clientName !== '' ? $clientName : ($r['client_email'] ?? 'Client inconnu'));
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
                                        <td style="padding:12px;" class="comment-cell">
                                            <?php
                                                $comment = $r['commentaire'] ?? '';
                                                if ($comment) {
                                                    echo '<span title="' . htmlspecialchars($comment) . '" style="display: inline-block; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . htmlspecialchars($comment) . '</span>';
                                                } else {
                                                    echo '<span style="color:#95a5a6;">Aucun commentaire</span>';
                                                }
                                            ?>
                                        </td>
                                        <td style="padding:12px;" class="status-cell">
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
                                        <td style="padding:12px; text-align:right;" class="actions-cell">
                                            <?php if (($r['status'] ?? '') === 'en attente'): ?>
                                                <button class="btn btn-success btn-sm" onclick="reservationMonitor.updateStatus(<?php echo (int)$r['id_reservation']; ?>, 'accepté')" style="margin-right:8px; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
                                                    <i class="fas fa-check"></i> Accepter
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="reservationMonitor.updateStatus(<?php echo (int)$r['id_reservation']; ?>, 'refusé')" style="padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
                                                    <i class="fas fa-times"></i> Refuser
                                                </button>
                                            <?php else: ?>
                                                <span style="color:#95a5a6; font-size:12px;">Aucune action</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <footer class="main-footer">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Tous droits réservés.</p>
            </div>
        </footer>
    </main>

    <script src="<?php echo $baseUrl; ?>js/dashboard_gest.js"></script>
    <script src="<?php echo $baseUrl; ?>js/reservation-realtime.js"></script>
    <script>
        // Fonction pour rendre une ligne de réservation
        function renderReservationRow(reservation) {
            const clientName = (reservation.client_prenom || '') + ' ' + (reservation.client_nom || '');
            const displayName = clientName.trim() || reservation.client_email || 'Client inconnu';
            
            const creneau = reservation.creneau || '';
            const creneauDisplay = creneau ? creneau.substring(0, 5) : '';
            
            const options = reservation.options_selectionnees || '';
            const optionsDisplay = options || '<span style="color:#95a5a6;">Aucune option</span>';
            
            const comment = reservation.commentaire || '';
            const commentDisplay = comment 
                ? `<span title="${escapeHtml(comment)}" style="display: inline-block; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(comment)}</span>`
                : '<span style="color:#95a5a6;">Aucun commentaire</span>';
            
            const status = reservation.status || 'en attente';
            const statusColors = {
                'en attente': '#f39c12',
                'accepté': '#27ae60',
                'refusé': '#e74c3c',
                'annulé': '#e74c3c'
            };
            const statusColor = statusColors[status] || '#999';
            
            const actions = status === 'en attente' 
                ? `<button class="btn btn-success btn-sm" onclick="reservationMonitor.updateStatus(${reservation.id_reservation}, 'accepté')" style="margin-right:8px; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
                    <i class="fas fa-check"></i> Accepter
                   </button>
                   <button class="btn btn-danger btn-sm" onclick="reservationMonitor.updateStatus(${reservation.id_reservation}, 'refusé')" style="padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
                    <i class="fas fa-times"></i> Refuser
                   </button>`
                : '<span style="color:#95a5a6; font-size:12px;">Aucune action</span>';
            
            const tr = document.createElement('tr');
            tr.setAttribute('data-reservation-id', reservation.id_reservation);
            tr.style.borderTop = '1px solid #eef2f7';
            tr.innerHTML = `
                <td style="padding:12px;">${escapeHtml(displayName)}</td>
                <td style="padding:12px;">${escapeHtml(reservation.nom_terrain || 'Terrain #' + reservation.id_terrain)}</td>
                <td style="padding:12px;">${escapeHtml(reservation.date_reservation)}</td>
                <td style="padding:12px;">${escapeHtml(creneauDisplay)}</td>
                <td style="padding:12px;">${optionsDisplay}</td>
                <td style="padding:12px;" class="comment-cell">${commentDisplay}</td>
                <td style="padding:12px;" class="status-cell">
                    <span class="status-badge" style="background-color: ${statusColor}; color:#fff; padding:6px 10px; border-radius:12px; font-size:12px;">
                        ${escapeHtml(capitalizeFirst(status))}
                    </span>
                </td>
                <td style="padding:12px; text-align:right;" class="actions-cell">${actions}</td>
            `;
            return tr;
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
        
        // Initialiser le moniteur de réservations en temps réel
        const baseUrl = '<?php echo $baseUrl; ?>index.php?url=';
        const filterStatus = '<?php echo htmlspecialchars($filter_status ?? ''); ?>';
        const lastReservationId = <?php echo (int)($last_reservation_id ?? 0); ?>;
        
        const reservationMonitor = new ReservationRealtimeMonitor({
            baseUrl: baseUrl,
            checkEndpoint: 'reservations/checkNewReservations',
            getEndpoint: 'reservations/getReservationById',
            updateEndpoint: 'reservations/updateStatus',
            containerSelector: '#reservationsTableBody',
            renderFunction: renderReservationRow,
            pollingInterval: 1000,
            filterStatus: filterStatus || null
        });
        
        // Démarrer le monitoring quand le DOM est prêt
        document.addEventListener('DOMContentLoaded', function() {
            reservationMonitor.init(lastReservationId);
            
            // Mettre à jour le filtre si le select change
            const statusSelect = document.querySelector('select[name="status"]');
            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    const newStatus = this.value || null;
                    reservationMonitor.setFilterStatus(newStatus);
                    // Recharger la page avec le nouveau filtre
                    window.location.href = '<?php echo $baseUrl; ?>reservations' + (newStatus ? '?status=' + encodeURIComponent(newStatus) : '');
                });
            }
        });
        
        // Exposer globalement pour les boutons onclick
        window.reservationMonitor = reservationMonitor;
    </script>
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .btn-success {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #229954;
        }
        
        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-sm {
            font-size: 13px;
            padding: 6px 12px;
        }
        
        tr[data-reservation-id] {
            transition: background-color 0.3s ease;
        }
        
        tr[data-reservation-id]:hover {
            background-color: #f8f9fa;
        }
    </style>
</body>
</html>


