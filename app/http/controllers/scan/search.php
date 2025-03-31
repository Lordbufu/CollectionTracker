<?php

use App\Core\App;

/* Store the relevant POST data incase we need to populate the pop-in again after errors. */
$oInput = $_POST;

/* Attempt to find the ISBN data in the Google Api. */
$searchResult = App::resolve('isbn')->easyRequest($_POST['isbn']);

/* If there where errors, store the correct data in the session, before redirecting back to the pop-in. */
if(is_string($searchResult)) {
    App::resolve('session')->flash([
        'oldForm' => $oInput,
        'feedback' => [
            'error' => $searchResult
        ],
        'tags' => [
            'pop-in' => 'items-maken'
    ]]);

    return App::redirect('beheer#items-maken-pop-in', TRUE);
}

/* Check if i need to present a title choice to the user. */
if(isset($searchResult[0]) && $searchResult[0] === 'Titles') {
    /* Ensure the right POST data is passed on. */
    $searchResult['_method'] = $_POST['_method'];
    $searchResult['index'] = (int) $_POST['rIndex'];
    $searchResult['isbn'] = (int) $_POST['isbn'];

    App::resolve('session')->setVariable('page-data', ['oldItem' => $oInput]);
    App::resolve('session')->flash([
        'isbn-choices' => $searchResult,
        'feedback' => [
            'choice' => 'De volgende titles zijn gevonden, maak aub een keuze !'
        ],
        'tags' => [
            'pop-in' => 'isbn-preview'
    ]]);

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
    App::resolve('session')->flash([
        'oldForm' => $oInput,
        'feedback' => [
            'error' => App::resolve('errors')->getError('isbn', 'search-error')
        ],
        'tags' => [
            'pop-in' => 'items-maken'
    ]]);

    return App::redirect('beheer#items-maken-pop-in', TRUE);
}

/* Clear old session _flash data, flash the new data, and redirect to the correct pop-in, preserving said flash data. */
App::resolve('session')->unflash();
App::resolve('session')->flash([
    'newItem' => $newItem,
    'feedback' => [
        'gevonden' => 'De data die is gevonden is ingevult, controleer of deze klopt met wat er verwacht wordt !'
    ],
    'tags' => [
        'pop-in' => 'items-maken'
]]);

return App::redirect('beheer#items-maken-pop-in', TRUE);