<?php

namespace src\Controllers\SecurityController;
@session_start();
use config\SessionManager\SessionManager;
use src\Controllers\AbstractController\AbstractController;

class SecutiryController extends AbstractController
{
    public static function index()
    {
        if (isset($_SESSION['id']))
        {
            header("Location:/chat");
        }
        if (isset($_POST['username']) && !empty($_POST['username']))
        {
            $username = $_POST['username'];
            // create session
            $sessionManager = new SessionManager($username);
            $sessionManager->set('id', $username);
            $sessionManager->set('username', $username);

            header("Location:/chat");
        } else {
            self::Render('login');
        }
    }
}