<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-green: #064420;
            --accent-cyan: #00bcd4;
            --lime-green: #b9ff00;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: var(--primary-green) !important;
            padding: 0.8rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        
        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.4rem;
        }
        
        .brand-text {
            color: var(--lime-green) !important;
        }
        
        .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 0.5rem 1.2rem !important;
            transition: all 0.3s;
            border-radius: 5px;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .main-content {
            padding: 2rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .password-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .password-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .password-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green), var(--accent-cyan));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .password-input-group {
            position: relative;
        }
        
        .form-control {
            padding: 0.75rem 2.5rem 0.75rem 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.1);
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #7f8c8d;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0;
        }
        
        .password-toggle:hover {
            color: var(--accent-cyan);
        }
        
        .btn-update {
            background: var(--primary-green);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn-update:hover {
            background: #053318;
            color: white;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-back:hover {
            background: #5a6268;
            color: white;
        }
        
        .alert {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .password-requirements {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .password-requirements ul {
            margin: 0.5rem 0 0 0;
            padding-left: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $baseUrl; ?>">
                <span>Book<span class="brand-text">&</span><span class="brand-text">Play</span></span>
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>utilisateur/dashboard">
                        <i class="bi bi-arrow-left"></i> Retour au Dashboard
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="password-card">
            <div class="password-header">
                <div class="password-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h2 style="color: var(--primary-green); margin-bottom: 0.5rem;">Changer le mot de passe</h2>
                <p style="color: #7f8c8d;">Sécurisez votre compte avec un nouveau mot de passe</p>
            </div>

            <form method="POST" action="<?php echo $baseUrl; ?>utilisateur/changePassword" id="passwordForm">
                <div class="mb-3">
                    <label class="form-label">Mot de passe actuel *</label>
                    <div class="password-input-group">
                        <input type="password" name="current_password" id="currentPassword" class="form-control" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('currentPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe *</label>
                    <div class="password-input-group">
                        <input type="password" name="new_password" id="newPassword" class="form-control" required minlength="6">
                        <button type="button" class="password-toggle" onclick="togglePassword('newPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirmer le nouveau mot de passe *</label>
                    <div class="password-input-group">
                        <input type="password" name="confirm_password" id="confirmPassword" class="form-control" required minlength="6">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="password-requirements">
                    <strong>Exigences du mot de passe :</strong>
                    <ul>
                        <li>Au moins 6 caractères</li>
                        <li>Les deux nouveaux mots de passe doivent correspondre</li>
                    </ul>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mb-2">
                        <a href="<?php echo $baseUrl; ?>utilisateur/dashboard" class="btn-back">
                            Annuler
                        </a>
                    </div>
                    <div class="col-md-6 mb-2">
                        <button type="submit" class="btn-update">
                            <i class="bi bi-shield-check"></i> Changer le mot de passe
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // Validation côté client
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Les nouveaux mots de passe ne correspondent pas !');
                return false;
            }

            if (newPassword.length < 6) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 6 caractères !');
                return false;
            }
        });
    </script>
</body>
</html>