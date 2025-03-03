<?php

use App\Core\App;

/* Retain the submitted form data as is, for re-filling it on errors. */
$oldFilData = [
    'method' => $_POST['_method'],
    'naam' => $_POST['naam'],
    'makers' => $_POST['makers'],
    'opmerking' => $_POST['opmerking']
];

/* Validate all post data via the FormValidator */
$form = App::resolve('form')::validate($_POST);

/* On validation error, prep the correct _flash data, and return to the pop-in. */
if(is_array($form)) {
    $flash = [
        'feedback' => $form,
        'oldForm' => $oldFilData,
        'tags' => [
            'pop-in' => 'reeks-maken'
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('beheer#reeks-maken-pop-in', TRUE);
}

/* If the validation passed, attempt to store the POST data. */
$store = App::resolve('reeks')->createReeks($_POST);

/* If the data wasnt stored properly, prepare the correct _flash data, and return to the pop-in. */
if(is_string($store)) {
    $flash = [
        'feedback' => [
            'error' => $store
        ],
        'oldForm' => $oldFilData,
        'tags' =>[
            'pop-in' => 'reeks-maken'
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('beheer#reeks-maken-pop-in', TRUE);
}

/* If the data was stored, provide usefull feedback in the _flash data, refresh the reeks paga-data and redirect to the 'beheer' */
App::resolve('session')->flash('feedback', [
    'success' => "De reeks: {$oldFilData['naam']} \n Is aangemaakt en zou nu in de lijst moeten staan!"
]);

App::resolve('session')->setVariable('page-data', [
    'reeks' => App::resolve('reeks')->getAllReeks()
]);

App::redirect('beheer', TRUE);