<?php

namespace App\Core\Middleware;

use App\Core\App;

/* AuthUser: Specifically targets the regular users. */
class AuthUser {
    public function handle() {
        /* If no user data is set, or the user isnt a user, redirect to the landing page. */
        if(!isset($_SESSION['user']['rights']) || $_SESSION['user']['rights'] !== 'user') {
            return App::redirect('');
        }
    }
}