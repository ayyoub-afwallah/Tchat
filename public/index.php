<?php
require_once dirname(__DIR__)."/vendor/autoload.php";

require_once(dirname(__DIR__) . '/config/RoutesLoader.php');
if($_SERVER['REQUEST_URI'] === '/')
    header("Location:/login");
