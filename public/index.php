<?php

/* Require the auto-loader, and start using the App. */
require __DIR__ . '/../' . 'app/helpers.php';
require __DIR__ . '/../' . 'vendor/autoload.php';

use App\Core\App;

/* Try to init the base App logic. */
try {
    /* Init the app and check its state, load the router, and load the routes. */
    if(App::initApp()) {
        $router = App::resolve('router');
        $routes = require base_path('app/routes.php');
    }

    /* Config session settings, and start a new session. */
    App::resolve('session')->configSession();

    /* If no session user data is set, and the user agent is not 'invalid', set the current user as guest. */
    if(!isset($_SESSION['user']) && isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) {
        App::resolve('session')->setVariable('user', [
            'rights' => 'guest'
        ]);
    }

    /* Route the request based on uri and request method (either costum or PhP standard method). */
    $router->route(parse_url($_SERVER['REQUEST_URI'])['path'], $_POST['_method'] ?? $_SERVER['REQUEST_METHOD']);

/* Catch exceptions if they happen, die the errror, since app init is broken if we hit this. */
} catch (Exception $e) {
    die($e->getMessage());
}

// Testing and developing the _flash function atm, so far its working as i intended it.
if(!isset($_SESSION['_flash']['tags']['redirect'])) {
    App::resolve('session')->unflash();
}