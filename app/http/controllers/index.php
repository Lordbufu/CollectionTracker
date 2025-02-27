<?php

use App\Core\App;

/* Return the 'trap' view, if no user was set. */
if(!isset($_SESSION['user']['rights'])) {
    return App::view('index.view.php');
}

/* Happy path redirecting to the actual homepage. */
return App::redirect('home', TRUE);