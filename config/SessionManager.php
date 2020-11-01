<?php

namespace config\SessionManager;
session_start();

use config\IStorage\IStorage;

/**
 * Class SessionManager
 */
class SessionManager
{
    function __construct($id)
    {
        session_name($id);
        session_start();
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return $_SESSION[$key];
    }

}