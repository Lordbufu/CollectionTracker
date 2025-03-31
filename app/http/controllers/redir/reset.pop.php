<?php

use App\Core\App;

$route = ($_SESSION['user']['rights'] === 'user') ? 'gebruik' : 'beheer';           // Store the redirect route based on the user rights.

/* Clear old session _flash data, and set the pop-in tag, before redirecting to the pop-in. */
App::resolve('session')->unflash();

App::resolve('session')->flash('tags', [
    'pop-in' => 'ww-reset'
]);

return App::redirect("{$route}#ww-reset-pop-in", TRUE);