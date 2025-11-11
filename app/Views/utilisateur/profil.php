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
    <title>Mon Profil - <?php echo APP_NAME; ?></title>
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
            max-width: 800px;
            margin: 0 auto;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--primary-green);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            margin: 0 auto 1rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.1);
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

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($currentUser['prenom'] ?? 'U', 0, 1)); ?>
                </div>
                <h2 style="color: var(--primary-green); margin-bottom: 0.5rem;">Mon Profil</h2>
                <p style="color: #7f8c8d;">Modifiez vos informations personnelles</p>
            </div>

            <form method="POST" action="<?php echo $baseUrl; ?>utilisateur/profil">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" 
                               value="<?php echo htmlspecialchars($currentUser['prenom'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control" 
                               value="<?php echo htmlspecialchars($currentUser['nom'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="num_tel" class="form-control" 
                           value="<?php echo htmlspecialchars($currentUser['num_tel'] ?? ''); ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <a href="<?php echo $baseUrl; ?>utilisateur/dashboard" class="btn-back">
                            Annuler
                        </a>
                    </div>
                    <div class="col-md-6 mb-2">
                        <button type="submit" class="btn-update">
                            <i class="bi bi-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>