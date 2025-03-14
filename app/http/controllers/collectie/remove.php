<?php

use App\Core\App;

/* Set POST as user input and add the user id. */
$uInput = $_POST;
if(!isset($uInput['Gebr_Index'])) { $uInput['Gebr_Index'] = $_SESSION['user']['id']; }

/* Attempt to process the user input, for database operations. */
$process = App::resolve('process')->store('collectie', $uInput);

/* Attempt to remove the item to the collection using the POST data. */
$store = App::resolve('collectie')->remColl(['Gebr_Index' => $process['Gebr_Index'], 'Item_Index' => $process['Item_Index']]);

/* If removing the item failed, store user feedback and redirect back to the user page. */
if(is_string($store)) {
    App::resolve('session')->flash('feedback', ['error' => $store]);
    return App::redirect('gebruik', TRUE);
}

/* Clear old session _flash data. */
App::resolve('session')->unflash();

/* Flash user-feedback about the item being removed, and update the collection page-data, before redirecton to the user page. */
$iName = App::resolve('items')->getKey(['Item_Index' => $_POST['index']], 'Item_Naam');
App::resolve('session')->flash('feedback', ['removed' => "Het item: {$iName}. \n  Is uit uw collectie verwijderdt."]);
if(isset($_SESSION['page-data']['collecties'])) { unset($_SESSION['page-data']['collecties']); }
App::resolve('session')->setVariable('page-data', ['collecties' => App::resolve('collectie')->getColl(['Gebr_Index' => $_SESSION['user']['id']])]);
return App::redirect('gebruik', TRUE);