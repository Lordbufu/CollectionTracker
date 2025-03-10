<?php

use App\Core\App;

/* Store the relevant POST data incase we need to populate the pop-in again after errors. */
$oInput = $_POST;

/* Attempt to find the ISBN data in the Google Api. */
$searchResult = App::resolve('isbn')->startRequest($_POST['isbn'], $_POST['rIndex'], TRUE);

/* If there where errors, store the correct data in the session, before redirecting back to the pop-in. */
if(is_string($searchResult)) {
    $flash = [
        'oldForm' => $oInput,
        'feedback' => [
            'error' => $searchResult
        ],
        'tags' => [
            'method' => $_POST['_method'],
            'rIndex' => $_POST['rIndex'],
            'pop-in' => 'items-maken'
    ]];

    App::resolve('session')->flash($flash);

    return App::redirect('beheer#items-maken-pop-in', TRUE);
}

/* Check if i need to present a title choice to the user. */
if(isset($searchResult[0]) && $searchResult[0] === 'Titles') {
    $flash = [
        'isbn-choices' => $searchResult,
        'feedback' => [
            'choice' => 'De volgende titles zijn gevonden, maak aub een keuze !'
        ],
        'tags' => [
            'reeks-index' => $_POST['rIndex'],
            'isbn-scanned' => $_POST['isbn'],
            'pop-in' => 'isbn-preview'
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('beheer#isbn-preview', TRUE);
}

/* Process the API data, into a usuable format for our App. */
$newItem = App::resolve('procApi')->processData($searchResult);

/* Add the missing POST data that i need to process any further requests. */
$newItem['iIndex'] = (int) $_POST['iIndex'];
$newItem['rIndex'] = (int) $_POST['rIndex'];
$newItem['nummer'] = (int) $_POST['nummer'];
$newItem['method'] = $_POST['_method'];

/* If no relevant item data was parsed previously, i throw a search error, although im not sure its even relevant at this point. */
if(!isset($newItem['naam']) && !isset($newItem['isbn'])) {
    $flash = [
        'oldForm' => $oInput,
        'feedback' => [
            'error' => App::resolve('errors')->getError('isbn', 'search-error')
        ],
        'tags' => [
            'pop-in' => 'items-maken'
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('beheer#items-maken-pop-in', TRUE);
}

/* Prepare all the correct session data, and redirect to the pop-in to present the results to the user. */
$flash = [
    'oldForm' => $newItem,
    'newCover' => $newItem['cover'],
    'feedback' => [
        'gevonden' => 'De data die is gevonden is ingevult, controleer of deze klopt met wat er verwacht wordt !'
    ],
    'tags' => [
        'pop-in' => 'items-maken'
]];

/* Unset any 'old form' data i had stored during this process, to prevent unexpected behavior. */
if(isset($_SESSION['_flash']['oldItem'])) {
    unset($_SESSION['_flash']['oldItem']);
}

if(isset($_SESSION['_flash']['oldForm'])) {
    unset($_SESSION['_flash']['oldForm']);
}

/* Flash the new data, and redirect to the correct pop-in, preserving said flash data. */
App::resolve('session')->flash($flash);
return App::redirect('beheer#items-maken-pop-in', TRUE);