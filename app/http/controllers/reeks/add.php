<?php

use App\Core\App;

/* Store raw input for pre-filling the form agian after errors. */
$oInput = $_POST;

/* Remove non-required empty inputs. */
if(empty($_POST['index'])) { unset($oInput['index']); }
if(empty($_POST['maker'])) { unset($oInput['maker']); }
if(empty($_POST['opmerking'])) { unset($oInput['opmerking']); }

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

/* Attempt to catch errors in all possible variations so far. */
if(is_array($form) || is_string($uInput) || is_array($plaatje)) {
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