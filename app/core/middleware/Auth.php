<?php

namespace App\Core\Middleware;

use App\Core\App;

/* Auth: Includes all authenticated (logged in) users, based on specific session data. */
class Auth {
    public function handle() {
        /* If no user data is set, redirect to the landing page. */
        if(!isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] !== 'guest') {
            App::resolve('session')->setVariable('page-data', [
                'reset' => TRUE
            ]);

            return;
        }
    }
}