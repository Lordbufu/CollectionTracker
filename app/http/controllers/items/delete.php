<?php

use App\Core\App;

/* Check if the correct data is present, and store it as ids for the database opperation. */
if(isset($_POST['iIndex']) && $_POST['rIndex']) {
    $itemName = App::resolve('items')->getKey([
            'Item_Index' => $_POST['iIndex'],
            'Item_Reeks' => $_POST['rIndex']
        ],
        'Item_Naam'
    );
}

/* Attempt to remove the item from the database. */
$dbStore = App::resolve('items')->remItems([
    'Item_Index' => $_POST['iIndex'],
    'Item_Reeks' => $_POST['rIndex']
]);

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