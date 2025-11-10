<?php

class AuthController extends Controller {
    public function login() {
        // If the form was submitted, process authentication
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

            if ('' === $email || '' === $password || empty($recaptchaResponse)) {
                header('Location: ' . BASE_URL . 'auth/login?error=' . urlencode('Please provide email and password and complete the captcha'));
                exit;
            }

            // Load the User model and try to authenticate
            $userModel = $this->model('User');
            $user = $userModel->authenticate($email, $password);

            if ($user) {
                // Store minimal user info in session
                $_SESSION['user'] = [
                    'id' => $user['id'] ?? null,
                    'name' => $user['name'] ?? $user['email'],
                    'email' => $user['email'] ?? $email,
                    'role' => $user['role'] ?? 'utilisateur',
                ];

                // Redirect according to role
                switch ($_SESSION['user']['role']) {
                    case 'gestionnaire':
                        header('Location: ' . BASE_URL . 'dashboard/gestionnaire');
                        break;
                    case 'administrateur':
                        header('Location: ' . BASE_URL . 'dashboard/administrateur');
                        break;
                    default:
                        header('Location: ' . BASE_URL . 'dashboard/utilisateur');
                }
                exit;
            }

            // Authentication failed
            header('Location: ' . BASE_URL . 'auth/login?error=' . urlencode('Invalid credentials'));
            exit;
        }

        // Otherwise show the login form
        $this->view('auth/login', []);
    }

    public function register() {
        // Si soumission du formulaire
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $nom = trim($_POST['name'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = strtolower(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            $num_tel = trim($_POST['num_tel'] ?? '');
            $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

            // Validation basique
            if ('' === $nom || '' === $prenom || '' === $email || '' === $password) {
                header('Location: ' . BASE_URL . 'auth/register?error=' . urlencode('Tous les champs obligatoires'));
                exit;
            }

            if ($password !== $confirm) {
                header('Location: ' . BASE_URL . 'auth/register?error=' . urlencode('Les mots de passe ne correspondent pas'));
                exit;
            }

            // Vérification reCAPTCHA
            if (empty($recaptchaResponse) || !defined('RECAPTCHA_SECRET_KEY')) {
                header('Location: ' . BASE_URL . 'auth/register?error=' . urlencode('Veuillez compléter le captcha'));
                exit;
            }

            $secretKey = RECAPTCHA_SECRET_KEY;
            $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) . '&response=' . urlencode($recaptchaResponse);
            $verifyResponse = @file_get_contents($verifyUrl);

            if (false === $verifyResponse) {
                header('Location: ' . BASE_URL . 'auth/register?error=' . urlencode('Erreur de vérification captcha'));
                exit;
            }
            $responseData = json_decode($verifyResponse, true);

            if (empty($responseData['success'])) {
                header('Location: ' . BASE_URL . 'auth/register?error=' . urlencode('Captcha invalide, veuillez réessayer'));
                exit;
            }

            // Appel du modèle pour enregistrer l'utilisateur
            $userModel = $this->model('User');
            $created = $userModel->register($nom, $prenom, $email, $password, $num_tel);

            if ($created) {
                // Créer session minimale et rediriger vers le dashboard utilisateur
                $_SESSION['user'] = [
                    'name' => $nom . ' ' . $prenom,
                    'email' => $email,
                    'role' => 'utilisateur',
                ];

                // Demande: header vers views/utilisateur/dashboard.php
                header('Location: ' . BASE_URL . 'views/utilisateur/dashboard.php');
                exit;
            }

            // Si l'email existe déjà ou erreur d'insertion
            header('Location: ' . BASE_URL . 'auth/register?error=' . urlencode('Cet email est déjà utilisé'));
            exit;
        }

        // Sinon afficher le formulaire
        $this->view('auth/register', []);
    }

    public function profile() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->view('auth/profile', ['user' => $_SESSION['user']]);
    }

    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . 'home');
        exit;
    }
}
