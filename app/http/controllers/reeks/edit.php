<?php

use App\Core\App;

/* Check if index was set, and request/store all required data for both the success and fail scenario. */
if(isset($_POST['index'])) {
    $oldName = App::resolve('reeks')->getName([
        'Reeks_Index' => $_POST['index']
    ]);

    $ids = ['Reeks_Index' => $_POST['index']];

    $oldForm = [
        'method' => $_POST['_method'],
        'index' => $_POST['index'],
        'naam' => inpFilt($_POST['naam']),
        'makers' => inpFilt($_POST['makers']),
        'opmerking' => inpFilt($_POST['opmerking'])
    ];
}

if(isset($oldName)) {
    $store = App::resolve('reeks')->updateReeks($ids, $_POST);
}

/* If reeks was not stored properly, prep the correct _flash data and user feedback, before returning to the pop-in again. */
if(is_string($store)) {
    $flash = [
        'oldForm' => $oldForm,
        'feedback' => [
            'error' => $store
        ],
        'tags' => [
            'pop-in' => 'reeks-maken'
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('beheer#reeks-maken-pop-in', TRUE);
}

App::resolve('session')->unflash();

$flash = [
    'feedback' => [
        'updated' => "De reeks {$oldName} \n is aangepast, met de naam {$_POST['naam']} !"
]];

App::resolve('session')->flash($flash);
return App::redirect('beheer', TRUE);