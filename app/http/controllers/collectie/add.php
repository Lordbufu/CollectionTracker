<?php

use App\Core\App;

/* If the expected inputs are set, create a collection data array. */
if(isset($_POST['index']) && isset($_SESSION['page-data']['huidige-reeks'])) {
    $collData = [
        'iIndex' => $_POST['index'],
        'rIndex' => App::resolve('reeks')->getId([
            'Reeks_Naam' => $_SESSION['page-data']['huidige-reeks']
        ])
    ];
}

/* If the array data wasnt set, or if the reek index resolved to a error, store the correct input and redirect back to the user page. */
if(!isset($collData) || is_string($collData['rIndex'])) {
    if(!isset($collData)) {
        App::resolve('session')->flash('feedback', [
            'error' => App::resolve('errors')->getError('forms', 'input-missing')
        ]);
    } else {
        App::resolve('session')->flash('feedback', [
            'error' => $collData['rIndex']
        ]);
    }

    return App::redirect('gebruik', TRUE);
}

/* Attemp to add the item to the user collection. */
$store = App::resolve('collectie')->addColl($collData);

/* Store user feedback on error, and redirect back to the user page. */
if(is_string($store)) {
    App::resolve('session')->flash('feedback', [
        'error' => $store
    ]);

    return App::redirect('gebruik', TRUE);
}

/* Get the item name of the item that was added, prepare the correct user feedback and refresh the collection data before redirecting back to the user page */
$iName = App::resolve('items')->getName([
    'Item_Index' => $_POST['index']
]);

App::resolve('session')->flash('feedback', [
    'added' => "Het item: {$iName}. \n  Is aan uw collectie toegevoegd."
]);

App::resolve('session')->setVariable('page-data', [
    'collecties' => App::resolve('collectie')->getColl()
]);

return App::redirect('gebruik', TRUE);