<?php

use App\Core\App;

/* Attempt to remove the item to the collection using the POST data. */
$store = App::resolve('collectie')->remColl($_POST);

/* If removing the item failed, store user feedback and redirect back to the user page. */
if(is_string($store)) {
    App::resolve('session')->flash('feedback', [
        'error' => $store
    ]);

    return App::redirect('gebruik', TRUE);
}

/* Flash user-feedback about the item being removed, and update the collection page-data. */
$iName = App::resolve('items')->getName([
    'Item_Index' => $_POST['index']
]);

App::resolve('session')->flash('feedback', [
    'removed' => "Het item: {$iName}. \n  Is uit uw collectie verwijderdt."
]);

App::resolve('session')->setVariable('page-data', [
    'collecties' => App::resolve('collectie')->getColl([
        'Gebr_Index' => $_SESSION['user']['id']
    ])
]);

/* Redirect to the gebruik-page, preserving the session flash data. */
return App::redirect('gebruik', TRUE);