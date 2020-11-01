<?php

namespace config\Router;

class Router
{
    public static $routes = array();

    //we store routes names in an array
    //and if current url matches a route name , we just execute the route's function
    public static function set($route, $function)
    {
        self::$routes[] = $route;
        if (strpos($_SERVER['REQUEST_URI'], $route) !== false) {
            $function->__invoke();
        }
    }
}