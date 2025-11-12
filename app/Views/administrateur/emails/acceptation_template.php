<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceptation de votre demande</title>
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
            background-color: #28a745;
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
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .terrain-info {
            background-color: white;
            padding: 15px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Félicitations !</h1>
    </div>
    
    <div class="content">
        <p>Bonjour <strong><?= htmlspecialchars($nom) ?></strong>,</p>
        
        <p>Nous avons le plaisir de vous informer que votre demande de gestionnaire a été <strong>acceptée</strong> par notre équipe d'administration.</p>
        
        <div class="terrain-info">
            <h3>📍 Informations sur votre terrain</h3>
            <p><strong>Nom du terrain :</strong> <?= htmlspecialchars($terrain) ?></p>
            <p><strong>Statut :</strong> <span style="color: #28a745;">✅ Activé</span></p>
        </div>
        
        <p>Vous pouvez maintenant accéder à votre espace de gestion et commencer à gérer les réservations pour votre terrain.</p>
        
        <p>Pour vous connecter, veuillez utiliser les identifiants que vous avez créés lors de votre inscription.</p>
        
        <div style="text-align: center;">
            <a href="<?= BASE_URL ?>auth/login" class="btn">Accéder à mon espace</a>
        </div>
        
        <p>Si vous avez des questions ou besoin d'assistance, n'hésitez pas à nous contacter.</p>
        
        <p>Cordialement,<br>
        L'équipe Book-Play MVC</p>
    </div>
    
    <div class="footer">
        <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
        <p>© <?= date('Y') ?> Book-Play MVC - Tous droits réservés</p>
    </div>
</body>
</html>