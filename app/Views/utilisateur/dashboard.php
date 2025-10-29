<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $baseUrl; ?>home">Book&Play</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($currentUser['name'] ?? 'User'); ?></span>
                <a class="nav-link" href="<?php echo $baseUrl; ?>auth/logout">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h2>User Dashboard</h2>
        <p>Welcome to your dashboard. Here you can manage your bookings.</p>
        
        <!-- Booking content here -->
    </div>
</body>
</html>

