<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #<?= $facture['num_facture'] ?></title>
    <style type="text/css">
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', 'Arial', sans-serif; font-size: 10pt; color: #333; line-height: 1.4; }
        .header { background: #f8f9fa; padding: 20pt; border-bottom: 2pt solid #dee2e6; }
        .logo { font-size: 24pt; font-weight: bold; color: #007bff; margin-bottom: 10pt; }
        .facture-title { font-size: 18pt; font-weight: bold; color: #495057; text-align: right; }
        .facture-info { margin-top: 15pt; display: table; width: 100%; }
        .info-row { display: table-row; }
        .info-cell { display: table-cell; padding: 3pt; vertical-align: top; }
        .info-label { font-weight: bold; color: #6c757d; font-size: 8pt; text-transform: uppercase; }
        .info-value { font-size: 10pt; }
        .content { padding: 20pt; }
        .parties { margin-bottom: 25pt; }
        .party { width: 48%; display: inline-block; vertical-align: top; }
        .party.left { margin-right: 4%; }
        .party h3 { font-size: 12pt; font-weight: bold; color: #007bff; margin-bottom: 8pt; border-bottom: 1pt solid #dee2e6; padding-bottom: 3pt; }
        .party p { margin: 2pt 0; font-size: 9pt; }
        .details-section { margin-bottom: 20pt; }
        .section-title { font-size: 12pt; font-weight: bold; color: #495057; margin-bottom: 10pt; padding-bottom: 3pt; border-bottom: 1pt solid #dee2e6; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 15pt; }
        .details-table td { padding: 5pt; border: 1pt solid #dee2e6; font-size: 9pt; }
        .details-table .label { background: #f8f9fa; font-weight: bold; width: 35%; }
        .amounts-table { width: 100%; border-collapse: collapse; margin-top: 20pt; }
        .amounts-table td { padding: 8pt; border: 1pt solid #dee2e6; font-size: 9pt; }
        .amounts-table .total { background: #007bff; color: white; font-weight: bold; font-size: 10pt; }
        .amounts-table .total td { border-color: #0056b3; }
        .footer { background: #f8f9fa; padding: 15pt; margin-top: 30pt; border-top: 1pt solid #dee2e6; text-align: center; font-size: 8pt; color: #6c757d; }
        .status { display: inline-block; padding: 2pt 8pt; border-radius: 3pt; font-size: 7pt; font-weight: bold; text-transform: uppercase; }
        .status.accepted { background: #d4edda; color: #155724; }
        .status.pending { background: #fff3cd; color: #856404; }
        .status.rejected { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; vertical-align: top;">
                <div class="logo">BOOK&amp;PLAY</div>
                <div style="font-size: 8pt; color: #6c757d; margin-top: 5pt;">Plateforme de Réservation de Terrains</div>
            </div>
            <div style="display: table-cell; text-align: right; vertical-align: top;">
                <div class="facture-title">FACTURE</div>
            </div>
        </div>
        
        <div class="facture-info">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Numéro de Facture</div>
                    <div class="info-value">#<?= str_pad(($facture['num_facture'] ?? 0), 6, '0', STR_PAD_LEFT) ?></div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Date de Facturation</div>
                    <div class="info-value"><?= !empty($facture['date_facturation']) ? date('d/m/Y', strtotime($facture['date_facturation'])) : date('d/m/Y') ?></div>
                </div>
            </div>
        </div>
    </div>
        <!-- Parties -->
        <div class="parties">
            <div class="party left">
                <h3>ÉMETTEUR</h3>
                <p><strong><?= htmlspecialchars($terrain['nom_terrain'] ?? 'N/A') ?></strong></p>
                <p><?= htmlspecialchars($terrain['localisation'] ?? 'N/A') ?></p>
                <p><strong>Gestionnaire:</strong> <?= htmlspecialchars(($gestionnaire['prenom'] ?? '') . ' ' . ($gestionnaire['nom'] ?? '')) ?: 'N/A' ?></p>
                <p><strong>Tél:</strong> <?= htmlspecialchars($gestionnaire['num_tel'] ?? 'N/A') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($gestionnaire['email'] ?? 'N/A') ?></p>
            </div>
            <div class="party">
                <h3>CLIENT</h3>
                <p><strong><?= htmlspecialchars(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')) ?: 'N/A' ?></strong></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($client['email'] ?? 'N/A') ?></p>
                <p><strong>Tél:</strong> <?= htmlspecialchars($client['num_tel'] ?? 'N/A') ?></p>
                <p><span class="status <?= ($reservation['status'] ?? 'en attente') === 'accepté' ? 'accepted' : (($reservation['status'] ?? 'en attente') === 'en attente' ? 'pending' : 'rejected') ?>">
                        <?= strtoupper($reservation['status'] ?? 'en attente') ?>
                    </span></p>
            </div>
        </div>
        <!-- Détails de la réservation -->
        <div class="details-section">
            <div class="section-title">DÉTAILS DE LA RÉSERVATION</div>
            <table class="details-table">
                <tr>
                    <td class="label">Terrain</td>
                    <td><?= htmlspecialchars($terrain['nom_terrain'] ?? 'N/A') ?></td>
                    <td class="label">Type de Terrain</td>
                    <td><?= htmlspecialchars($terrain['type_terrain'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <td class="label">Format</td>
                    <td><?= htmlspecialchars($terrain['format_terrain'] ?? 'N/A') ?></td>
                    <td class="label">Date de Réservation</td>
                    <td><?= !empty($reservation['date_reservation']) ? date('d/m/Y', strtotime($reservation['date_reservation'])) : 'N/A' ?></td>
                </tr>
                <tr>
                    <td class="label">Créneau Horaire</td>
                    <td><?= !empty($reservation['creneau']) ? date('H:i', strtotime($reservation['creneau'])) : 'N/A' ?></td>
                    <td class="label">Type de Réservation</td>
                    <td><?= ucfirst($reservation['type'] ?? 'normal') ?></td>
                </tr>
                <?php if (!empty($reservation['commentaire'])): ?>
                <tr>
                    <td class="label">Commentaire</td>
                    <td colspan="3"><?= htmlspecialchars($reservation['commentaire']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        <!-- Options supplémentaires -->
        <?php if (!empty($options)): ?>
        <div class="details-section">
            <div class="section-title">OPTIONS SUPPLÉMENTAIRES</div>
            <table class="details-table">
                <?php foreach ($options as $option): ?>
                <tr>
                    <td class="label"><?= htmlspecialchars($option['nom_option'] ?? 'Option') ?></td>
                    <td colspan="3"><?= number_format(($option['prix'] ?? 0), 2, ',', ' ') ?> DH</td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
        <!-- Tableau des montants -->
        <table class="amounts-table">
            <tr>
                <td>Prix du Terrain (par heure)</td>
                <td style="text-align: right;"><?= number_format(($terrain['prix_heure'] ?? 0), 2, ',', ' ') ?> DH</td>
            </tr>
            <?php if (!empty($options)): ?>
            <tr>
                <td>Options Supplémentaires</td>
                <td style="text-align: right;"><?= number_format(($total_options ?? 0), 2, ',', ' ') ?> DH</td>
            </tr>
            <?php endif; ?>
            <tr>
                <td>Sous-total HT</td>
                <td style="text-align: right;"><?= number_format((($facture['TTC'] ?? 0) / 1.20), 2, ',', ' ') ?> DH</td>
            </tr>
            <tr>
                <td>TVA (20%)</td>
                <td style="text-align: right;"><?= number_format((($facture['TTC'] ?? 0) - (($facture['TTC'] ?? 0) / 1.20)), 2, ',', ' ') ?> DH</td>
            </tr>
            <tr class="total">
                <td><strong>TOTAL TTC</strong></td>
                <td style="text-align: right;"><strong><?= number_format(($facture['TTC'] ?? 0), 2, ',', ' ') ?> DH</strong></td>
            </tr>
        </table>
    </div>
    <div class="footer">
        <div><strong>Book&amp;Play</strong> - Plateforme de Réservation de Terrains de Sport</div>
        <div style="margin-top: 8pt; font-size: 7pt;">Cette facture est générée automatiquement. Pour toute question, contactez le gestionnaire du terrain.</div>
    </div>
</body>
