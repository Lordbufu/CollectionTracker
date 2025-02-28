<?php

use App\Core\App;

$oldFilData = [                                                     // Filter the user input, so i can savely return it to the page.
    'naam' => $_POST['naam'],
    'makers' => $_POST['makers'],
    'opmerking' => $_POST['opmerking']
];

$form = App::resolve('form')::validate($_POST);                     // Validate all user input POST data.

if(is_array($form)) {                                               // If a error string is returned,
    App::resolve('session')->flash([                                // flash the required data to the session,
        'feedback' => $form,
        'oldForm' => $oldFilData,
        'tags' => [
            'pop-in' => 'reeks-maken'
    ]]);

    return App::redirect('beheer#reeks-maken-pop-in', TRUE);        // redirect to te to pop-in preserving said flash data.
}

$store = App::resolve('reeks')->createReeks($_POST);                // Attempt to create the requested reeks.

if(is_string($store)) {                                             // If a error string is returned,
    App::resolve('session')->flash([                                // flash the required data to the session,
        'feedback' => [
            'error' => $store
        ],
        'oldForm' => $oldFilData,
        'tags' =>[
            'pop-in' => 'reeks-maken'
    ]]);

    return App::redirect('beheer#reeks-maken-pop-in', TRUE);        // redirect to te to pop-in preserving said flash data.
}

/* Store a feedback for succes, update the page-data */
App::resolve('session')->flash('feedback', [
    'success' => "De reeks: {$oldFilData['naam']} \n Is aangemaakt en zou nu in de lijst moeten staan!"
]);

App::resolve('session')->setVariable('page-data', [
    'reeks' => App::resolve('reeks')->getAllReeks()
]);

App::redirect('beheer', TRUE);