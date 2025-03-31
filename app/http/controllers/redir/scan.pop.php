<?php

use App\Core\App;

$route = ($_SESSION['user']['rights'] === 'user') ? 'gebruik' : 'beheer';           // Store the redirect route based on the user rights.
$rIndex  = App::resolve('reeks')->getKey([                                          // Get the Reeks index, using the selected reeks its name.
    'Reeks_Naam' => $_POST['naam']],
    'Reeks_Index'
);

App::resolve('session')->unflash();                                                 // Clear old '_flash' memory.
App::resolve('session')->flash([                                                    // Store the new '_flash' memory.
    'tags' => [
        'reeks-index' => $rIndex,
        'pop-in' => 'bScan'
]]);

return App::redirect("{$route}#item-scan-pop-in", TRUE);                            // Redirect to the correct pop-in.