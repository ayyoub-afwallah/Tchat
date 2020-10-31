<?php

namespace src\Controllers\AbstractController;

class AbstractController
{
    public static function Render($ViewName)
    {
        require_once(dirname(__DIR__) .'/../templates/'. $ViewName .'.php');
    }
}