<?php

use config\Router\Router;
use src\Controller\ChatController\ChatController;
use src\Controllers\SecurityController\SecutiryController;

$RoutesList = [
    "chat" => function () {
        ChatController::index();
    },
    "login" => function () {
        SecutiryController::index();
    },

];

$router = new Router();

foreach ($RoutesList as $key => $value) {
    $router::set($key, $value);
}

