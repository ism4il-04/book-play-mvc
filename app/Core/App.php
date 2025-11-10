<?php

class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];
    protected $db;

    public function __construct() {
        // Initialize database connection
        $this->db = Database::getInstance()->getConnection();
        
        $url = $this->parseUrl();

        // --- Gestion spéciale pour les routes avec underscore ---
        if (isset($url[0])) {
            // Route: auto_newsletter
            if ($url[0] === 'auto_newsletter') {
                $controllerPath = __DIR__ . '/../Controllers/AutoNewsletterController.php';
                
                if (file_exists($controllerPath)) {
                    require_once $controllerPath;
                    $this->controller = new AutoNewsletterController($this->db);
                    
                    // Méthode
                    if (isset($url[1]) && method_exists($this->controller, $url[1])) {
                        $this->method = $url[1];
                        unset($url[1]);
                    }
                    
                    unset($url[0]);
                    $this->params = $url ? array_values($url) : [];
                    call_user_func_array([$this->controller, $this->method], $this->params);
                    return;
                }
            }
            
            // Route: newsletter (manuelle)
            if ($url[0] === 'newsletter') {
                $controllerPath = __DIR__ . '/../Controllers/NewsletterController.php';
                
                if (file_exists($controllerPath)) {
                    require_once $controllerPath;
                    $this->controller = new NewsletterController($this->db);
                    
                    // Méthode
                    if (isset($url[1]) && method_exists($this->controller, $url[1])) {
                        $this->method = $url[1];
                        unset($url[1]);
                    }
                    
                    unset($url[0]);
                    $this->params = $url ? array_values($url) : [];
                    call_user_func_array([$this->controller, $this->method], $this->params);
                    return;
                }
            }
        }

        // --- Controller (logique existante) ---
        $controllerPath = __DIR__ . '/../Controllers/' . $this->controller . '.php';

        if (isset($url[0]) && file_exists(__DIR__ . '/../Controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            $controllerPath = __DIR__ . '/../Controllers/' . $this->controller . '.php';
            unset($url[0]);
        }

        require_once $controllerPath;
        
        // Pass database connection to the controller if it requires it
        $controllerClass = $this->controller;
        $this->controller = new $controllerClass($this->db);

        // --- Method ---
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        // --- Parameters ---
        $this->params = $url ? array_values($url) : [];

        // --- Execute Controller + Method ---
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }

        return [];
    }
}