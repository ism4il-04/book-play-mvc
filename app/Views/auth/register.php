<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/register.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="form-container">
       
        <h1 class="form-title">
            <i class="bi bi-person-plus-fill"></i>
            Inscription
        </h1>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-list">
                <ul>
                    <li><?php echo htmlspecialchars($_GET['error']); ?></li>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo BASE_URL; ?>auth/register">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nom</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" id="name" name="name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person-badge input-icon"></i>
                        <input type="text" id="prenom" name="prenom" required>
                    </div>
                </div>
            </div>

            <div class="form-group full-width">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group full-width">
                <label for="num_tel">Téléphone</label>
                <div class="input-wrapper phone">
                    <i class="bi bi-telephone input-icon phone-icon"></i>
                    <input type="tel" id="num_tel" name="num_tel" placeholder="+212 6 XX XX XX XX" value="<?php echo htmlspecialchars($_POST['num_tel'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmer</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
            </div>

            <div class="recaptcha-wrapper">
                <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
            </div>
            
            <button type="submit" class="submit-btn">
                <i class="bi bi-check-circle"></i>
                S'inscrire
            </button>

            <div class="login-link">
                <i class="bi bi-box-arrow-in-right"></i>
                Déjà un compte ? <a href="<?php echo BASE_URL; ?>auth/login">Se connecter</a>
            </div>
        </form>
    </div>
</body>
</html>

