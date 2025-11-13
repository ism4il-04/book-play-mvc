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
    background: linear-gradient(135deg, #fbfff5 0%, #f1faea 100%);
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

.page-title::before {
    content: '';
    display: inline-block;
    width: 6px;
    height: 26px;
    margin-right: 10px;
    border-radius: 3px;
    background: linear-gradient(135deg, #CEFE24, #b9ff00);
    vertical-align: -4px;
}

/* CTA: Nouvelle Demande (vert clair, simple & fancy) */
.btn-fancy-green {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    color: #fff;
    border: 2px solid rgba(6, 68, 32, 0.12);
    padding: 0.7rem 1.4rem;
    font-weight: 700;
    border-radius: 999px;
    letter-spacing: 0.01em;
    transition: transform 0.2s ease, box-shadow 0.3s ease, border-color 0.2s ease;
    box-shadow: 0 6px 16px rgba(46, 204, 113, 0.28), inset 0 1px 0 rgba(255,255,255,0.3);
}

.btn-fancy-green i { opacity: 0.9; }

.btn-fancy-green:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(46, 204, 113, 0.36), inset 0 1px 0 rgba(255,255,255,0.5);
    border-color: rgba(6, 68, 32, 0.2);
}

/* Bouton retour (outline) */
.btn-back-outline {
    background: #fff;
    color: var(--green-dark);
    border: 2px solid rgba(6, 68, 32, 0.18);
    padding: 0.55rem 1rem;
    border-radius: 999px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    transition: transform .15s ease, box-shadow .2s ease, border-color .2s ease;
}

.btn-back-outline:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
    border-color: rgba(6, 68, 32, 0.28);
}

/* Pas de soulignement sur les boutons (ancres) */
.btn, .btn:visited, .btn:hover { text-decoration: none !important; }

.btn-fancy-green:active { transform: translateY(0); }

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
    background: linear-gradient(135deg, #f9fbf4 0%, #eef6e6 100%);
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
   TOOLBAR (SEARCH + FILTERS)
   ============================================ */
.tournoi-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
}

.search-input {
    position: relative;
    flex: 1 1 320px;
    max-width: 520px;
}

.search-input input[type="text"] {
    width: 100%;
    padding: 0.75rem 2.5rem 0.75rem 2.5rem;
    border: 2px solid var(--border-color);
    border-radius: 999px;
    background: #fff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.search-input input[type="text"]:focus {
    outline: none;
    border-color: var(--green-primary);
    box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.08);
}

.search-input .icon-left {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.filter-pills {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.filter-pill {
    border: 1px solid var(--border-color);
    background: #fff;
    color: #495057;
    padding: 0.5rem 0.9rem;
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-pill:hover { box-shadow: var(--shadow-sm); }
.filter-pill.active {
    border-color: var(--green-primary);
    background: var(--success-bg);
    color: var(--green-dark);
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

.btn-chip {
    border: none;
    padding: 0.55rem 0.9rem;
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    color: #fff;
    transition: transform 0.15s ease, box-shadow 0.2s ease, opacity 0.2s ease;
    box-shadow: var(--shadow-sm);
}

.btn-chip i { font-size: 0.95rem; }
.btn-chip:hover { transform: translateY(-1px); box-shadow: var(--shadow-md); }
.btn-chip:disabled { opacity: 0.7; cursor: not-allowed; }

.btn-chip.btn-success { background: linear-gradient(135deg, #2ecc71, #27ae60); }
.btn-chip.btn-warning { background: linear-gradient(135deg, #f6c23e, #e0a800); }
.btn-chip.btn-danger { background: linear-gradient(135deg, #e74c3c, #c0392b); }

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

    .btn.btn-fancy-green {
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

    .tournoi-toolbar { flex-direction: column; align-items: stretch; }
    .filter-pills { flex-wrap: wrap; }
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
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-6">
                <h1 class="page-title mb-0">
                    <i class="fas fa-list me-2 text-success"></i>Mes Demandes de Tournoi
                </h1>
                <a href="<?= BASE_URL ?>tournoi/create" class="btn btn-fancy-green">
                    <i class="fas fa-plus me-2"></i>Nouvelle Demande
                </a>
            </div>
            <div class="tournoi-toolbar mb-4" style="margin-top: 1rem;">
                <div class="search-input">
                    <i class="fas fa-search icon-left"></i>
                    <input type="text" id="searchInput" placeholder="Rechercher un tournoi ou un gestionnaire..." aria-label="Rechercher">
                </div>
                <div class="filter-pills" role="tablist" aria-label="Filtrer par statut">
                    <button type="button" class="filter-pill active" data-filter="all">Tous</button>
                    <button type="button" class="filter-pill" data-filter="en attente">En attente</button>
                    <button type="button" class="filter-pill" data-filter="accepté">Accepté</button>
                    <button type="button" class="filter-pill" data-filter="refusé">Refusé</button>
                </div>
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
                                        <tr class="table-row" data-status="<?= htmlspecialchars(strtolower($status)) ?>" data-search="<?= htmlspecialchars(strtolower(($tournoi['nom_tournoi'] ?? '') . ' ' . ($tournoi['gestionnaire_prenom'] ?? '') . ' ' . ($tournoi['gestionnaire_nom'] ?? ''))) ?>">
                                            <td class="fw-semibold">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="icon-badge"><i class="fas fa-trophy"></i></span>
                                                    <div>
                                                        <div><?= htmlspecialchars($tournoi['nom_tournoi']) ?></div>
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
                                                    <a href="<?= BASE_URL ?>tournoi/details/<?= $tournoi['id_tournoi'] ?>" class="btn btn-chip btn-success">
                                                        <i class="fas fa-eye"></i>Détails
                                                    </a>
                                                    <?php if ($status === 'en attente'): ?>
                                                        <a href="<?= BASE_URL ?>tournoi/edit/<?= $tournoi['id_tournoi'] ?>" class="btn btn-chip btn-warning">
                                                            <i class="fas fa-edit"></i>Modifier
                                                        </a>
                                                        <button type="button" class="btn btn-chip btn-danger js-cancel-demande" data-tournoi-id="<?= $tournoi['id_tournoi'] ?>">
                                                            <i class="fas fa-trash"></i>Annuler
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

<div class="container">
    <div class="row">
        <div class="col-12 d-flex justify-content-start my-5">
            <button type="button" class="btn btn-back-outline" onclick="history.back()">
                <i class="fas fa-arrow-left"></i> Retour
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const filterButtons = document.querySelectorAll('.filter-pill');
    const rows = Array.from(document.querySelectorAll('tbody .table-row'));
    const cancelButtons = document.querySelectorAll('.js-cancel-demande');
    let activeFilter = 'all';

    cancelButtons.forEach(btn => {
        btn.addEventListener('click', async () => {
            const tournoiId = btn.getAttribute('data-tournoi-id');
            if (!tournoiId) return;
            try {
                btn.disabled = true;
                const data = new FormData();
                data.append('tournoi_id', tournoiId);
                const res = await fetch('<?= BASE_URL ?>tournoi/deleteDemande', { method: 'POST', body: data });
                const out = await res.json();
                if (out.success) {
                    window.location.reload();
                } else {
                    alert(out.message || 'Une erreur est survenue.');
                    btn.disabled = false;
                }
            } catch (e) {
                alert('Une erreur est survenue.');
                btn.disabled = false;
            }
        });
    });

    function applyFilters() {
        const q = (searchInput?.value || '').trim().toLowerCase();
        rows.forEach(tr => {
            const status = tr.getAttribute('data-status') || '';
            const hay = tr.getAttribute('data-search') || '';
            const byStatus = activeFilter === 'all' || status === activeFilter;
            const bySearch = !q || hay.includes(q);
            tr.style.display = (byStatus && bySearch) ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', applyFilters);
    filterButtons.forEach(btn => btn.addEventListener('click', () => {
        filterButtons.forEach(b => b.classList.toggle('active', b === btn));
        activeFilter = (btn.getAttribute('data-filter') || 'all').toLowerCase();
        applyFilters();
    }));

    // Injecter le nouveau tournoi depuis sessionStorage (si présent)
    try {
        const raw = sessionStorage.getItem('new_tournoi');
        if (raw) {
            const t = JSON.parse(raw);
            sessionStorage.removeItem('new_tournoi');

            const status = (t.status || 'en attente').toLowerCase();
            const statusClass = status === 'accepté' ? 'badge-accepted' : status === 'refusé' ? 'badge-rejected' : 'badge-pending';

            // Trouver/Créer tbody
            let tbody = document.querySelector('table tbody');
            if (!tbody) {
                const container = document.querySelector('.container .card.shadow-sm') || document.querySelector('.container');
                const card = document.createElement('div');
                card.className = 'card shadow-sm';
                const cardBody = document.createElement('div');
                cardBody.className = 'card-body p-0';
                const wrap = document.createElement('div');
                wrap.className = 'table-responsive';
                const table = document.createElement('table');
                table.className = 'table table-hover table-borderless align-middle mb-0';
                table.innerHTML = `
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
                    <tbody></tbody>
                `;
                wrap.appendChild(table);
                cardBody.appendChild(wrap);
                card.appendChild(cardBody);
                const empty = document.querySelector('.empty-state');
                if (empty && empty.parentElement) {
                    empty.parentElement.replaceWith(card);
                } else if (container) {
                    container.appendChild(card);
                }
                tbody = table.querySelector('tbody');
            }

            if (tbody) {
                const tr = document.createElement('tr');
                const searchStr = `${(t.nom_tournoi||'')} ${(t.gestionnaire_prenom||'')} ${(t.gestionnaire_nom||'')}`.toLowerCase();
                tr.className = 'table-row';
                tr.setAttribute('data-status', status);
                tr.setAttribute('data-search', searchStr);
                tr.innerHTML = `
                    <td class="fw-semibold">
                        <div class="d-flex align-items-center gap-3">
                            <span class="icon-badge"><i class="fas fa-trophy"></i></span>
                            <div><div>${escapeHtml(t.nom_tournoi || '')}</div></div>
                        </div>
                    </td>
                    <td>${escapeHtml(((t.gestionnaire_prenom||'') + ' ' + (t.gestionnaire_nom||'')).trim())}</td>
                    <td>
                        <div class="d-flex flex-column">
                            <span>${formatDate(t.date_debut)}</span>
                            <small class="text-muted">au ${formatDate(t.date_fin)}</small>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill bg-light text-success fw-semibold">${Number(t.nb_equipes||0)} équipes</span>
                    </td>
                    <td>
                        <span class="status-chip ${statusClass}">${status === 'accepté' ? 'Accepté' : status === 'refusé' ? 'Refusé' : 'En attente'}</span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <a href="<?= BASE_URL ?>tournoi/details/${Number(t.id_tournoi||0)}" class="btn btn-chip btn-success"><i class="fas fa-eye"></i>Détails</a>
                            <a href="<?= BASE_URL ?>tournoi/edit/${Number(t.id_tournoi||0)}" class="btn btn-chip btn-warning"><i class="fas fa-edit"></i>Modifier</a>
                            <button type="button" class="btn btn-chip btn-danger js-cancel-demande" data-tournoi-id="${Number(t.id_tournoi||0)}"><i class="fas fa-trash"></i>Annuler</button>
                        </div>
                    </td>
                `;
                tbody.prepend(tr);

                const newCancel = tr.querySelector('.js-cancel-demande');
                if (newCancel) {
                    newCancel.addEventListener('click', async () => {
                        const tournoiId = newCancel.getAttribute('data-tournoi-id');
                        if (!tournoiId) return;
                        try {
                            newCancel.disabled = true;
                            const data = new FormData();
                            data.append('tournoi_id', tournoiId);
                            const res = await fetch('<?= BASE_URL ?>tournoi/deleteDemande', { method: 'POST', body: data });
                            const out = await res.json();
                            if (out.success) {
                                tr.remove();
                            } else {
                                alert(out.message || 'Une erreur est survenue.');
                                newCancel.disabled = false;
                            }
                        } catch (e) {
                            alert('Une erreur est survenue.');
                            newCancel.disabled = false;
                        }
                    });
                }
            }
        }
    } catch (e) {}

    applyFilters();

    function escapeHtml(str) {
        return String(str).replace(/[&<>"]{1}/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s]));
    }
    function formatDate(s) {
        if (!s) return '';
        try { const d = new Date(s); return d.toLocaleDateString('fr-FR'); } catch { return s; }
    }
});
</script>