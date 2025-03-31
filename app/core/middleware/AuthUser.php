<?php

namespace App\Core\Middleware;

use App\Core\App;

/* AuthUser: Specifically targets the regular users. */
class AuthUser {
    public function handle() {
        /* If no user id was set, or the rights are not set correctly, redirect to the 'home' page. */
        if(!isset($_SESSION['user']['id']) || $_SESSION['user']['rights'] !== 'user') {
            return App::resolve('session')->setVariable('page-data', [
                'reset' => TRUE
            ]);
        }
    }
}