<?php

namespace App\Core\Middleware;

use App\Core\App;

/* Guest: Specifically targets the guest user. */
class Guest {
    public function handle() {
        /* If no user data is set, or the user isnt a guest, redirect to the landing page. */
        if(!isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] !== 'guest') {
            return App::redirect('');
        }
    }
}