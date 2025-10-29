<?php

// Load configuration for BASE_URL constant
require_once __DIR__ . '/../../config/config.php';

class Controller {
    // Load a model from the Models folder
    protected function model($model) {
        $file = __DIR__ . '/../Models/' . $model . '.php';

        if (file_exists($file)) {
            require_once $file;

            return new $model();
        }
        exit("Model '{$model}' not found.");
    }

    // Load a view from the Views folder
    protected function view($view, $data = []) {
        $file = __DIR__ . '/../Views/' . $view . '.php';

        if (file_exists($file)) {
            extract($data); // makes $data['title'] become $title
            require_once $file;
        } else {
            exit("View '{$view}' not found.");
        }
    }
}
