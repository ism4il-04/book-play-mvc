<?php

class AuthController extends Controller {
    public function login() {
        // If the form was submitted, process authentication
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            if ('' === $email || '' === $password) {
                header('Location: ' . BASE_URL . 'auth/login?error=' . urlencode('Please provide email and password'));
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
