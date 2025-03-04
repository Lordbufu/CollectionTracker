<?php

namespace App\Core\Middleware;

use App\Core\App;

/* AuthAdmin: Specifically targets the Admin user.*/
class AuthAdmin {
    public function handle() {
        /* If no user data is set, or the user isnt a administrator, set a redirect tag for JS and return to caller.. */
        if(!isset($_SESSION['user']['rights']) || $_SESSION['user']['rights'] !== 'admin') {
            App::resolve('session')->setVariable('page-data', [
                'reset' => TRUE
            ]);

            return;
        }
    }
}