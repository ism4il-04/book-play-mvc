<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terrains disponibles - Book&Play</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/home.css">
</head>
<body class="terrains-page">
<?php require_once __DIR__ . '/../layouts/home/navbar.php'; ?>
<div class="container mt-4">
    <h2 class="fw-bold mb-3 text-success">Terrains disponibles</h2>
    <form class="row g-2 mb-4" method="get" action="">
        <div class="col-md-5">
            <input type="search" name="search" class="form-control bg-white text-dark" placeholder="Rechercher un terrain..." value="<?php echo htmlspecialchars($filters['search'] ?? '', ENT_QUOTES); ?>">
        </div>
        <div class="col-md-3">
            <select name="taille" class="form-select">
                <option value="">Toutes les tailles</option>
                <?php $selTaille = $filters['taille'] ?? ''; ?>
                <option value="5v5" <?php echo '5v5' === $selTaille ? 'selected' : ''; ?>>5v5</option>
                <option value="6v6" <?php echo '6v6' === $selTaille ? 'selected' : ''; ?>>6v6</option>
                <option value="7v7" <?php echo '7v7' === $selTaille ? 'selected' : ''; ?>>7v7</option>
                <option value="8v8" <?php echo '8v8' === $selTaille ? 'selected' : ''; ?>>8v8</option>
                <option value="9v9" <?php echo '9v9' === $selTaille ? 'selected' : ''; ?>>9v9</option>
                <option value="10v10" <?php echo '10v10' === $selTaille ? 'selected' : ''; ?>>10v10</option>
                <option value="11v11" <?php echo '11v11' === $selTaille ? 'selected' : ''; ?>>11v11</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">Tous les types</option>
                <?php $selType = $filters['type'] ?? ''; ?>
                <option value="Gazon naturel" <?php echo 'Gazon naturel' === $selType ? 'selected' : ''; ?>>Gazon naturel</option>
                <option value="Gazon synthétique" <?php echo 'Gazon synthétique' === $selType ? 'selected' : ''; ?>>Gazon synthétique</option>
                <option value="Terre / Sol" <?php echo 'Terre / Sol' === $selType ? 'selected' : ''; ?>>Terre / Sol</option>
                <option value="Terrain couvert / Salle" <?php echo 'Terrain couvert / Salle' === $selType ? 'selected' : ''; ?>>Terrain couvert / Salle</option>
            </select>
        </div>
        <div class="col-md-1 text-end">
            <button class="btn btn-success w-100" type="submit">Filtrer</button>
        </div>
    </form>
    <?php if (!empty($terrains)) { ?>
        <div class="row g-4">
            <?php foreach ($terrains as $terrain) { ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100">
                        <?php
                        $imageFile = isset($terrain['image']) ? trim($terrain['image']) : '';
                $safeFile = '' !== $imageFile ? basename($imageFile) : '';
                $rootDir = realpath(__DIR__ . '/../../../');
                $absolutePath = $rootDir . '/public/images/' . $safeFile;
                $imageExists = ('' !== $safeFile) && file_exists($absolutePath);
                $imageUrl = $imageExists ? ($baseUrl . 'images/' . rawurlencode($safeFile)) : ($baseUrl . 'images/terrain.png');
                $altText = htmlspecialchars($terrain['localisation'] ?? ($terrain['nom'] ?? 'Terrain'));
                ?>
                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo $altText; ?>" class="card-img-top" style="height:190px; object-fit:cover;">

                        <div class="card-body">
                            <h5 class="card-title mb-2"><?php echo htmlspecialchars($terrain['localisation'] ?? 'Lieu inconnu'); ?></h5>
                            <p class="card-text mb-1 text-muted small"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($terrain['localisation'] ?? 'Localisation inconnue'); ?></p>
                            <div class="mb-2">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($terrain['format_terrain'] ?? '-'); ?></span>
                                <span class="badge bg-info"><?php echo htmlspecialchars($terrain['type_terrain'] ?? '-'); ?></span>
                            </div>
                            <div class="small text-muted mb-1"><i class="bi bi-envelope"></i> gestionnaire@bookplay.ma</div>
                            <div class="card-footer bg-transparent border-0 px-0 mt-2">
                                <div class="row align-items-center g-2">
                                    <div class="col-7">
                                        <span class="fw-bold text-success">Prix: <?php echo htmlspecialchars($terrain['prix_heure']); ?> MAD/heure</span>
                                    </div>
                                    <div class="col-5 text-end">
                                        <a href="<?php echo $baseUrl; ?>auth/login" class="btn btn-outline-success w-100">Réserver</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-info mt-4">Aucun terrain disponible pour le moment.</div>
    <?php } ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
