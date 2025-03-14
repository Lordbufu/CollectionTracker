<?php

use App\Core\App;

/* Set POST as user input and add the user id. */
$uInput = $_POST;

if(!isset($uInput['Gebr_Index'])) { $uInput['Gebr_Index'] = $_SESSION['user']['id']; }

/* Attempt to process the user input, for database operations. */
$process = App::resolve('process')->store('collectie', $uInput);

if(is_string($process)) {
    App::resolve('session')->flash('feedback', ['error' => App::resolve('errors')->getError('forms', 'input-missing')]);
    return App::redirect('gebruik', TRUE);
}

/* Attemp to add the item to the user collection. */
$store = App::resolve('collectie')->addColl($process);

/* Store user feedback on error, and redirect back to the user page. */
if(is_string($store)) {
    App::resolve('session')->flash('feedback', ['error' => $store]);
    return App::redirect('gebruik', TRUE);
}

/* Clear old session _flash data. */
App::resolve('session')->unflash();

/* Get the item name of the item that was added, prepare the correct user feedback and refresh the collection data before redirecting back to the user page */
$iName = App::resolve('items')->getKey(['Item_Index' => $_POST['index']], 'Item_Naam');
App::resolve('session')->flash('feedback', ['added' => "Het item: {$iName}. \n  Is aan uw collectie toegevoegd." ]);
App::resolve('session')->setVariable('page-data', ['collecties' => App::resolve('collectie')->getColl()]);
return App::redirect('gebruik', TRUE);