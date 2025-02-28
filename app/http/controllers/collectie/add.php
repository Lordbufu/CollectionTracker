<?php

use App\Core\App;

$store = 'empty';                                                                                   // Set useless string for evaluation reasons.

if(isset($_POST['index']) && isset($_SESSION['page-data']['huidige-reeks'])) {                      // Check if we have the required data,
    $collData = [                                                                                   // prep it as an array for the collection add request.
        'iIndex' => $_POST['index'],
        'rIndex' => App::resolve('reeks')->getId([
            'Reeks_Naam' => $_SESSION['page-data']['huidige-reeks']
        ])
    ];
}

if(isset($collData['iIndex']) && isset($collData['rIndex'])) {                                      // Check if required data was set,
    $store = App::resolve('collectie')->addColl($collData);                                         // and add item to user collecton.
}

if(is_string($store)) {                                                                             // If issues or errors,
    $mssg = isset($store) ? $store : App::resolve('errors')->getError('forms', 'input-missing');    // check what error to store,

    App::resolve('session')->flash('feedback', [                                                    // prep error as user feedback,
        'error' => $store
    ]);

    return App::redirect('gebruik', TRUE);                                                          // and redirect to gebruik-page with _flash tags.
}

$iName = App::resolve('items')->getName([                                                           // Get item name,
    'Item_Index' => $_POST['index']
]);

App::resolve('session')->flash('feedback', [                                                        // Tell user {item-name} was added,
    'added' => "Het item: {$iName}. \n  Is aan uw collectie toegevoegd."
]);

App::resolve('session')->setVariable('page-data', [                                                 // Update user there collection data,
    'collecties' => App::resolve('collectie')->getColl()
]);

return App::redirect('gebruik', TRUE);                                                              // Redirect to gebruik-page with _flash tags.