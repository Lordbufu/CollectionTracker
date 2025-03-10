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
$finalItem;

/* Make sure we store the correct autheurs ... W.I.P. ¿ */
if(!empty($store['autheurs']) && count($store['autheurs']) > 1) {
    dd('Deal with multiple autheurs here ¿');
} else {
    $finalItem['autheur'] = $store['autheurs'][0] ?? '';
}

/* Store the identifiers (isbn/ean codes), and try to get always get ISBN_13 first. */
$isbn = $store['isbn'];

if(is_array($isbn)) {
    foreach($isbn as $okey => $pair) {
        foreach($pair as $ikey => $value) {
            if($ikey === 'type' && $value === 'ISBN_13') {
                $finalItem['isbn'] = $isbn[$okey]['identifier'];
            }

            if($ikey === 'type' && $value === 'ISBN_10' && !isset($finalItem['isbn'])) {
                $finalItem['isbn'] = $isbn[$okey]['identifier'];
            }
        }
    }
// Exception in case a single identifier is slightly different in code.
} else {
    dd('Deal with single identifiers here ¿');
}

/* Check if a cover was returned, and deal with all known solutions, exception is included incase i missed something */
if(isset($store['cover'])) {
    $cover = $store['cover'];

    if(isset($cover['thumbnail'])) {
        $finalItem['cover'] = $cover['thumbnail'];
    } else if(isset($cover['smallThumbnail'])) {
        $finalItem['cover'] = $cover['smallThumbnail'];
    } else {
        $finalItem['cover'] = 'Exception in cover code, review what went wrong !';
    }
}

/* Attempt to set the remaining data to store the item in the database. */
$finalItem['naam'] = $store['title'] ?? '';
$finalItem['datum'] = $store['date'] ?? '';
$finalItem['opmerking'] = $store['opmerking'] ?? '';
$finalItem['rIndex'] = $_POST['reeks-index'];

$flash = [
    'oldItem' => $finalItem,
    'newCover' => $finalItem['cover'],
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