<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/dashboard_admin.css">
</head>
<body>
  <!-- Vertical Sidebar Navigation -->
  <nav class="sidebar-nav">
    <div class="sidebar-header">
      <a class="sidebar-brand d-flex align-items-center gap-2" href="<?= $baseUrl ?>dashboard/administrateur">
        <div class="brand-icon">
          <img src="<?php echo $baseUrl; ?>images/logo.png" alt="Book&Play" style="width:100px; height:100px;">
        </div>
        <span class="brand-text">Book<span style="color: #CEFE24;">&</span>Play</span>
      </a>
    </div>

    <div class="sidebar-content">

      <!-- Navigation Menu -->
      <ul class="nav nav-pills flex-column">
        <li class="nav-item">
          <a class="nav-link" href="<?= $baseUrl ?>dashboard/administrateur">
            <i class="bi bi-grid-3x3-gap nav-icon"></i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link" href="<?= $baseUrl ?>Gestion_gestionnaire">
            <i class="bi bi-people nav-icon"></i>
            <span class="nav-text">Gestion des Proprietaires</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link " href="<?= $baseUrl ?>dashboard/administrateur">
            <i class="bi bi-envelope nav-icon"></i>
            <span class="nav-text">Envoyer Email</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link" href="<?= $baseUrl ?>dashboard/administrateur">
            <i class="bi bi-bell nav-icon"></i>
            <span class="nav-text">Notifications</span>
          </a>
        </li>
      </ul>

    </div>

    <!-- User Profile Section -->
    <div class="sidebar-footer">
      <div class="user-profile">
        <div class="user-avatar">
          <?php 
            $userEmail = $_SESSION['user']['email'] ?? '';
            $avatarLetter = !empty($userEmail) ? strtoupper(substr($userEmail, 0, 1)) : 'A';
            echo $avatarLetter; 
          ?>
        </div>
        <div class="user-info">
          <div class="user-name" title="<?php echo htmlspecialchars($userEmail); ?>">
            <?php 
            // Tronquer l'email s'il est trop long (max 20 caractères)
            $displayEmail = strlen($userEmail) > 20 ? substr($userEmail, 0, 17) . '...' : $userEmail;
            echo htmlspecialchars($displayEmail); 
            ?>
          </div>
        </div>
        <!-- Logout Icon -->
        <a href="<?= $baseUrl ?>home" class="logout-icon" title="Retour à la page d'accueil">
          <i class=" bi bi-box-arrow-right"></i>
        </a> 
      </div>
    </div>
  </nav>

  <!-- Mobile Toggle Button (for responsive) -->
  <button class="sidebar-toggle d-lg-none" type="button" onclick="toggleSidebar()">
    <i class="bi bi-list"></i>
  </button>

  <script>
    function toggleSidebar() {
      const sidebar = document.querySelector('.sidebar-nav');
      sidebar.classList.toggle('show');
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
      const sidebar = document.querySelector('.sidebar-nav');
      const toggle = document.querySelector('.sidebar-toggle');
      
      if (window.innerWidth <= 768 && 
          !sidebar.contains(event.target) && 
          !toggle.contains(event.target)) {
        sidebar.classList.remove('show');
      }
    });
  </script>
