<?php
class Controller {
    // Load a model from the Models folder
    protected function model($model) {
        if (file_exists("../app/Models/" . $model . ".php")) {
            require_once "../app/Models/" . $model . ".php";
            return new $model();
        } else {
            die("Model '$model' not found.");
        }
    }

    // Load a view from the Views folder
    protected function view($view, $data = []) {
        $file = "../app/Views/" . $view . ".php";
        if (file_exists($file)) {
            extract($data); // makes $data['title'] become $title
            require_once $file;
        } else {
            die("View '$view' not found.");
        }
    }
}
