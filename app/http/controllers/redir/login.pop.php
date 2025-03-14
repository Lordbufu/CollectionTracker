<?php

use App\Core\App;

/* Clear old session _flash data, set the pop-in tag, and redirect to the pop=-in. */
App::resolve('session')->unflash();
App::resolve('session')->flash('tags', ['pop-in' => 'login']);
return App::redirect('home#login-pop-in', TRUE);