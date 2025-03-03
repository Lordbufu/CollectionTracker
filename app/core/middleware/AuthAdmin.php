<?php

namespace App\Core\Middleware;

use App\Core\App;

/* AuthAdmin: Specifically targets the Admin user.*/
class AuthAdmin {
    public function handle() {
        /* If no user data is set, or the user isnt a administrator, redirect to the landing page. */
        if(!isset($_SESSION['user']['rights']) || $_SESSION['user']['rights'] !== 'admin') {
            return App::redirect('');
        }
    }
}