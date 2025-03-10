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

/* Check if i need to present a title choice to the admin. */
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

// debug code use for cleaup runs.
dd($searchResult);

/* If a single result was found, process said result to present the data to the user for validation. */
if(is_array($searchResult)) {
    $newItem = [
        'iIndex' => $_POST['iIndex'],
        'naam' => $searchResult['title'],
        'nummer' => $_POST['nummer'],
        'datum' => $searchResult['publishedDate'],
        'opmerking' => $searchResult['description']
    ];

    if(isset($searchResult['authors']) && count($searchResult['authors']) == 1) {
        $newItem['autheur'] = $searchResult['authors'][0];
    } else {
        $newItem['autheur'] = 'meer dan 1 schrijver gevonden!';
    }

    if(isset($searchResult['industryIdentifiers']) && count($searchResult['industryIdentifiers']) == 1) {
        $newItem['isbn'] = $searchResult['industryIdentifiers'][0]['identifier'];
    } else {
        $newItem['isbn'] = $searchResult['industryIdentifiers'][1]['identifier'];
    }

    if(isset($searchResult['imageLinks']) && isset($searchResult['imageLinks']['thumbnail'])) {
        $newItem['cover'] = App::resolve('file')->procUrl($searchResult['imageLinks']['thumbnail']);
    } else {
        $newItem['cover'] = App::resolve('file')->procUrl($searchResult['imageLinks']['smallThumbnail']);
    }
}

/* This seems redundant, considering there is a error path before setting a new item ? */
if(!isset($newItem)) {
    $flash = [
        'oldForm' => $oInput,
        'feedback' => [
            'error' => App::resolve('errors')->getError('isbn', 'search-error')
        ],
        'tags' => [
            'method' => $_POST['_method'],
            'rIndex' => $_POST['rIndex'],
            'pop-in' => 'items-maken'
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('beheer#items-maken-pop-in', TRUE);
}

/* Prepare all the correct session data, and redirect to the pop-in to present the results to the user. */
$flash = [
    'oldForm' => $newItem,
    'feedback' => [
        'gevonden' => 'De data die is gevonden is ingevult, controleer of deze klopt met wat er verwacht wordt !'
    ],
    'tags' => [
        'method' => $_POST['_method'],
        'rIndex' => $_POST['rIndex'],
        'pop-in' => 'items-maken'
]];

App::resolve('session')->flash($flash);
return App::redirect('beheer#items-maken-pop-in', TRUE);