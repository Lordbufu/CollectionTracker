<?php

use App\Core\App;

/* Attempt to get the user choice item, via my isbn class. */
$store = App::resolve('isbn')->confirmChoice($_POST);

/*  If a string is returned,
    i store the feedback in the flash memory,
    and return to the default beheer page,
    preserving the flash memory tags.
 */
if(is_string($store)) {
    App::resolve('session')->flash('feedback', [
        'fail' => $store
    ]);

    return App::redirect('beheer', TRUE);
}

/* Prep a final item variable, and check if we have an item stored. */
$finalItem;

if(is_array($store)) {
    /* Make sure we store the correct autheurs ... W.I.P. ¿ */
    if(!empty($store['autheurs']) && count($store['autheurs']) > 1) {
        dd('Deal with multiple autheurs here ¿');
    } else {
        $finalItem['autheur'] = $store['autheurs'][0] ?? '';
    }

    /*  Check what type of isbn was returned,
        always prefer storing the ISBN_13,
        if there is non we try the ISBN_10,
        incase that is also not there i just store a 0.
     */
    if(isset($store['isbn'][1]['type']) && $store['isbn'][1]['type'] === 'ISBN_13') {
        $finalItem['isbn'] = $store['isbn'][1]['identifier'];
    } elseif(isset($store['isbn'][0]['type']) && $store['isbn'][0]['type'] === 'ISBN_10') {
        $finalItem['isbn'] = $store['isbn'][0]['identifier'];
    } else {
        $finalItem['isbn'] = 0;
    }

    /*  I also check if a tumbnail was returned,
        if the large one isnt set,
        i pick the small one,
        and set nothing if non was set.
     */
    if(isset($store['cover']['thumbnail'])) {
        $finalItem['cover'] = $store['cover']['thumbnail'];
    } elseif(isset($store['cover']['smallThumbnail'])) {
        $finalItem['cover'] = $store['cover']['smallThumbnail'];
    } else {
        $finalItem['cover'] = '';
    }

    /* Attempt to set the remaining data to store the item in the database. */
    $finalItem['naam'] = $store['title'] ?? '';
    $finalItem['datum'] = $store['date'] ?? '';
    $finalItem['opmerking'] = $store['opmerking'] ?? '';
    $finalItem['rIndex'] = $_POST['reeks-index'];
}

/* Check if the finalItem was stored properly, and prepare the correct flash memory data. */
if(is_array($finalItem)) {
    $flash = [
        'oldItem' => $finalItem,
        'newCover' => $finalItem['cover'],
        'feedback' => [
            'found' => 'Controleer de ingevulde gegevens van Google, het kan zijn dat deze niet helemaal klopt.'
        ],
        'tags' => [
            'method' => 'PATCH',
            'pop-in' => 'items-maken'
        ]
    ];
/* Prepare a flesh error feedback is no item was set properly. */
} else {
    $flash = ['feedback' => [
        'fail' => 'Er ging iets mis tijdens het ophalen van de item gegevens, neem contact op met uw Administrator!'
    ]];
}

/* Always flash the outcome of the above evaluation, and redirect back to the items-maken pop-in or beheer page. */
App::resolve('session')->flash($flash);

if(!is_array($finalItem)) {
    return App::redirect('beheer', TRUE);
}

return App::redirect('beheer#items-maken-pop-in', TRUE);