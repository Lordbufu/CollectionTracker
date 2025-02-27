<?php

namespace App\Core\Middleware;

use App\Core\App;

class Auth {
    public function handle() {
        if(!isset($_SESSION['user']['rights'])) {
            return App::redirect('');
        }
    }
}