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
    <title>Profile - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $baseUrl; ?>home">Book&Play</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?php echo $baseUrl; ?>auth/profile">Profile</a>
                <a class="nav-link" href="<?php echo $baseUrl; ?>auth/logout">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <h3>User Profile</h3>
                <?php if ($currentUser) { ?>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($currentUser['name'] ?? 'N/A'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($currentUser['email'] ?? 'N/A'); ?></p>
                    <p><strong>Role:</strong> <?php echo htmlspecialchars($currentUser['role'] ?? 'N/A'); ?></p>
                <?php } else { ?>
                    <p>No user data available.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>

