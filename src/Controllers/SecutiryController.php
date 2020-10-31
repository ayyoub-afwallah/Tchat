<?php

namespace src\Controllers\SecurityController;
@session_start();
use config\SessionStorage\SessionStorage;
use src\Controllers\AbstractController\AbstractController;

class SecutiryController extends AbstractController
{
    public static function index()
    {


        if (
            isset($_POST['username']) &&
            !empty($_POST['username'])
        ) {

            $username = $_POST['username'];
            // create session
            $sessionStorage = new SessionStorage($username);
            $sessionStorage->set('id', $username);
            $sessionStorage->set('username', $username);

            // redirect to room chat
            header("Location:/chat");

        } else {
            self::Render('login');
        }
    }
}