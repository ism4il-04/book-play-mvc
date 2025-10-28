<?php
class AuthController extends Controller {
    
    public function login() {
        $this->view('auth/login', []);
    }
    
    public function register() {
        $this->view('auth/register', []);
    }
    
    public function profile() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit();
        }
        
        $this->view('auth/profile', ['user' => $_SESSION['user']]);
    }
    
    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . 'home');
        exit();
    }
}
