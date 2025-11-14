<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;

// Récupérer les informations de l'utilisateur connecté pour la navbar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentUser = $_SESSION['user'] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tournois - Book&Play</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>css/home.css">
    <style>
    /* Navbar Style Override */
    .navbar {
        background: #0d4d3d !important;
        padding: 0.8rem 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #fff !important;
        font-size: 1.3rem;
        font-weight: 600;
    }
    .navbar-brand img {
        height: 40px;
    }
    .navbar-brand .brand-text {
        color: #b9ff00 !important;
    }
    .navbar-nav .nav-link {
        color: #fff !important;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        transition: background 0.2s;
    }
    .navbar-nav .nav-link:hover {
        background: rgba(255,255,255,0.1);
    }
    .navbar-nav .nav-link.active {
        background: #13b7c2;
        color: #fff !important;
    }
    .navbar-nav .nav-link i {
        margin-right: 0.3rem;
    }
    .navbar-nav.ms-auto .nav-link {
        color: #b9ff00 !important;
        font-size: 1.2rem;
    }
    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .tournois-page {
        background: #195156; min-height: 100vh; padding-bottom: 4vh;
    }
    .tournois-page .tournois-card {
        background: #ebf6f5;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.13);
        border: none; margin: 32px auto 0 auto;
        padding: 0 0 6px 0;
        overflow: hidden;
        max-width: 98vw;
    }
    .tournois-page table {
        background: #fff;
        border-radius: 10px; overflow: hidden;
        margin-bottom: 0;
    }
    .tournois-page thead {
        background: #13b7c2;
        color: #fff;
        font-weight: 600;
        font-size: 1.09em;
    }
    .tournois-page td, .tournois-page th {
        vertical-align: middle !important;
        border-top: none;
        border-bottom: 1.3px solid #eaf2f2;
        padding: 13px 8px 13px 8px;
    }
    .tournois-page th {border: none;}
    .tournois-page tr:last-child td { border-bottom: none; }
    .tournois-page .badge-vert {
        background: #13d558;
        color: #fff;
        border-radius: 5px;
        font-size: 0.95em;
        font-weight: 600;
        padding: 4px 16px;
    }
    .tournois-page .badge-orange {
        background: #ff9700;
        color: #fff;
        border-radius: 5px;
        font-size: 0.95em;
        font-weight: 600;
        padding: 4px 16px;
    }
    .tournois-page .btn-action {
        background: #15aaa9;
        color: #fff;
        padding: 4px 18px;
        font-size: 0.98em;
        border-radius: 7px;
        margin-right: 7px;
        border: none;
        transition: background 0.16s, color .16s;
    }
    .tournois-page .btn-action .bi {margin-right: 2px;}
    .tournois-page .btn-action:hover,
    .tournois-page .btn-action:focus {
        background: #198ca1;
        color: #fff;
    }
    .tournois-page .btn-inscrire {
        background: #ececec;
        color: #15aaa9;
        padding: 4px 14px;
        border-radius: 7px;
        font-weight: 500;
        border: none;
        font-size: 0.96em;
    }
    .tournois-page .btn-inscrire:hover,
    .tournois-page .btn-inscrire:focus {
        background: #b9ff00;
        color: #0b2e0f;
    }
    @media (max-width:900px) {
        .tournois-page .tournois-card {padding-left: 2px; padding-right: 2px;}
        .tournois-page table { font-size: 0.98em; }
    }
    </style>
</head>
<body class="tournois-page">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $baseUrl; ?>">
                <img src="<?php echo $baseUrl; ?>images/logo.png" alt="Logo">
                <span>Book<span class="brand-text">&</span><span class="brand-text">Play</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>utilisateur/dashboard">
                            Terrains
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>utilisateur/mesReservations">
                            Mes Réservations
                        </a>
                    </li>
                                        <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>facture/client">
                            <i class="fas fa-file-invoice-dollar me-1"></i> Mes Factures
                        </a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $baseUrl; ?>home/tournois">
                            <i class="fas fa-trophy"></i> Tournois
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>tournoi/create">
                            <i class="fas fa-plus-circle"></i> Demander un tournoi
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link p-0" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Mon profil">
                            <i class="bi bi-person-circle"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="profileDropdown" style="min-width: 280px;">
                            <h6 class="mb-3" style="color:#2c3e50; font-weight:700;">Informations personnelles</h6>
                            <div class="d-flex align-items-start mb-2" style="gap:.6rem; color:#7f8c8d;">
                                <i class="bi bi-person" style="color:#00bcd4;"></i>
                                <div>
                                    <div style="font-size:.8rem; opacity:.8;">Nom complet</div>
                                    <div style="font-weight:600; color:#2c3e50;">
                                        <?php 
                                        $prenom = $currentUser['prenom'] ?? '';
                                        $nom = $currentUser['nom'] ?? '';
                                        $name = trim($prenom . ' ' . $nom);
                                        if ($name === '') { $name = $currentUser['name'] ?? 'Utilisateur'; }
                                        echo htmlspecialchars($name);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-2" style="gap:.6rem; color:#7f8c8d;">
                                <i class="bi bi-envelope" style="color:#00bcd4;"></i>
                                <div>
                                    <div style="font-size:.8rem; opacity:.8;">Email</div>
                                    <div style="font-weight:600; color:#2c3e50;">
                                        <?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3" style="gap:.6rem; color:#7f8c8d;">
                                <i class="bi bi-telephone" style="color:#00bcd4;"></i>
                                <div>
                                    <div style="font-size:.8rem; opacity:.8;">Téléphone</div>
                                    <div style="font-weight:600; color:#2c3e50;">
                                        <?php 
                                        $tel = $currentUser['telephone'] ?? ($currentUser['num_tel'] ?? '');
                                        echo htmlspecialchars($tel);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a class="btn btn-outline-secondary btn-sm" href="<?php echo $baseUrl; ?>utilisateur/changePassword">Changer mot de passe</a>
                                <a class="btn btn-primary btn-sm" style="background:#b9ff00; color:#064420; border-color:#b9ff00;" href="<?php echo $baseUrl; ?>utilisateur/profil">Modifier mes informations</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>logout" title="Déconnexion">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<div class="container tournois-card p-4">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th>Nom du tournoi</th>
          <th>Date</th>
          <th>Terrain</th>
          <th>Équipes</th>
          <th>Statut</th>
          <th class="text-end pe-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tournois as $t): ?>
        <tr>
          <td><?= htmlspecialchars($t['nom_tournoi']) ?></td>
          <td><?= htmlspecialchars($t['date_debut']) ?><?= ($t['date_fin']) ? ' - '.htmlspecialchars($t['date_fin']) : '' ?></td>
          <td>
            <?= htmlspecialchars($t['nom_terrain'] ?? '—') ?>
            <?php if (!empty($t['localisation'])): ?>
              <small class="text-muted"> — <?= htmlspecialchars($t['localisation']) ?></small>
            <?php endif; ?>
          </td>
          <td><?= (int)($t['equipes_inscrites'] ?? 0) ?> / <?= (int)($t['nb_equipes'] ?? 0) ?></td>
          <td>
            <?php if (($t['statut_inscription'] ?? '') === 'disponible'): ?>
              <span class="badge-vert">Disponible</span>
            <?php else: ?>
              <span class="badge-orange">Complet</span>
            <?php endif; ?>
          </td>
          <td class="text-end pe-3">
            <?php if (($t['statut_inscription'] ?? '') === 'disponible'): ?>
              <a class="btn-action" href="<?= $baseUrl ?>tournoi/inscriptionForm/<?= (int)$t['id_tournoi'] ?>"><i class="bi bi-eye"></i> Détails</a>
              <a class="btn-inscrire" href="<?= $baseUrl ?>tournoi/inscriptionForm/<?= (int)$t['id_tournoi'] ?>">S'inscrire</a>
            <?php else: ?>
              <button class="btn-inscrire" type="button" disabled>Complet</button>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>