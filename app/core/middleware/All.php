<?php

namespace App\Core\Middleware;

use App\Core\App;

/*  Auth: Includes all authenticated (logged in) users, based on specific session data. */
class All {
    public function handle() {
        /* If user id is not set, redirect to the default 'home' page. */
        if(!isset($_SESSION['user'])) {
            return App::resolve('session')->setVariable('page-data', [
                'reset' => TRUE
            ]);
        }
    }
}