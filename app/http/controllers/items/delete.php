<?php

use App\Core\App;

/* Check if the correct data is present, and store it as ids for the database opperation. */
if(isset($_POST['iIndex']) && $_POST['rIndex']) {
    $ids = [
        'Item_Index' => $_POST['iIndex'],
        'Item_Reeks' => $_POST['rIndex']
    ];

    $itemName = App::resolve('items')->getName($ids);
}

// temp error handeling or missing form inputs.
if(!isset($ids)) {
    App::resolve('session')->flash('feedback', [
        'error' => App::resolve('errors')->getError('forms', 'input-missing')
    ]);
    return App::redirect('beheer', TRUE);
}

/* Attempt to remove the item from the database. */
$dbStore = App::resolve('items')->remItems($ids);

/* If there was an error, store said error as user feedback and return to the page. */
if(is_string($dbStore)) {
    App::resolve('session')->flash('feedback', [
        'error' => $dbStore
    ]);

    return App::redirect('beheer', TRUE);
}

/* If there was no error, store the correct feedback before returning tot he page. */
App::resolve('session')->flash('feedback', [
    'success' => "Het item: {$itemName}. is verwijdert !"
]);

return App::redirect('beheer', TRUE);