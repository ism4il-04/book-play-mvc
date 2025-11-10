<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #<?= $facture['num_facture'] ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .facture-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        
        /* En-tête */
        .facture-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            position: relative;
        }
        
        .facture-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 20px;
            background: white;
            border-radius: 20px 20px 0 0;
        }
        
        .logo-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        
        .facture-badge {
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 14px;
            backdrop-filter: blur(10px);
        }
        
        .facture-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .info-box h3 {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-box p {
            font-size: 18px;
            font-weight: 600;
        }
        
        /* Corps de la facture */
        .facture-body {
            padding: 40px;
        }
        
        .parties {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .partie {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .partie h3 {
            color: #667eea;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        
        .partie p {
            color: #333;
            line-height: 1.8;
            margin: 5px 0;
        }
        
        .partie strong {
            color: #000;
            font-weight: 600;
        }
        
        /* Détails de la réservation */
        .reservation-details {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .reservation-details h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .reservation-details h3::before {
            content: '⚽';
            font-size: 24px;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .detail-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 16px;
            color: #333;
            font-weight: 600;
        }
        
        /* Tableau des montants */
        .montants-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #e9ecef;
        }
        
        .montant-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 25px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .montant-row:last-child {
            border-bottom: none;
        }
        
        .montant-label {
            color: #666;
            font-size: 15px;
        }
        
        .montant-value {
            font-weight: 600;
            color: #333;
            font-size: 15px;
        }
        
        .total-row {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .total-row .montant-label {
            color: white;
            font-size: 18px;
            font-weight: 600;
        }
        
        .total-row .montant-value {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        /* Options supplémentaires */
        .options-section {
            margin: 30px 0;
        }
        
        .options-section h4 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .options-list {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        
        .option-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #dee2e6;
        }
        
        .option-item:last-child {
            border-bottom: none;
        }
        
        /* Pied de page */
        .facture-footer {
            background: #f8f9fa;
            padding: 30px 40px;
            text-align: center;
            color: #666;
            font-size: 13px;
            line-height: 1.8;
        }
        
        .footer-note {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #667eea;
            text-align: left;
        }
        
        /* Boutons d'action */
        .actions {
            padding: 20px 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
            background: white;
            border-top: 2px solid #e9ecef;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }
        
        /* Statut badge */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-accepte {
            background: #d4edda;
            color: #155724;
        }
        
        .status-attente {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-refuse {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Impression */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .facture-container {
                box-shadow: none;
                border-radius: 0;
            }
            
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="facture-container">
        <!-- En-tête -->
        <div class="facture-header">
            <div class="logo-section">
                <div class="logo">BOOK&PLAY</div>
                <div class="facture-badge">FACTURE</div>
            </div>
            
            <div class="facture-info">
                <div class="info-box">
                    <h3>Numéro de Facture</h3>
                    <p>#<?= str_pad(($facture['num_facture'] ?? 0), 6, '0', STR_PAD_LEFT) ?></p>
                </div>
                <div class="info-box">
                    <h3>Date de Facturation</h3>
                    <p><?= !empty($facture['date_facturation']) ? date('d/m/Y', strtotime($facture['date_facturation'])) : date('d/m/Y') ?></p>
                </div>
            </div>
        </div>
        
        <!-- Corps -->
        <div class="facture-body">
            <!-- Parties -->
            <div class="parties">
                <!-- Émetteur -->
                <div class="partie">
                    <h3>📍 Émetteur</h3>
                    <p><strong><?= htmlspecialchars($terrain['nom_terrain'] ?? 'N/A') ?></strong></p>
                    <p><?= htmlspecialchars($terrain['localisation'] ?? 'N/A') ?></p>
                    <p><strong>Gestionnaire:</strong> <?= htmlspecialchars(($gestionnaire['prenom'] ?? '') . ' ' . ($gestionnaire['nom'] ?? '')) ?: 'N/A' ?></p>
                    <p><strong>Tél:</strong> <?= htmlspecialchars($gestionnaire['num_tel'] ?? 'N/A') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($gestionnaire['email'] ?? 'N/A') ?></p>
                </div>
                
                <!-- Client -->
                <div class="partie">
                    <h3>👤 Client</h3>
                    <p><strong><?= htmlspecialchars(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')) ?: 'N/A' ?></strong></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($client['email'] ?? 'N/A') ?></p>
                    <p><strong>Tél:</strong> <?= htmlspecialchars($client['num_tel'] ?? 'N/A') ?></p>
                    <p>
                        <span class="status-badge status-<?= ($reservation['status'] ?? 'en attente') === 'accepté' ? 'accepte' : (($reservation['status'] ?? 'en attente') === 'en attente' ? 'attente' : 'refuse') ?>">
                            <?= strtoupper($reservation['status'] ?? 'en attente') ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <!-- Détails de la réservation -->
            <div class="reservation-details">
                <h3>Détails de la Réservation</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Terrain</span>
                        <span class="detail-value"><?= htmlspecialchars($terrain['nom_terrain'] ?? 'N/A') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Type de Terrain</span>
                        <span class="detail-value"><?= htmlspecialchars($terrain['type_terrain'] ?? 'N/A') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Format</span>
                        <span class="detail-value"><?= htmlspecialchars($terrain['format_terrain'] ?? 'N/A') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date de Réservation</span>
                        <span class="detail-value"><?= !empty($reservation['date_reservation']) ? date('d/m/Y', strtotime($reservation['date_reservation'])) : 'N/A' ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Créneau Horaire</span>
                        <span class="detail-value"><?= !empty($reservation['creneau']) ? date('H:i', strtotime($reservation['creneau'])) : 'N/A' ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Type de Réservation</span>
                        <span class="detail-value"><?= ucfirst($reservation['type'] ?? 'normal') ?></span>
                    </div>
                </div>
                
                <?php if (!empty($reservation['commentaire'])): ?>
                <div class="detail-item" style="margin-top: 15px; grid-column: 1 / -1;">
                    <span class="detail-label">Commentaire</span>
                    <span class="detail-value"><?= htmlspecialchars($reservation['commentaire']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Options supplémentaires (si disponibles) -->
            <?php if (!empty($options)): ?>
            <div class="options-section">
                <h4>🎯 Options Supplémentaires</h4>
                <div class="options-list">
                    <?php foreach ($options as $option): ?>
                    <div class="option-item">
                        <span><?= htmlspecialchars($option['nom_option'] ?? 'Option') ?></span>
                        <span><strong><?= number_format(($option['prix'] ?? 0), 2, ',', ' ') ?> DH</strong></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Tableau des montants -->
            <div class="montants-table">
                <div class="montant-row">
                    <span class="montant-label">Prix du Terrain (par heure)</span>
                    <span class="montant-value"><?= number_format(($terrain['prix_heure'] ?? 0), 2, ',', ' ') ?> DH</span>
                </div>
                
                <?php if (!empty($options)): ?>
                <div class="montant-row">
                    <span class="montant-label">Options Supplémentaires</span>
                    <span class="montant-value"><?= number_format(($total_options ?? 0), 2, ',', ' ') ?> DH</span>
                </div>
                <?php endif; ?>
                
                <div class="montant-row">
                    <span class="montant-label">Sous-total HT</span>
                    <span class="montant-value"><?= number_format((($facture['TTC'] ?? 0) / 1.20), 2, ',', ' ') ?> DH</span>
                </div>
                
                <div class="montant-row">
                    <span class="montant-label">TVA (20%)</span>
                    <span class="montant-value"><?= number_format((($facture['TTC'] ?? 0) - (($facture['TTC'] ?? 0) / 1.20)), 2, ',', ' ') ?> DH</span>
                </div>
                
                <div class="montant-row total-row">
                    <span class="montant-label">TOTAL TTC</span>
                    <span class="montant-value"><?= number_format(($facture['TTC'] ?? 0), 2, ',', ' ') ?> DH</span>
                </div>
            </div>
        </div>
        
        <!-- Pied de page -->
        <div class="facture-footer">
            <p><strong>Book&Play</strong> - Plateforme de Réservation de Terrains de Sport</p>
            <p>Merci pour votre confiance !</p>
            
            <div class="footer-note">
                <strong> Note:</strong> Cette facture est générée automatiquement.
                Pour toute question concernant cette facture, veuillez contacter le gestionnaire du terrain.
            </div>
        </div>
        
        <!-- Actions -->
        <div class="actions">
            <button class="btn btn-primary" onclick="window.print()">
                🖨️ Imprimer la Facture
            </button>
            <button class="btn btn-secondary" onclick="downloadPDF()">
                📥 Télécharger PDF
            </button>
        </div>
    </div>
    
    <script>
        function downloadPDF() {
            // Cette fonction peut être implémentée avec une bibliothèque comme jsPDF
            alert('Fonctionnalité de téléchargement PDF à implémenter');
        }
    </script>
</body>
</html>
