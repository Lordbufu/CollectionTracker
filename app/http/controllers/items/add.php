<?php

use App\Core\App;

/* Filter the user input, so i can savely return it to the page. */
$oldFilData = [
    'rIndex' => $_POST['rIndex'],
    'naam' => inpFilt($_POST['naam']),
    'nummer' => $_POST['nummer'],
    'datum' => $_POST['datum'],
    'autheur' => inpFilt($_POST['autheur']),
    'isbn' => $_POST['isbn'],
    'opmerking' => inpFilt($_POST['opmerking'])
];

/*  Process the cover if one was set via the HTML input,
    store in file scope variable, for when everything was validated,
    inlude file string as old form data, for where there are issues validating.
 */
if(!empty($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
    $cover = App::resolve('file')->procFile($_FILES['cover']);
    $oldFilData['cover'] = $cover ?? '';
}

/*  Process the cover if one was set via the barcode scanner,
    inlude file string as old form data, for where there are issues validating.
 */
if(isset($_SESSION['_flash']['newCover'])) {
    $cover = App::resolve('file')->procUrl($_SESSION['_flash']['newCover']);
    $oldFilData['cover'] = $cover ?? '';
}

/* Validate the POST data. */
$form = App::resolve('form')::validate($_POST);

/*  If validation failed,
    prep the expected feeback, tags and old formdata in the _flash memory,
    then redirecting to the pop-in again with the _flash memory intact.
 */
if(is_array($form)) {
    $flash = [
        'feedback' => $form,
        'oldForm' => $oldFilData,
        'tags' => [
            'pop-in' => 'items-maken'
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('beheer#items-maken-pop-in', TRUE);
}

/*  Prep user input and cover image string for the PDO to store it,
    and then attemp to store it.
 */
$dbData = $_POST;

if(isset($cover)) {
    $dbData['cover'] = $cover;
}

$store = App::resolve('items')->createItem($dbData);

/*  If db action failed,
    prep the expected feeback, tags and old formdata,
    then redirecting to the pop-in again preserving the _flash memory.
 */
if(is_string($store)) {
    $flash = [
        'oldForm' => $oldFilData,
        'feedback' => [
            'error' => $store
        ],
        'tags' => [
            'pop-in' => 'items-maken'
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('beheer#items-maken-pop-in', TRUE);
}

/*  When the items was store,
    we prep the correct feedback,
    and update the 'items' page-data if it was set.
 */
App::resolve('session')->flash('feedback', [
    'success' => "Het item: {$oldFilData['naam']} \n Is aangemaakt en zou nu in de lijst moeten staan!"
]);

if(isset($_SESSION['page-data']['items'])) {
    App::resolve('session')->setVariable('page-data', [
        'items' => App::resolve('items')->getAllFor([
            'Item_Reeks' => $_POST['rIndex']
        ])
    ]);
}

/* unset any old _flash data that is no longer required, */
if(isset($_SESSION['_flash']['tags']['pop-in'])) {
    if(isset($_SESSION['_flash']['oldForm'])) {
        unset($_SESSION['_flash']['oldForm']);
    }

    if(isset($_SESSION['_flash']['oldItem'])) {
        unset($_SESSION['_flash']['oldItem']);
    }

    if(isset($_SESSION['_flash']['newCover'])) {
        unset($_SESSION['_flash']['newCover']);
    }
    
    unset($_SESSION['_flash']['tags']['pop-in']);
}

/* and redirect to the 'beheer'-page preserving the flash data (user feedback). */
return App::redirect('beheer', TRUE);