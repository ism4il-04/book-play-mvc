<?php
/**
 * Vue: Mes Demandes de Tournoi (Utilisateur/Client)
 */

$demandes = $demandes ?? [];
?>

<style>
/* ============================================
   MES DEMANDES DE TOURNOI - STYLES
   ============================================ */

:root {
    --green-primary: #28a745;
    --green-dark: #064420;
    --green-darker: #0a5c3c;
    --accent-lime: #84cc16;
    --success-bg: rgba(6, 68, 32, 0.08);
    --success-text: #0b7a3c;
    --warning-bg: rgba(255, 229, 163, 0.35);
    --warning-text: #8a5800;
    --danger-bg: rgba(236, 98, 98, 0.18);
    --danger-text: #a51d1d;
    --light-bg: #f8fdf9;
    --border-color: #e5e7eb;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* ============================================
   CONTAINER & LAYOUT
   ============================================ */
body {
    font-family: "Poppins", sans-serif;
    background-color: #f6f9f8;
    color: #333;
}

.container {
    max-width: 1200px;
}

/* ============================================
   PAGE HEADER
   ============================================ */
.page-title {
    font-family: "Poppins", sans-serif;
    font-weight: 700;
    color: var(--green-dark);
    font-size: 2rem;
    letter-spacing: -0.02em;
}

.page-title i {
    font-size: 1.8rem;
    vertical-align: middle;
}

.btn-success.rounded-pill {
    background: linear-gradient(135deg, var(--green-primary) 0%, var(--green-dark) 100%);
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btn-success.rounded-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(40, 167, 69, 0.4);
    background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-darker) 100%);
}

/* ============================================
   ALERTS
   ============================================ */
.alert {
    border-radius: 12px;
    border: none;
    padding: 1rem 1.25rem;
    font-weight: 500;
    box-shadow: var(--shadow-sm);
}

.alert-success {
    background: linear-gradient(135deg, #d1f4e0 0%, #e8f8f0 100%);
    color: var(--success-text);
    border-left: 4px solid var(--green-primary);
}

.alert-danger {
    background: linear-gradient(135deg, #fde8e8 0%, #fef0f0 100%);
    color: var(--danger-text);
    border-left: 4px solid #e74c3c;
}

.alert i {
    font-size: 1.1rem;
    vertical-align: middle;
}

/* ============================================
   EMPTY STATE
   ============================================ */
.empty-state {
    border-radius: 16px;
    border: 2px dashed var(--border-color);
    background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    transition: all 0.3s ease;
}

.empty-state:hover {
    border-color: var(--green-primary);
    background: linear-gradient(135deg, #f0f9f4 0%, #e6f7ed 100%);
}

.empty-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--success-bg);
    color: var(--green-dark);
    font-size: 1.7rem;
    transition: transform 0.3s ease;
}

.empty-state:hover .empty-icon {
    transform: scale(1.1);
}

.btn-outline-success.rounded-pill {
    border: 2px solid var(--green-primary);
    color: var(--green-primary);
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-success.rounded-pill:hover {
    background: var(--green-primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* ============================================
   CARD & TABLE
   ============================================ */
.card {
    border-radius: 16px;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    background: white;
}

.table-responsive {
    border-radius: 16px;
}

.table {
    margin-bottom: 0;
}

.table thead {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.table thead th {
    border: none;
    padding: 1rem 1.25rem;
    font-weight: 600;
    font-size: 0.85rem;
    letter-spacing: 0.05em;
    color: #6c757d;
}

.table tbody td {
    padding: 1.25rem;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
}

.table-row {
    transition: all 0.3s ease;
}

.table-row:hover {
    background: var(--light-bg) !important;
    transform: translateX(2px);
}

.table-row:last-child td {
    border-bottom: none;
}

/* ============================================
   ICON BADGE
   ============================================ */
.icon-badge {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: var(--success-bg);
    color: var(--green-dark);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.table-row:hover .icon-badge {
    background: var(--green-primary);
    color: white;
    transform: scale(1.1);
}

/* ============================================
   STATUS CHIPS
   ============================================ */
.status-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 110px;
    padding: 0.5rem 0.875rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.status-chip:hover {
    transform: scale(1.05);
}

.badge-pending {
    background: var(--warning-bg);
    color: var(--warning-text);
}

.badge-accepted {
    background: rgba(44, 190, 99, 0.15);
    color: var(--success-text);
}

.badge-rejected {
    background: var(--danger-bg);
    color: var(--danger-text);
}

.badge-default {
    background: #e9ecef;
    color: #495057;
}

/* ============================================
   BADGES
   ============================================ */
.badge.rounded-pill {
    padding: 0.5rem 0.875rem;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge.bg-light.text-success {
    background: var(--success-bg) !important;
    color: var(--success-text) !important;
}

/* ============================================
   ACTION BUTTONS
   ============================================ */
.btn-group {
    gap: 0.5rem;
}

.btn-link {
    text-decoration: none;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}

.btn-link:hover {
    background: rgba(0, 0, 0, 0.05);
    transform: translateY(-1px);
}

.btn-link.text-success:hover {
    background: var(--success-bg);
    color: var(--green-primary) !important;
}

.btn-link.text-warning:hover {
    background: rgba(255, 193, 7, 0.1);
    color: #f39c12 !important;
}

.btn-link.text-danger:hover {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545 !important;
}

.btn-link i {
    font-size: 0.95rem;
}

/* ============================================
   MODAL
   ============================================ */
.modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.modal-header.bg-danger {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%) !important;
    border-radius: 16px 16px 0 0;
    padding: 1.5rem;
}

.modal-title {
    font-weight: 700;
    font-size: 1.25rem;
}

.modal-body {
    padding: 2rem;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
}

.btn-danger {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    border: none;
    font-weight: 700;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(231, 76, 60, 0.4);
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 768px) {
    .page-title {
        font-size: 1.5rem;
    }

    .page-title i {
        font-size: 1.4rem;
    }

    .btn.btn-success {
        width: 100%;
        justify-content: center;
    }

    .d-flex.flex-wrap {
        flex-direction: column;
        align-items: stretch !important;
    }

    .btn-group {
        flex-direction: column;
        gap: 0.5rem;
        width: 100%;
    }

    .btn-link {
        width: 100%;
        justify-content: center;
        text-align: center;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        flex-direction: column;
        gap: 0.75rem;
    }

    .modal-footer .btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .page-title {
        font-size: 1.3rem;
    }

    .empty-icon {
        width: 48px;
        height: 48px;
        font-size: 1.4rem;
    }

    .status-chip {
        font-size: 0.8rem;
        min-width: 90px;
        padding: 0.4rem 0.7rem;
    }
}

/* ============================================
   ANIMATIONS
   ============================================ */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.table-row {
    animation: fadeIn 0.3s ease;
}

.alert {
    animation: fadeIn 0.5s ease;
}
</style>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <h1 class="page-title mb-0">
                    <i class="fas fa-list me-2 text-success"></i>Mes Demandes de Tournoi
                </h1>
                <a href="<?= BASE_URL ?>tournoi/create" class="btn btn-success rounded-pill px-4">
                    <i class="fas fa-plus me-2"></i>Nouvelle Demande
                </a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (empty($demandes)): ?>
                <div class="card shadow-sm empty-state">
                    <div class="card-body text-center py-5">
                        <div class="empty-icon mb-3">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Aucune demande pour le moment</h5>
                        <p class="text-muted mb-4">Dès que vous créez une demande, elle apparaîtra ici.</p>
                        <a href="<?= BASE_URL ?>tournoi/create" class="btn btn-outline-success rounded-pill">
                            <i class="fas fa-plus me-2"></i>Soumettre une demande
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="text-muted text-uppercase small">
                                        <th scope="col">Tournoi</th>
                                        <th scope="col">Gestionnaire</th>
                                        <th scope="col">Dates</th>
                                        <th scope="col" class="text-center">Équipes</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($demandes as $tournoi): ?>
                                        <?php $status = $tournoi['status'] ?? 'en attente'; ?>
                                        <tr class="table-row">
                                            <td class="fw-semibold">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="icon-badge"><i class="fas fa-trophy"></i></span>
                                                    <div>
                                                        <div><?= htmlspecialchars($tournoi['nom_tournoi']) ?></div>
                                                        <small class="text-muted">ID #<?= $tournoi['id_tournoi'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars(trim($tournoi['gestionnaire_prenom'] . ' ' . $tournoi['gestionnaire_nom'])) ?>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span><?= date('d/m/Y', strtotime($tournoi['date_debut'])) ?></span>
                                                    <small class="text-muted">au <?= date('d/m/Y', strtotime($tournoi['date_fin'])) ?></small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-light text-success fw-semibold">
                                                    <?= htmlspecialchars($tournoi['nb_equipes']) ?> équipes
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                    $statusClass = [
                                                        'en attente' => 'badge-pending',
                                                        'accepté' => 'badge-accepted',
                                                        'refusé' => 'badge-rejected',
                                                    ][$status] ?? 'badge-default';
                                                    $statusText = [
                                                        'en attente' => 'En attente',
                                                        'accepté' => 'Accepté',
                                                        'refusé' => 'Refusé',
                                                    ][$status] ?? ucfirst($status);
                                                ?>
                                                <span class="status-chip <?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="<?= BASE_URL ?>tournoi/details/<?= $tournoi['id_tournoi'] ?>" class="btn btn-link btn-sm text-success">
                                                        <i class="fas fa-eye me-1"></i>Détails
                                                    </a>
                                                    <?php if ($status === 'en attente'): ?>
                                                        <a href="<?= BASE_URL ?>tournoi/edit/<?= $tournoi['id_tournoi'] ?>" class="btn btn-link btn-sm text-warning">
                                                            <i class="fas fa-edit me-1"></i>Modifier
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-link btn-sm text-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteModal"
                                                                data-tournoi-id="<?= $tournoi['id_tournoi'] ?>"
                                                                data-tournoi-nom="<?= htmlspecialchars($tournoi['nom_tournoi']) ?>">
                                                            <i class="fas fa-trash me-1"></i>Annuler
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Confirmation Suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirmer l'annulation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Êtes-vous sûr de vouloir annuler la demande de tournoi&nbsp;?</p>
                <p class="fw-semibold" id="tournoiNom"></p>
                <p class="text-muted small mb-0">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                <form id="deleteForm" method="POST" class="mb-0">
                    <input type="hidden" name="tournoi_id" id="tournoiId">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Oui, annuler
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const deleteModal = document.getElementById('deleteModal');
    const tournoiIdInput = document.getElementById('tournoiId');
    const tournoiNomSpan = document.getElementById('tournoiNom');
    const deleteForm = document.getElementById('deleteForm');

    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const tournoiId = button?.getAttribute('data-tournoi-id');
            const tournoiNom = button?.getAttribute('data-tournoi-nom') ?? '';

            tournoiIdInput.value = tournoiId ?? '';
            tournoiNomSpan.textContent = tournoiNom;
        });
    }

    if (deleteForm) {
        deleteForm.addEventListener('submit', event => {
            event.preventDefault();
            const tournoiId = tournoiIdInput.value;
            if (!tournoiId) {
                return;
            }

            const data = new FormData();
            data.append('tournoi_id', tournoiId);

            fetch('<?= BASE_URL ?>tournoi/deleteDemande', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.message || 'Une erreur est survenue.');
                }
            })
            .catch(() => alert('Une erreur est survenue.'));
        });
    }
});
</script>