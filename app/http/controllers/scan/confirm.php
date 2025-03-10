<?php

use App\Core\App;

/* Attempt to get the user choice item, via my isbn class. */
$store = App::resolve('isbn')->confirmChoice($_POST);

/* If a string is returned, store the user feedback error, and return to the default view. */
if(is_string($store)) {
    App::resolve('session')->flash('feedback', [
        'fail' => $store
    ]);

    return App::redirect('beheer', TRUE);
}

/* Prep a final item variable, and check if we have an item stored. */
$newItem = App::resolve('procApi')->processData($store);

if(!isset($newItem['rIndex'])) {
    $newItem['rIndex'] = (int) $_POST['reeks-index'];
}

$flash = [
    'oldItem' => $newItem,
    'newCover' => $newItem['cover'],
    'feedback' => [
        'found' => 'Controleer de ingevulde gegevens van Google, het kan zijn dat deze niet helemaal klopt.'
    ],
    'tags' => [
        'method' => 'PUT',
        'pop-in' => 'items-maken'
]];

/* Always flash the outcome of the above evaluation, and redirect back to the items-maken pop-in or beheer page. */
App::resolve('session')->flash($flash);

return App::redirect('beheer#items-maken-pop-in', TRUE);