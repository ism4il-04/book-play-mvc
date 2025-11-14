<?php
// Load configuration
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = rtrim(BASE_URL, '/') . '/';
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
    <style>
        /* Styles pour la section newsletter */
        .newsletter-section {
            background: linear-gradient(135deg, #064420 0%, #0a5c3c 100%);
            padding: 80px 0;
        }
        .newsletter-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .newsletter-form .form-control {
            border-radius: 50px;
            padding: 15px 25px;
            border: none;
            font-size: 1rem;
        }
        .newsletter-form .form-control:focus {
            border-color: #CEFE24;
            box-shadow: 0 0 0 0.25rem rgba(206, 254, 36, 0.25);
        }
        .newsletter-form .btn-subscribe {
            background: #CEFE24;
            color: #064420;
            font-weight: 700;
            border-radius: 50px;
            padding: 15px 30px;
            border: none;
            transition: all 0.3s ease;
        }
        .newsletter-form .btn-subscribe:hover {
            background: #b9ff00;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(206, 254, 36, 0.4);
        }
        .newsletter-benefits {
            margin-top: 3rem;
        }
        .benefit-item {
            display: flex;
            align-items: start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .benefit-icon {
            width: 50px;
            height: 50px;
            background: rgba(206, 254, 36, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .benefit-icon i {
            font-size: 24px;
            color: #CEFE24;
        }
        .benefit-text h6 {
            color: white;
            margin-bottom: 0.25rem;
            font-weight: 600;
        }
        .benefit-text small {
            color: rgba(255, 255, 255, 0.7);
        }
        .form-check-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
        }
        .form-check-label a {
            color: white;
            text-decoration: underline;
        }
        .toast {
            min-width: 300px;
        }
    </style>
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
                <li class="nav-item"><a class="nav-link px-3" href="#newsletter">Newsletter</a></li>
                <li class="nav-item"><a class="btn btn-green ms-lg-3" href="<?php echo $baseUrl; ?>auth/login">Connexion</a></li>
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
                <a href="<?php echo $baseUrl; ?>home/terrains" class="btn btn-green mt-3">Book a Field Now</a>
            </div>
            <div class="col-lg-6 text-center">
                <img src="<?php echo $baseUrl; ?>images/messi.jpg?v=<?php echo time(); ?>" alt="People playing football">
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

<!-- Newsletter Section -->
<section id="newsletter" class="newsletter-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="newsletter-content text-white text-center">
                    <h2>
                        <i class="bi bi-envelope-heart-fill me-2"></i>
                        Stay Updated!
                    </h2>
                    <p class="lead mb-4">
                        Subscribe to receive the latest news, new fields, exclusive tournaments, and special promotions directly in your inbox
                    </p>

                    <!-- Formulaire d'abonnement -->
                    <form action="index.php?url=auto_newsletter/subscribe" 
                          method="POST" 
                          class="newsletter-form"
                          onsubmit="return validateNewsletterForm()">
                        
                        <div class="row g-3 justify-content-center">
                            <div class="col-md-5">
                                <input type="text" 
                                       class="form-control" 
                                       name="nom" 
                                       id="newsletter_name"
                                       placeholder="Your name" 
                                       required>
                            </div>
                            <div class="col-md-5">
                                <input type="email" 
                                       class="form-control" 
                                       name="email" 
                                       id="newsletter_email"
                                       placeholder="your@email.com" 
                                       required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-subscribe w-100">
                                    <i class="bi bi-send-fill me-2"></i>
                                    Subscribe
                                </button>
                            </div>
                        </div>

                        <!-- Checkbox Politique de confidentialité -->
                        <div class="form-check mt-3 text-start" style="max-width: 800px; margin-left: auto; margin-right: auto;">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="accept_terms" 
                                   id="acceptTerms" 
                                   required>
                            <label class="form-check-label" for="acceptTerms">
                                I agree to receive the Book&Play newsletter and have read the 
                                <a href="<?php echo $baseUrl; ?>privacy">privacy policy</a>
                            </label>
                        </div>
                    </form>

                    <!-- Avantages de l'abonnement -->
                    <div class="newsletter-benefits">
                        <div class="row text-start">
                            <div class="col-md-4">
                                <div class="benefit-item">
                                    <div class="benefit-icon">
                                        <i class="bi bi-lightning-charge-fill"></i>
                                    </div>
                                    <div class="benefit-text">
                                        <h6>Exclusive News</h6>
                                        <small>Be the first to know</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="benefit-item">
                                    <div class="benefit-icon">
                                        <i class="bi bi-gift-fill"></i>
                                    </div>
                                    <div class="benefit-text">
                                        <h6>Exclusive Promotions</h6>
                                        <small>Promo codes for subscribers</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="benefit-item">
                                    <div class="benefit-icon">
                                        <i class="bi bi-trophy-fill"></i>
                                    </div>
                                    <div class="benefit-text">
                                        <h6>Early Access to Tournaments</h6>
                                        <small>Register first</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="mt-4 text-white-50 small mb-0">
                        <i class="bi bi-shield-check me-2"></i>
                        No spam. Unsubscribe in one click. Your data is protected.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <p>© <?php echo date('Y'); ?> Book&Play. All rights reserved.</p>
</footer>

<!-- Toast Notifications -->
<?php if (isset($_SESSION['newsletter_success'])): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div class="toast show" role="alert">
        <div class="toast-header bg-success text-white">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong class="me-auto">Success!</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            <?php echo htmlspecialchars($_SESSION['newsletter_success']); unset($_SESSION['newsletter_success']); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['newsletter_error'])): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div class="toast show" role="alert">
        <div class="toast-header bg-danger text-white">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            <?php echo htmlspecialchars($_SESSION['newsletter_error']); unset($_SESSION['newsletter_error']); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function validateNewsletterForm() {
    const nom = document.getElementById('newsletter_name').value.trim();
    const email = document.getElementById('newsletter_email').value.trim();
    const terms = document.getElementById('acceptTerms').checked;

    if (!nom || !email) {
        alert('Please fill in all fields');
        return false;
    }

    if (!terms) {
        alert('Please accept the terms and conditions');
        return false;
    }

    // Simple email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address');
        return false;
    }

    return true;
}

// Auto-hide toasts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toast => {
        setTimeout(() => {
            const bsToast = new bootstrap.Toast(toast);
            bsToast.hide();
        }, 5000);
    });
});
</script>
</body>
</html>