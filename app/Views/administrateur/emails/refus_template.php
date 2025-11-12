<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refus de votre demande</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .terrain-info {
            background-color: white;
            padding: 15px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
        }
        .info-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>❌ Information importante</h1>
    </div>
    
    <div class="content">
        <p>Bonjour <strong><?= htmlspecialchars($nom) ?></strong>,</p>
        
        <p>Nous vous informons que votre demande de gestionnaire a été <strong>refusée</strong> par notre équipe d'administration.</p>
        
        <div class="terrain-info">
            <h3>📍 Informations sur votre demande</h3>
            <p><strong>Nom du terrain concerné :</strong> <?= htmlspecialchars($terrain) ?></p>
            <p><strong>Statut :</strong> <span style="color: #dc3545;">❌ Refusé</span></p>
        </div>
        
        <div class="info-box">
            <h3>📋 Que faire maintenant ?</h3>
            <p>Plusieurs raisons peuvent expliquer ce refus :</p>
            <ul>
                <li>Informations incomplètes dans votre dossier</li>
                <li>Non-conformité des documents fournis</li>
                <li>Capacité maximale de gestionnaires atteinte</li>
                <li>Autres critères administratifs</li>
            </ul>
        </div>
        
        <p>Si vous souhaitez plus d'informations sur les raisons de ce refus ou si vous pensez qu'il s'agit d'une erreur, nous vous invitons à :</p>
        
        <ul>
            <li>Vérifier les informations fournies lors de votre inscription</li>
            <li>Contacter notre service administratif pour plus de détails</li>
            <li>Soumettre une nouvelle demande si nécessaire</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="<?= BASE_URL ?>" class="btn">Retour au site</a>
        </div>
        
        <p>Nous restons à votre disposition pour toute question complémentaire.</p>
        
        <p>Cordialement,<br>
        L'équipe Book-Play MVC</p>
    </div>
    
    <div class="footer">
        <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
        <p>© <?= date('Y') ?> Book-Play MVC - Tous droits réservés</p>
    </div>
</body>
</html>