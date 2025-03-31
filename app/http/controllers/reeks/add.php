<?php

use App\Core\App;

/* Store the user input as is, for pre-filling the form on errors. */
$oInput = $_POST;

/* Remove non-required empty or unset inputs. */
if(!isset($_POST['_method']) || empty($_POST['_method'])) { unset($oInput['_method']); }
if(!isset($_POST['index']) || empty($_POST['index'])) { unset($oInput['index']); }
if(!isset($_POST['maker']) || empty($_POST['maker'])) { unset($oInput['maker']); }
if(!isset($_POST['opmerking']) || empty($_POST['opmerking'])) { unset($oInput['opmerking']); }

/* Deal with image data next, so it can be included during errors. */
$plaatje = FALSE;

if(!empty($_FILES['plaatje']) && $_FILES['plaatje']['error'] === 0) {
    $cover = App::resolve('file')->procFile($_FILES['plaatje']);
    if(!is_array($cover)) {
        $oInput['plaatje'] = $cover;
        $plaatje = TRUE;
    } else {
        $plaatje = $cover;
    }
}

/* Validate the POST data, and process it for the database operation. */
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

/* If the validation passed, attempt to store the POST data, and deal with the potential errors. */
$store = App::resolve('reeks')->createReeks($uInput);

if(is_string($store)) {
    App::resolve('session')->flash([
        'feedback' => [
            'error' => $store
        ],
        'oldForm' => $oInput,
        'tags' =>[
            'pop-in' => 'reeks-maken'
    ]]);

    return App::redirect('beheer#reeks-maken-pop-in', TRUE);
}

/* Clear old session _flash data, update the session page-data for the 'reeks' key, provide usefull feedback, and redirect to the default 'beheer' page. */
App::resolve('session')->unflash();

if(isset($_SESSION['page-data']['reeks'])) {
    unset($_SESSION['page-data']['reeks']);
    App::resolve('session')->setVariable('page-data', ['reeks' => App::resolve('reeks')->getAllReeks()]);
}

App::resolve('session')->flash('feedback', ['success' => "De reeks: {$oInput['naam']} \n Is aangemaakt en zou nu in de lijst moeten staan!"]);
return App::redirect('beheer', TRUE);