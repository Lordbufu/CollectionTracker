<?php

use App\Core\App;

/* Use the authenticator to logout, destroying the session\cookie, and redirect to get a 'guest' status again. */
if(App::resolve('auth')->logout()) {
    App::redirect('');
}