<?php

use App\Core\App;

/* Store the user input as is, for pre-filling the form on errors. */
$oInput = $_POST;

/* Remove non-required empty inputs. */
if(empty($_POST['index'])) { unset($oInput['index']); }
if(empty($_POST['maker'])) { unset($oInput['maker']); }
if(empty($_POST['opmerking'])) { unset($oInput['opmerking']); }

/* Deal with image data next, so it can be included during errors. */
$plaatje = FALSE;

/* Check for included user input files. */
if(!empty($_FILES['plaatje']) && $_FILES['plaatje']['error'] === 0) {
    $cover = App::resolve('file')->procFile($_FILES['plaatje']);
    if(!is_array($cover)) {
        $oInput['plaatje'] = $cover;
    } else {
        $plaatje = $cover;
    }
/* Attempt to request any stored cover images, but ignore if nothing was returned */
} else if($_FILES['plaatje']['error'] !== 0 && isset($_POST['index'])) {
    $cover = App::resolve('reeks')->getKey(['Reeks_Index' => $_POST['index']], 'Reeks_Plaatje');
    if(isset($cover)) {
        $oInput['plaatje'] = $cover;
    } else if(!empty($cover)) {
        $plaatje = $cover;
    }
}

/* Validate the form data, and process the post data for the database operation. */
$form = App::resolve('form')::validate($oInput);
$uInput = App::resolve('process')->store('reeks', $oInput);

/* Check if there were any errors so far. */
if(is_array($form) || is_array($plaatje) || is_string($plaatje) || is_string($uInput)) {
    $feedback = [];
    
    if(is_array($form)) { $feedback = $form; }
    if(is_array($plaatje)) { $feedback['plaatje-error'] = $plaatje['error']; }
    if(is_string($plaatje)) { $feedback['plaatje-error'] = $plaatje; }
    if(is_string($uInput)) { $feedback['process-error'] = $uInput; }

    App::resolve('session')->flash([
        'feedback' => $feedback,
        'oldForm' => $oInput,
        'tags' => [
            'pop-in' => 'reeks-maken'
    ]]);

    return App::redirect('beheer#reeks-maken-pop-in', TRUE);
}

/* Store the old reeks name for later, and store the index for the the update operation. */
$oldName = App::resolve('reeks')->getKey(['Reeks_Index' => $_POST['index']], 'Reeks_Naam');
$ids = ['Reeks_Index' => $_POST['index']];

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