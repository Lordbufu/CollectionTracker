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

    /* If no session user data is set, set the current user as guest. */
    if(!isset($_SESSION['user'])) {
        App::resolve('session')->setVariable('user', ['rights' => 'guest']);
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

// Potentially usefull documentation, atm mostly a reminder for me:
    // Select query testing:
        // $selectIds = [ 'Gebr_Naam' => 'test' ];
        // $selectResult = App::resolve('database')->prepQuery('select', 'gebruikers', $selectIds)->getSingle();
        // dd($selectResult);
    // Insert query testing: TODO: Might needs a seperate function to check if the results was null ?
        // $insertData = [ 'Gebr_Index' => 2460, 'Alb_Index' => 25 ];
        // $insertResult = App::resolve('database')->prepQuery('insert', 'collecties', null, $insertData)->getAll();    // result is null
        // dd($insertResult);
    // Update query testing: TODO: Might needs a seperate function to check if the results was null ?
        // $updateId = [ 'Gebr_Index' => 2460 ];
        // $updateData = ['Alb_Index' => 26 ];
        // $updateResult = App::resolve('database')->prepQuery('update', 'collecties', $updateId, $updateData)->getAll();
        // dd($updateResult);
    // Delete query testing: TODO: Might needs a seperate function to check if the results was null ?      
        // $deleteIds = [ 'Gebr_Index' => 2460, 'Alb_Index' => 26 ];  
        // $deleteResult = App::resolve('database')->prepQuery('delete', 'collecties', $deleteIds)->getAll();
        // dd($deleteResult);
    // Count query testing (incl seperate return function):
        // $countIds = 1;
        // $countResults = App::resolve('database')->prepQuery('count', null, $countIds)->countItems();
        // dd($countResults);
    // testTable query testing (incl seperate return function):
        // $testCode = App::resolve('database')->prepQuery('testTable', 'gebruiker')->getErrorCode();
        // dd($testCode); // (Expected error code = 42S02)