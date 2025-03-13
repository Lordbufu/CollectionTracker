<?php

use App\Core\App;

/* Return the 'trap' view, if no user was set. */
if(!isset($_SESSION['user']['rights'])) {
    return App::view('index.view.php');
}

/* Clear old session _flash data. */
App::resolve('session')->unflash();

/* Happy path redirecting to the actual homepage. */
return App::redirect('home', TRUE);