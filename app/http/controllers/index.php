<?php

use App\Core\App;

/* Return the 'trap' view, if no user was set. */
if(!isset($_SESSION['user']['rights'])) {
    return App::view('index.view.php');
}

/* Redirect guests to the guest page, preserving any session _flash memory. */
if(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] === 'guest') {
    return App::redirect('home', TRUE);
}

/* Redirect users to the gebruik page, preserving any session _flash memory. */
if(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] === 'user') {
    return App::redirect('gebruik', TRUE);
}

/* Redirect admin to the beheer page, preserving any session _flash memory. */
if(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] === 'admin') {
    return App::redirect('beheer', TRUE);
}