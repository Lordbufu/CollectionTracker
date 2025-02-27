<?php

use App\Core\App;

/* To reduce page clutter, the pop-ins have been gated behing a session _flash tag. */
App::resolve('session')->flash('tags', [
    'pop-in' => 'ww-reset'
]);

return App::redirect('beheer#ww-reset-pop-in', TRUE);