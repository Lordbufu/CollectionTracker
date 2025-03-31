<?php

namespace App\Core\Middleware;

use App\Core\App;

/* Guest: Specifically targets the guest user. */
class Guest {
    public function handle() {
        /* If no user rights data is set, or the user rights are not set correctly, redirect to the 'home' page. */
        if(empty($_SESSION['user']) || $_SESSION['user']['rights'] !== 'guest') {
            return App::resolve('session')->setVariable('page-data', [
                'reset' => TRUE
            ]);
        }
    }
}