<?php

class App {
    protected $controller = 'HomeController'; // Default controller

    protected $method = 'index';               // Default method

    protected $params = [];                    // Any parameters

    public function __construct() {
        $url = $this->parseUrl();

        // --- Controller ---
        $controllerPath = __DIR__ . '/../Controllers/' . ucfirst($this->controller) . '.php';

        if (isset($url[0]) && file_exists(__DIR__ . '/../Controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            $controllerPath = __DIR__ . '/../Controllers/' . $this->controller . '.php';
            unset($url[0]);
        }

        require_once $controllerPath;
        $this->controller = new $this->controller();

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

    // Parse the URL: /controller/method/param1/param2
    private function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }

        return [];
    }
}
