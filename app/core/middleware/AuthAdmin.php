<?php

namespace App\Core\Middleware;

use App\Core\App;

/* AuthAdmin: Specifically targets the Admin user.*/
class AuthAdmin {
    public function handle() {
        /* If no user id was set, or the rights are not set correctly, redirect to the 'home' page. */
        if(!isset($_SESSION['user']['id']) || $_SESSION['user']['rights'] !== 'admin') {
            return App::resolve('session')->setVariable('page-data', [
                'reset' => TRUE
            ]);
        }
    }
}