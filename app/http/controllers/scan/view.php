<?php

use App\Core\App;

/* Store the redirect route based on the user rights, if its not a 'user' we store the admin route. */
$route = ($_SESSION['user']['rights'] === 'user') ? 'gebruik' : 'beheer';

/* Store what the name of the current reeks is, trying to use the POST as default, stored as an id for a DB opperations. */
$ids = [
    'Reeks_Naam' => $_POST['naam'] ?? $_SESSION['page-data']['huidige-reeks']
];

/* Prepare the _flash tags required for this pop-in. */
$tags = ['tags' => [
    'reeks-index' => App::resolve('reeks')->getId($ids),
    'pop-in' => 'bScan'
]];

/* Flash the tags into the session. */
App::resolve('session')->flash($tags);

/* Redirect based on the user rights. */
return App::redirect("{$route}#item-scan-pop-in", TRUE);