<?php
class Dashboad_gestionnaireController extends Controller {
    public function index() {
        // Check if user is logged in and is a manager
        if (!isset($_SESSION['user']) || 'gestionnaire' !== ($_SESSION['user']['role'] ?? null)) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->view('gestionnaire/dashboard', ['user' => $_SESSION['user']]);
    }
}


