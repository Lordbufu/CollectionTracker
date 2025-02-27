<?php

use App\Core\App;

/* To reduce page clutter, the pop-ins have been gated behind a session _flash tag. */
App::resolve('session')->flash('tags', [
    'pop-in' => 'register'
]);

return App::redirect('home#account-maken-pop-in', TRUE);