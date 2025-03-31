<?php

use App\Core\App;

/* Process the post data, and get the stored item from the Isbn class. */
$sInput = App::resolve('process')->store('scan', $_POST);
$store = App::resolve('isbn')->confirmChoice($sInput);

/* Deal with any potential Isbn class errors. */
if(is_string($store)) {
    App::resolve('session')->flash('feedback', [
        'fail' => $store
    ]);

    return App::redirect('beheer', TRUE);
}

/* Process the API data for pre-filling the form, and add any missing data if that was set in the POST. */
$newItem = App::resolve('procApi')->processData($store);

if(!isset($newItem['rIndex']) && !empty($_POST['reeks-index'])) {
    $newItem['rIndex'] = (int) $_POST['reeks-index'];
}

if(!isset($newItem['_method'])) {
    $newItem['_method'] = 'PUT';
}

/* Clear old session _flash data, flash the new data, and redirect back to the item-maken pop-in. */
App::resolve('session')->unflash();

App::resolve('session')->setVariable('page-data', [
    'temp-cover' => $newItem['plaatje']
]);

App::resolve('session')->flash([
    'newItem' => $newItem,
    'temp-cover' => $newItem['plaatje'],
    'feedback' => [
        'found' => 'Controleer de ingevulde gegevens van Google, het kan zijn dat deze niet helemaal klopt.'
    ],
    'tags' => [
        'pop-in' => 'items-maken'
]]);

return App::redirect('beheer#items-maken-pop-in', TRUE);