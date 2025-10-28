<?php
class DashboardController extends Controller {
    
    public function utilisateur() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit();
        }
        
        $this->view('utilisateur/dashboard', ['user' => $_SESSION['user']]);
    }
    
    public function gestionnaire() {
        // Check if user is logged in and is a manager
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'gestionnaire') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit();
        }
        
        $this->view('gestionnaire/dashboard', ['user' => $_SESSION['user']]);
    }
    
    public function administrateur() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit();
        }
        
        $this->view('administrateur/dashboard', ['user' => $_SESSION['user']]);
    }
}

