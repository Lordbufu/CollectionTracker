<?php

namespace App\Core\Middleware;

use App\Core\App;

class AuthUser {
    public function handle() {
        if(!isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] !== 'user') {
            return App::redirect('');
        }
    }
}