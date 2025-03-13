<?php

use App\Core\App;

/* Clear old session _flash data. */
App::resolve('session')->unflash();

/* To reduce page clutter, the pop-ins have been gated behind a session _flash tag. */
App::resolve('session')->flash('tags', [
    'pop-in' => 'login'
]);

return App::redirect('home#login-pop-in', TRUE);