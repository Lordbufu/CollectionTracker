<?php

use App\Core\App;

/* Attempt to get the user choice item, via my isbn class, if a string is returned, store the user feedback error, and return to the default view. */
$store = App::resolve('isbn')->confirmChoice($_POST);

if(is_string($store)) {
    App::resolve('session')->flash('feedback', [
        'fail' => $store
    ]);

    return App::redirect('beheer', TRUE);
}

/* Prep a final item variable, and check if we have an item stored. */
$newItem = App::resolve('procApi')->processData($store);
if(!isset($newItem['rIndex'])) { $newItem['rIndex'] = (int) $_POST['reeks-index']; }
if(!isset($newItem['method'])) { $newItem['method'] = 'PUT'; }

/* Clear old session _flash data, flash the new data, and redirect back to the item-maken pop-in. */
App::resolve('session')->unflash();
App::resolve('session')->flash([
    'newItem' => $newItem,
    'feedback' => [
        'found' => 'Controleer de ingevulde gegevens van Google, het kan zijn dat deze niet helemaal klopt.'
    ],
    'tags' => [
        'pop-in' => 'items-maken'
]]);
return App::redirect('beheer#items-maken-pop-in', TRUE);