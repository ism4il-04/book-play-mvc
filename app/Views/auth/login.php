<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/login.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="form-container">
        <a href="<?php echo BASE_URL; ?>home" class="back-btn">
            <i class="bi bi-arrow-left"></i>
            Retour
        </a>

        <h1 class="form-title">
            <i class="bi bi-box-arrow-in-right"></i>
            Connexion
        </h1>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo BASE_URL; ?>auth/login">
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" id="email" name="email" required placeholder="votre@email.com">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                <div class="forgot-password">
                    <a href="<?php echo BASE_URL; ?>auth/forgot-password">
                        <i class="bi bi-question-circle"></i> Mot de passe oublié ?
                    </a>
                </div>
            </div>

            <div class="recaptcha-wrapper">
                <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
            </div>
            
            <button type="submit" class="submit-btn">
                <i class="bi bi-check-circle"></i>
                Se connecter
            </button>

            <div class="register-link">
                <i class="bi bi-person-plus"></i>
                Pas encore de compte ? <a href="<?php echo BASE_URL; ?>auth/register">S'inscrire</a>
            </div>
        </form>
    </div>
</body>
</html>

