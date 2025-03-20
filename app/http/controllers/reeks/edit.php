<?php

use App\Core\App;

/* Store the user input as is, for pre-filling the form on errors. */
$oInput = $_POST;
/* Validate the form data, and process the post data for the database operation. */
$form = App::resolve('form')::validate($_POST);
$uInput = App::resolve('process')->store('reeks', $_POST);

/* Store the old reeks name for later, and store the index for the the update operation. */
$oldName = App::resolve('reeks')->getKey(['Reeks_Index' => $_POST['index']], 'Reeks_Naam');
$ids = ['Reeks_Index' => $_POST['index']];

/* Deal with image data next, so it can be included during errors. */
$plaatje = FALSE;

if(!empty($_FILES['plaatje']) && $_FILES['plaatje']['error'] === 0) {
    $cover = App::resolve('file')->procFile($_FILES['plaatje']);
    if(!is_array($cover)) {
        $oInput['plaatje'] = $cover;
        $uInput['Reeks_Plaatje'] = $cover;
        $plaatje = TRUE;
    }
}

/* Attempt to catch errors in all possible variations so far. */
if(is_array($form) || !$plaatje || is_string($uInput)) {
    /* Deal with all possible error combinations with the cover image. */
    if(!$plaatje && is_array($form)) { $form['plaatje-error'] = $cover['error']; }
    if(!$plaatje && is_string($uInput)) { $form = ['error-1' => $cover, 'error-2' => $uInput]; }
    if(!$plaatje) { $form = $cover; }
    /* Deal with the remaining $uInput error combinations. */
    if(is_array($form) && is_string($uInput)) { $form['input-error'] = $uInput; }
    if(is_string($uInput)) { $form = ['input-error' => $uInput]; }

    App::resolve('session')->flash([
        'feedback' => $form,
        'oldForm' => $oInput,
        'tags' => [
            'pop-in' => 'reeks-maken'
    ]]);

    return App::redirect('beheer#reeks-maken-pop-in', TRUE);
}

/* If a oldName was set, attemp to update the reeks, if not store a input missing error for user feedback. */
if(isset($oldName)) {
    $store = App::resolve('reeks')->updateReeks($ids, $uInput);
} else {
    $store = App::resolve('errors')->getError('forms', 'input-missing');
}

/* If reeks was not stored properly, prep the correct _flash data and user feedback, before returning to the pop-in again. */
if(is_string($store)) {
    App::resolve('session')->flash([
        'oldForm' => $oInput,
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

if($oInput['naam'] === $oldName) {
    $flash = [
        'feedback' => [
            'updated' => "De reeks {$oldName} \n is aangepast !"
    ]];
} else {
    $flash = [
        'feedback' => [
            'updated' => "De reeks {$oldName} is aangepast, \n met de nieuwe naam {$oInput['naam']} !"
    ]];
}

App::resolve('session')->flash($flash);
return App::redirect('beheer', TRUE);