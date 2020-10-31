<?php

namespace src\Controller\ChatController;

use src\Controllers\AbstractController\AbstractController;
@session_start();

class ChatController extends AbstractController
{
    public static function index()
    {
        if (!isset($_SESSION['id'])) {
            header("Location:/login");
        }
        self::Render('chat');
    }
}