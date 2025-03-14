<?php

use App\Core\App;

/* Clear old session _flash data, and set the pop-in tag, before redirecting to the pop-in. */
App::resolve('session')->unflash();
App::resolve('session')->flash('tags', ['pop-in' => 'ww-reset']);
return App::redirect('beheer#ww-reset-pop-in', TRUE);