<link rel="stylesheet" href="<?php echo $baseUrl; ?>css/dashboard_admin.css">
<!-- Vertical Sidebar Navigation -->
<nav class="sidebar-nav">
  <div class="sidebar-header">
    <a class="sidebar-brand d-flex align-items-center gap-2" href="<?= $baseUrl ?>Dashboad_Admin">
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
        <a class="nav-link" href="<?= $baseUrl ?>Dashboad_Admin">
          <i class="bi bi-grid-3x3-gap nav-icon"></i>
          <span class="nav-text">Dashboard</span>
        </a>
      </li>
      
      <!-- Gestion des Gestionnaires -->
      <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrl ?>Dashboad_Admin">
          <i class="bi bi-people nav-icon"></i>
          <span class="nav-text">Gestion des Gestionnaires</span>
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
        <a class="nav-link" href="<?= $baseUrl ?>Dashboad_Admin">
          <i class="bi bi-bell nav-icon"></i>
          <span class="nav-text">Notifications</span>
        </a>
      </li>
    </ul>

  </div>

  <!-- User Profile Section with Dropdown -->
  <div class="sidebar-footer">
    <div class="user-profile">
      <div class="user-avatar">
        <?php 
          $displayName = isset($userName) ? $userName : (
            (isset($_SESSION['user']['name']) && $_SESSION['user']['name']) ? $_SESSION['user']['name'] : (
              (isset($_SESSION['user']['nom']) && $_SESSION['user']['nom']) ? $_SESSION['user']['nom'] : 'Admin'
            )
          );
          echo strtoupper(substr($displayName,0,1)); 
        ?>
      </div>
      <div class="user-info">
        <div class="user-name"><?php echo htmlspecialchars($displayName); ?></div>
        <div class="user-email">admin@bookplay.com</div>
      </div>
      
      <!-- Dropdown -->
      <div class="dropdown">
        <a class="profile-dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-three-dots-vertical"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="profileDropdown">
          <li><a class="dropdown-item text-danger" href="<?= $baseUrl ?>auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
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