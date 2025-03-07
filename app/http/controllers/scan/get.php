<?php

use App\Core\App;

/* Define redirect keyword, based on wat type of user rights are stored in the session. */
$route = ($_SESSION['user']['rights'] === 'user') ? 'gebruik' : 'beheer';

/*  Check if all expected input was set,
    resolve the session class to flash user feedback data, and resolve the forms input-missing error into it,
    then redirect to the curen user-page preserving the flash data.
 */
if(!isset($_POST['item-isbn']) || !isset($_POST['reeks-index'])) {
    App::resolve('session')->flash('feedback', [
        'error' => App::resolve('errors')->getError('forms', 'input-missing')
    ]);

    return App::redirect($route, TRUE);
}

/*  Attempt to request data from the Google API with the scanned isbn,
    if user is admin switch the default function parameter to TRUE.
 */
if($route === 'beheer') {
    $apiRequest = App::resolve('isbn')->startRequest($_POST['item-isbn'], $_POST['reeks-index'], TRUE);
} else {
    $apiRequest = App::resolve('isbn')->startRequest($_POST['item-isbn'], $_POST['reeks-index']);
}

/*  If the expected array isnt returned,
    resolve the session class to flash user feedback data,
    and set the returned error into it,
    then redirect to the gebruik-page preserving the flash data.
 */
if(!is_array($apiRequest)) {           
    App::resolve('session')->flash('feedback', [
        'error' => $apiRequest
    ]);

    return App::redirect($route, TRUE);
}

/*  If there was a error matching a single item,
    to the current reeks items,
    flash the error for user feedback,
    and then i redirect to the gebruik-page preserving the flash data.
 */
if(isset($apiRequest['error'])) {
    App::resolve('session')->flash('feedback', [
        'error' => $apiRequest['error']
    ]);

    return App::redirect($route, TRUE);
}

/*  If the API request retruned only titles (Administrator only),
    then i save the choices in the _flash memory,
    i store a feedback message,
    and i set the correct tags data to show the pop-in and populate its hidden fields,
    before i redirect preserving the _flash memory data.
 */
if(isset($apiRequest[0]) && $apiRequest[0] === 'Titles') {
    $flash = [
        'isbn-choices' => $apiRequest,
        'feedback' => [
            'choice' => 'Er zijn meerdere items gevonden, maakt aub een keuze die overeenkomt met wat u gescanned heeft !'
        ],
        'tags' => [
            'pop-in' => 'isbn-preview',
            'isbn-scanned' =>  $_POST['item-isbn'],
            'reeks-index' => $_POST['reeks-index']
    ]];

    App::resolve('session')->flash($flash);

    return App::redirect("{$route}#isbn-preview", TRUE);
}

/* Get the item name for feedback messages, and evaluate the items collection state. */
$iName = App::resolve('items')->getKey($apiRequest, 'Item_Naam');
$aanwezig = App::resolve('collectie')->evalColl($apiRequest);

/*  If the evaluation had a eror,
    flash user feedback data,
    and set the returned error into it,
    then redirect to the gebruik-page preserving the flash data.
 */
if(is_string($aanwezig)) {
    App::resolve('session')->flash('feedback', [
        'error' => $aanwezig
    ]);

    return App::redirect($route, TRUE);
}

/*  If the present tag is still TRUE,
    i used the collection class to trigger a remove action,
    and i use the index returned from the API request,
    i also need to flash user feedback to the session,
    that tells the user what item was removed using the stored name.
 */
if($aanwezig) {
    App::resolve('collectie')->remColl([
        'index' => $apiRequest['Item_Index']
    ]);

    App::resolve('session')->flash('feedback', [
        'removed' => "Gescanned item: {$iName}. \n Is uit uw collectie verwijderdt!"
    ]);

/*  If the present tag is FALSE,
    i used the collection class to trigger a add action,
    and i use the index returned from the API request,
    i also need to flash user feedback to the session,
    that tells the user what item was added using the stored name.
 */
} else {
    App::resolve('collectie')->addColl([
        'iIndex' => $apiRequest['Item_Index'],
        'rIndex' => $apiRequest['Item_Reeks']
    ]);

    App::resolve('session')->flash('feedback', [
        'added' => "Gescanned item: {$iName}. \n Is aan uw collectie toegevoegd!"
    ]);
}

/*  Regardless of the present state,
    i need to update the collection data,
    by simply request all collection data again.
 */
App::resolve('session')->setVariable('page-data', [
    'collecties' => App::resolve('collectie')->getColl()
]);

/* And then i redirect to the gebruik-page preserving the flash data. */
return App::redirect($route, TRUE);