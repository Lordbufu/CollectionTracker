<?php

use App\Core\App;

/* If a pop-in was closed, clean up the session flash data. */
if(isset($_POST['return'])) {
    App::resolve('session')->unflash();
}

/* Return the normal home view. */
return App::view('home/index.view.php');