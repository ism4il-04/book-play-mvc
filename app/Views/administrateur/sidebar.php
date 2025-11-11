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
      <!-- Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrl ?>dashboard/administrateur">
          <i class="bi bi-grid-3x3-gap nav-icon"></i>
          <span class="nav-text">Dashboard</span>
        </a>
      </li>
      
      <!-- Gestion des Gestionnaires -->
      <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrl ?>Gestion_gestionnaire">
          <i class="bi bi-people nav-icon"></i>
          <span class="nav-text">Gestion des Proprietaires</span>
        </a>
      </li>
      
      <!-- Newsletter avec sous-menu -->
      <li class="nav-item">
        <a class="nav-link" href="#newsletterMenu" data-bs-toggle="collapse" role="button" aria-expanded="false">
          <i class="bi bi-envelope nav-icon"></i>
          <span class="nav-text">Newsletters</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="collapse show" id="newsletterMenu">
          <ul class="nav flex-column ms-3">
            <li class="nav-item">
              <a class="nav-link" href="<?= $baseUrl ?>newsletter">
                <i class="bi bi-pencil-square nav-icon"></i>
                <span class="nav-text">Manuelle</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= $baseUrl ?>auto_newsletter">
                <i class="bi bi-robot nav-icon"></i>
                <span class="nav-text">Automatique</span>
              </a>
            </li>
          </ul>
        </div>
      </li>
      
      <!-- Notifications -->
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
  
  // Maintenir le sous-menu Newsletter ouvert si on est sur une page newsletter
  document.addEventListener('DOMContentLoaded', function() {
    const currentUrl = window.location.href;
    const newsletterMenu = document.getElementById('newsletterMenu');
    
    // Si l'URL contient 'newsletter' ou 'auto_newsletter', garder le menu ouvert
    if (currentUrl.includes('newsletter') || currentUrl.includes('auto_newsletter')) {
      if (newsletterMenu && !newsletterMenu.classList.contains('show')) {
        newsletterMenu.classList.add('show');
      }
    }
  });
</script>