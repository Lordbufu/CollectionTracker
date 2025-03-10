<?php

use App\Core\App;

$route = ($_SESSION['user']['rights'] === 'user') ? 'gebruik' : 'beheer';           // Store the redirect route based on the user rights.
$id = ['Reeks_Naam' => $_POST['naam'] ?? $_SESSION['page-data']['huidige-reeks']];  // Store the reeks name of the currently selected reeks.

$tags = [                                                                           // Start preparing the '_flash' memory.
    'tags' => [
        'reeks-index' => App::resolve('reeks')->getKey($id, 'Reeks_Index'),         // Get Reeks index based on the above define Reeks name.
        'pop-in' => 'bScan'                                                         // Store the pop-in name i want to load.
]];

App::resolve('session')->flash($tags);                                              // Store the '_flash' memory in the session.
return App::redirect("{$route}#item-scan-pop-in", TRUE);                            // Redirect to the correct pop-in, preserving the session _flash memory.