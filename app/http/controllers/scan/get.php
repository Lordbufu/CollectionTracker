<?php

use App\Core\App;

/* Set the correct re-direct route based on user rights, and make sure the expected input is set. */
$route = ($_SESSION['user']['rights'] === 'user') ? 'gebruik' : 'beheer';

if(!isset($_POST['item-isbn']) || !isset($_POST['reeks-index'])) {
    App::resolve('session')->flash('feedback', ['error' => App::resolve('errors')->getError('forms', 'input-missing')]);
    return App::redirect($route, TRUE);
}

/* Depending on the route, parse\request the correct data from the Isbn Core Class. */
if($route === 'beheer') {
    $apiRequest = App::resolve('isbn')->startRequest($_POST['item-isbn'], $_POST['reeks-index'], TRUE);
} else {
    $apiRequest = App::resolve('isbn')->startRequest($_POST['item-isbn'], $_POST['reeks-index']);
}

/* If no array is returned or errors where set, i handover the correct user feedback, and redirect to the default page. */
if(!is_array($apiRequest) || isset($apiRequest['error'])) {
    if(is_string($apiRequest)) {
        App::resolve('session')->flash('feedback', ['error' => $apiRequest]);
    } else {
        App::resolve('session')->flash('feedback', ['error' => $apiRequest['error']]);
    }

    return App::redirect($route, TRUE);
}

/* If the Administrator action returend a title choice: */
if(isset($apiRequest[0]) && $apiRequest[0] === 'Titles') {
    $apiRequest['isbn-scanned'] = $_POST['item-isbn'];
    $apiRequest['reeks-index'] = $_POST['reeks-index'];

    App::resolve('session')->flash([
        'isbn-choices' => $apiRequest,
        'feedback' => [
            'choice' => 'Er zijn meerdere items gevonden, maakt aub een keuze die overeenkomt met wat u gescanned heeft !'
        ],
        'tags' => [
            'pop-in' => 'isbn-preview'
    ]]);

    return App::redirect("{$route}#isbn-preview", TRUE);
}

/* Get the item name for feedback messages, and evaluate the items collection state. */
$iName = App::resolve('items')->getKey($apiRequest, 'Item_Naam');
$aanwezig = App::resolve('collectie')->evalColl($apiRequest);

/* If the item wasnt evaluated properly, prepare the userfeedback and redirect back to the default user page. */
if(is_string($aanwezig)) {
    App::resolve('session')->flash('feedback', ['error' => $aanwezig]);
    return App::redirect($route, TRUE);
}

/* If it said the item was already in the user collection, remove it and add the associated feedback. */
if($aanwezig) {
    App::resolve('collectie')->remColl(['index' => $apiRequest['Item_Index']]);
    App::resolve('session')->flash('feedback', ['removed' => "Gescanned item: {$iName}. \n Is uit uw collectie verwijderdt!"]);
/* If it said the item wasnt in the user collection, add it and add the associated feedback. */
} else {
    App::resolve('collectie')->addColl(['iIndex' => $apiRequest['Item_Index'], 'rIndex' => $apiRequest['Item_Reeks']]);
    App::resolve('session')->flash('feedback', [ 'added' => "Gescanned item: {$iName}. \n Is aan uw collectie toegevoegd!"]);
}

/* Update the user collectie page-data, and redirect back to the default user page. */
if(isset($_SESSION['page-data']['collecties'])) { unset($_SESSION['page-data']['collecties']); }
App::resolve('session')->setVariable('page-data', ['collecties' => App::resolve('collectie')->getColl()]);
return App::redirect($route, TRUE);