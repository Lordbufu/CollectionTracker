<?php

use App\Core\App;

/* Store the user input as is, for pre-filling the form on errors. */
$oInput = $_POST;

/* Clean any non-required not set or empty inputs. */
if(!isset($_POST['_method']) || empty($_POST['_method'])) { unset($oInput['_method']); }
if(!isset($_POST['rIndex']) || empty($_POST['rIndex'])) { unset($oInput['rIndex']); }
if(!isset($_POST['iIndex']) || empty($_POST['iIndex'])) { unset($oInput['iIndex']); }
if(!isset($_POST['nummer']) || empty($_POST['nummer'])) { unset($oInput['nummer']); }
if(!isset($_POST['datum']) || empty($_POST['datum'])) { unset($oInput['datum']); }
if(!isset($_POST['autheur']) || empty($_POST['autheur'])) { unset($oInput['autheur']); }
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
$uInput = App::resolve('process')->store('items', $oInput);

/* Check if there were any errors so far, and append them to a array in the correct order. */
if(is_array($form) || is_array($plaatje) || is_string($plaatje) || is_string($uInput)) {
    $feedback = [];
    
    if(is_array($form)) { $feedback = $form; }
    if(is_array($plaatje)) { $feedback['plaatje-error'] = $plaatje['error']; }
    if(is_string($plaatje)) { $feedback['plaatje-error'] = $plaatje; }
    if(is_string($uInput)) { $feedback['process-error'] = $uInput; }

    App::resolve('session')->flash([
        'feedback' => $feedback,
        'oldItem' => $oInput,
        'tags' => [
            'pop-in' => 'items-maken'
    ]]);

    return App::redirect('beheer#items-maken-pop-in', TRUE); 
}

/* Attempt to store the processed input, and deal with any errors after. */
$store = App::resolve('items')->createItem($uInput);

if(is_string($store)) {
    App::resolve('session')->flash([
        'oldForm' => $oInput,
        'feedback' => [
            'error' => $store
        ],
        'tags' => [
            'pop-in' => 'items-maken'
    ]]);

    return App::redirect('beheer#items-maken-pop-in', TRUE);
}

/* If the store had no errors, i start by refreshing the session page-data. */
if(isset($_SESSION['page-data']['items'])) {
    unset($_SESSION['page-data']['items']);
    App::resolve('session')->setVariable('page-data', ['items' => App::resolve('items')->getAllFor(['Item_Reeks' => $_POST['rIndex']])]);
}

/* Clear old session _flash data, then prepare the user feedback before returning back to default page. */
App::resolve('session')->unflash();
App::resolve('session')->flash('feedback', ['success' => "Het item: {$oInput['Item_Naam']} \n Is aangemaakt en zou nu in de lijst moeten staan!"]);
return App::redirect('beheer', TRUE);