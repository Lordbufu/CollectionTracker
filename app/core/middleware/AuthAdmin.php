<?php

namespace App\Core\Middleware;

class AuthAdmin {
    public function handle() {
        if(!isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] !== 'admin') {
            return App::redirect('');
        }
    }
}