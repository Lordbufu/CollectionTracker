<?php

use App\Core\App;

/* Store the old item name, if the user came from the edit action */
if(!empty($_POST['iIndex']) && !empty($_POST['rIndex'])) {
    $oldName = App::resolve('items')->getName([
        'Item_Index' => $_POST['iIndex'],
        'Item_Reeks' => $_POST['rIndex']
    ]);
}

/* Store the POST data as oldForm data for all request types. */
$oldForm = [
    '_method' => $_POST['_method'],
    'rIndex' => $_POST['rIndex'],
    'iIndex' => $_POST['iIndex'] ?? '',
    'naam' => $_POST['naam'],
    'nummer' => $_POST['nummer'],
    'datum' => $_POST['datum'],
    'autheur' => $_POST['autheur'],
    'isbn' => $_POST['isbn'],
    'opmerking' => $_POST['opmerking']
];

/* Store post as temp, and set cover state to false. */
$temp = $_POST;
$plaatje = FALSE;

/* If cover input was set, convert it to a base64 string, and change the cover state to true. */
if($_FILES['cover']['error'] === 0) {
    $file = App::resolve('file')->procFile($_FILES['cover']);
    $plaatje = TRUE;
/* If no cover input was used, but a new cover was stored in the session flash, attempt to process that file. */
} elseif(isset($_SESSION['_flash']['newCover'])){
    $file = App::resolve('file')->procUrl($_SESSION['_flash']['newCover']);
    $plaatje = TRUE;
/* If no cover input was used, attempt to request the image from the databse, and change the cover state to true. */
} else {
    $file = App::resolve('database')->prepQuery('select', 'items', [
        'Item_Index' => $_POST['iIndex']
    ])->find('Item_Plaatje');

    $plaatje = TRUE;
}

/* If no image was found, store user feedback in the _flash memory. */
if(!$plaatje) {
    App::resolve('session')->flash('feedback', [
        'file-error' => $file
    ]);
/* If no errors, store the cover in temp and oldForm. */
} else {
    $temp['cover'] = $file;
    $oldForm['cover'] = $file;
}

/* Attempt to update the item using the unfiltered data. */
if(isset($oldName)) {
    $store = App::resolve('items')->updateItems($temp);
} else {
    $store = App::resolve('items')->createItem($temp);
}

/* Evalute the updat attempt, and store user feedback if problematic. */
if(is_string($store)) {
    App::resolve('session')->flash('feedback', [
        'error' => $store
    ]);
}

/* If any errors are stored, set the oldItem to the _flash memory, and redirect to the pop-in perserving said memory. */
if(isset($_SESSION['_flash']['feedback'])) {
    App::resolve('session')->flash('oldForm', $oldForm);
    return App::redirect('beheer#items-maken-pop-in', TRUE);
}

/* If no errors and a old name was stored, provide feedback on what item was updated. */
if(isset($oldName) && $oldName !== $_POST['naam']) {
    App::resolve('session')->flash('feedback', [
        'klaar' => "Het item: {$oldName} \n Is voor uw aangepast met de naam: {$_POST['naam']} !"
    ]);
/* Else provide feedback that the new item was added from the isbn scan functions. */
} else {
    App::resolve('session')->flash('feedback', [
        'klaar' => "Het item: {$oldForm['naam']} \n is toegevoegd aan de huidige reeks !"
    ]);
}

/* And redirect to the admin (beheer) page preserving the _flash memory. */
return App::redirect('beheer', TRUE);