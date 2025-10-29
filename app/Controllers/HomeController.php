<?php

class HomeController extends Controller
{
    public function index()
    {
        $this->view('home/index');
    }

    public function subscribe()
    {
        if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['email'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            // Here you can save the email to database if needed
            // For now, just redirect back with success message

            header('Location: ' . BASE_URL . 'home?subscribed=1');
            exit;
        }

        header('Location: ' . BASE_URL . 'home');
        exit;
    }
}
