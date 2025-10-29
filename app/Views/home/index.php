<?php
// Load configuration
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book&Play - Book Your Football Field in Seconds!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>css/landing_style.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="bi bi-circle-fill"></i> Book<span>&</span>Play</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item"><a class="nav-link px-3" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#contact">Contact</a></li>
                <li class="nav-item"><a class="btn btn-green ms-lg-3" href="<?php echo $baseUrl; ?>auth/login">Book a Field</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <h1>Book Your Football Field in Seconds!</h1>
                <p>Easy booking, real-time availability, and flexible options — all at your fingertips.</p>
                <a href="#book" class="btn btn-green mt-3">Book a Field Now</a>
            </div>
            <div class="col-lg-6 text-center">
                <img src="<?php echo $baseUrl; ?>images/téléchargement.jpeg?v=<?php echo time(); ?>" alt="People playing football">
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section id="features">
    <div class="container">
        <h2>Why Choose Book&Play?</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card h-100">
                    <h4><i class="bi bi-calendar"></i> Choose Date & Time</h4>
                    <p>Select your preferred slot in just a few clicks.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card h-100">
                    <h4><i class="bi bi-clipboard-data"></i> Select Field Type</h4>
                    <p>Mini, medium, or large fields — natural or artificial grass.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card h-100">
                    <h4><i class="bi bi-person-gear"></i> Add Extras</h4>
                    <p>Need a referee, ball, or jerseys? Customize your game setup easily.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card h-100">
                    <h4><i class="bi bi-clock"></i> Real-Time Availability</h4>
                    <p>Instant updates — no more double bookings.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact -->
<section id="contact">
    <div class="container">
        <h2>Stay Updated</h2>
        <p>Subscribe to get notified about new fields and special offers!</p>
        <form method="POST" action="<?php echo $baseUrl; ?>home/subscribe" class="d-flex flex-column flex-sm-row justify-content-center align-items-center mt-4">
            <input type="email" name="email" placeholder="Enter your email" required class="mb-2 mb-sm-0">
            <button type="submit" name="subscribe">Subscribe</button>
        </form>

        <?php if (isset($_GET['subscribed'])) { ?>
            <p class='mt-3 bg-white text-success rounded-pill px-4 py-2 d-inline-block'>
                Thanks for subscribing!
            </p>
        <?php } ?>
    </div>
</section>

<!-- Footer -->
<footer>
    <p>© <?php echo date('Y'); ?> Book&Play. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>