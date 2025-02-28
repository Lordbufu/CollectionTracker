<?php

use App\Core\App;

/* Store the relevant POST data incase we need to populate the pop-in again after errors. */
$oldForm = [
    'rIndex' => $_POST['rIndex'],
    'iIndex' => $_POST['iIndex'],
    'naam' => $_POST['naam'],
    'nummer' => $_POST['nummer'],
    'datum' => $_POST['datum'],
    'autheur' => $_POST['autheur'],
    'isbn' => $_POST['isbn'],
    'opmerking' => $_POST['opmerking']
];

$searchResult = App::resolve('isbn')->startRequest($_POST['isbn'], $_POST['rIndex'], TRUE);

if(is_string($searchResult)) {
    $flash = [
        'oldForm' => $oldForm,
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

if(!isset($newItem)) {
    $flash = [
        'oldForm' => $oldForm,
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