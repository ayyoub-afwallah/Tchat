<?php
namespace config\SessionStorage;
session_start();
use config\IStorage\IStorage;

/**
 * Class SessionStorage
 */
class SessionStorage implements IStorage
{


    /**
     * SessionStorage constructor.
     * @param $id
     */
    function __construct($id)
    {
            session_name($id);
            session_start();
    }

    /**
     * @param $key
     * @param $value
     * @return mixed|void
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $_SESSION[$key];
    }

}