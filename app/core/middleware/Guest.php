<?php

namespace App\Core\Middleware;

use App\Core\App;

class Guest {
    public function handle() {
        if(!isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] !== 'guest') {
            return App::redirect('');
        }
    }
}