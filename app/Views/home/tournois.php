<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tournois - Book&Play</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/home.css">
    <style>
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
<?php require_once __DIR__ . '/../layouts/home/navbar.php'; ?>
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
        <?php foreach ($tournois as $t) { ?>
        <tr>
          <td><?php echo htmlspecialchars($t['nom_tournoi']); ?></td>
          <td><?php echo htmlspecialchars($t['date_debut']); ?><?php echo ($t['date_fin']) ? ' - ' . htmlspecialchars($t['date_fin']) : ''; ?></td>
          <td>
            <?php echo htmlspecialchars($t['terrain'] ?? $t['localisation'] ?? '—'); ?>
          </td>
          <td><?php echo htmlspecialchars($t['nb_equipes'] ?? '-'); ?></td>
          <td>
            <?php if (isset($t['status']) && 'accepté' === strtolower($t['status'])) { ?>
              <span class="badge-vert">Ouvert</span>
            <?php } elseif (isset($t['status']) && 'complet' === strtolower($t['status'])) { ?>
              <span class="badge-orange">Complet</span>
            <?php } else { ?>
              <span class="badge-vert">Ouvert</span>
            <?php } ?>
          </td>
          <td class="text-end pe-3">
            <a class="btn-action" href="#"><i class="bi bi-eye"></i> Détails</a>
            <a class="btn-inscrire" href="#">S'inscrire</a>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
