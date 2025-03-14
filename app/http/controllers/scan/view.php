<?php

use App\Core\App;

$route = ($_SESSION['user']['rights'] === 'user') ? 'gebruik' : 'beheer';           // Store the redirect route based on the user rights.
$id = ['Reeks_Naam' => $_POST['naam'] ?? $_SESSION['page-data']['huidige-reeks']];  // Store the reeks name of the currently selected reeks.

App::resolve('session')->unflash();                                                 // Clear old session _flash data.
App::resolve('session')->flash([                                                    // Store the '_flash' memory in the session.
    'tags' => [
        'reeks-index' => App::resolve('reeks')->getKey($id, 'Reeks_Index'),
        'pop-in' => 'bScan'
]]);
return App::redirect("{$route}#item-scan-pop-in", TRUE);                            // Redirect to the correct pop-in, preserving the session _flash memory.