<?php

use App\Core\App;

/* Validate the POST data first. */
$validate = App::resolve('form')::validate($_POST);

/* Store the user input as old input for error paths, and new for storing it in the database. */
$oInput = $_POST;
$uInput = App::resolve('process')->store('items', $_POST);

/* Deal with the potentially uploaded image file, or the stored image file in either the session or database. */
$plaatje = FALSE;

if($_FILES['cover']['error'] === 0) {                                               // Check if user input was used,
    $cover = App::resolve('file')->procFile($_FILES['cover']);
    if(!is_array($cover)) {
        $plaatje = TRUE;
    }
} else {                                                                            // or attempt to load from database if nothing was found otherwhise.
    $cover = App::resolve('database')->prepQuery('select', 'items', [
        'Item_Index' => $_POST['iIndex']
    ])->find('Item_Plaatje');
    $plaatje = TRUE;
}

/* If any error happened during validation or the image processing, store them properly and redirect to the pop-in. */
if(is_array($validate) || !$plaatje) {
    if(!$plaatje && !is_array($validate)) {
        $validate = ['file-error' => $cover];
    } else {
        $validate['file-error'] = $cover;
    }

    $flash = [
        'oldForm' => $oInput,
        'feedback' => $validate,
        'tags' => [
            'pop-in' => 'items-maken'
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('beheer#items-maken-pop-in', TRUE);
}

/* Store the cover image if it was processed */
$oInput['cover'] = $cover;
$uInput['Item_Plaatje'] = $cover;

/* Store the name of the edited item, as is stored in the DB before we update the record. */
$oldName = App::resolve('items')->getKey([
        'Item_Index' => $_POST['iIndex'],
        'Item_Reeks' => $_POST['rIndex']
    ],
    'Item_Naam'
);

/* Attempt to update the item record, and store feedback and redirect on errors. */
$store = App::resolve('items')->updateItems($uInput);

if(is_string($store)) {
    $flash = [
        'feedback' => [
            'error' => $store
        ],
        'oldForm' => $oInput,
        'tags' => [
            'pop-in' => 'items-maken'
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('beheer#items-maken-pop-in', TRUE);
}

/* Clear old session _flash data. */
App::resolve('session')->unflash();

/* Perpare the correct user feedback, based on if the item name changed or not. */
if($oldName !== $_POST['naam']) {
    App::resolve('session')->flash('feedback', [
        'klaar' => "Het item: {$oldName} \n Is voor uw aangepast met de naam: {$_POST['naam']} !"
    ]);
} else {
    App::resolve('session')->flash('feedback', [
        'klaar' => "Het item: {$oldName} \n is aangepast in de huidige reeks !"
    ]);
}

/* And redirect to the admin (beheer) page preserving the _flash memory. */
return App::redirect('beheer', TRUE);