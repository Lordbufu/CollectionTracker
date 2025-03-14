<?php

use App\Core\App;

/* Clear old session _flash data, set the pop-in tag, and redirect to the pop-in. */
App::resolve('session')->unflash();
App::resolve('session')->flash('tags', ['pop-in' => 'register']);
return App::redirect('home#account-maken-pop-in', TRUE);