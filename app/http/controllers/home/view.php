<?php

use App\Core\App;

if(isset($_POST['return'])) {                   // If a pop-in is closed,
    App::resolve('session')->unflash();         // unflash any tags and data that might have been stored for it,
}

return App::view('home/index.view.php');        // and then return the correct view.