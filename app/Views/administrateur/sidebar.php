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
      <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrl ?>Dashboad_Admin">
          <i class="bi bi-grid-3x3-gap nav-icon"></i>
          <span class="nav-text">Dashboard</span>
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrl ?>Dashboad_Admin">
          <i class="bi bi-people nav-icon"></i>
          <span class="nav-text">Gestion des Gestionnaires</span>
          <!-- <span class="nav-badge"></span> -->
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link " href="<?= $baseUrl ?>Dashboad_Admin">
          <i class="bi bi-envelope nav-icon"></i>
          <span class="nav-text">Envoyer Email</span>
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link" href="<?= $baseUrl ?>Dashboad_Admin">
          <i class="bi bi-bell nav-icon"></i>
          <span class="nav-text">Notifications</span>
          <!-- <span class="nav-badge"></span> -->
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
</script>
