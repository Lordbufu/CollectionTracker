<?php

use App\Core\App;

/* Check if index was set, and request/store all required data for both the success and fail scenario. */
if(isset($_POST['index'])) {
    $ids = ['Reeks_Index' => $_POST['index']];
    $oldName = App::resolve('reeks')->getKey(['Reeks_Index' => $_POST['index']], 'Reeks_Naam');
    $oldForm = [
        'method' => $_POST['_method'],
        'index' => $_POST['index'],
        'naam' => $_POST['naam'],
        'makers' => $_POST['makers'],
        'opmerking' => $_POST['opmerking']
    ];
}

/* If a oldName was set, attemp to update the reeks, if not store a input missing error for user feedback. */
if(isset($oldName)) {
    $store = App::resolve('reeks')->updateReeks($ids, $_POST);
} else {
    $store = App::resolve('errors')->getError('forms', 'input-missing');
}

/* If reeks was not stored properly, prep the correct _flash data and user feedback, before returning to the pop-in again. */
if(is_string($store)) {
    App::resolve('session')->flash([
        'oldForm' => $oldForm,
        'feedback' => [
            'error' => $store
        ],
        'tags' => [
            'pop-in' => 'reeks-maken'
    ]]);
    return App::redirect('beheer#reeks-maken-pop-in', TRUE);
}

/* Start with a clean _flash memory, then prep the correct feedback and flash it, before redirecting back to the 'beheer' page*/
App::resolve('session')->unflash();

if($oldForm['naam'] === $oldName) {
    $flash = [
        'feedback' => [
            'updated' => "De reeks {$oldName} \n is aangepast !"
    ]];
} else {
    $flash = [
        'feedback' => [
            'updated' => "De reeks {$oldName} is aangepast, \n met de nieuwe naam {$_POST['naam']} !"
    ]];
}

App::resolve('session')->flash($flash);
return App::redirect('beheer', TRUE);