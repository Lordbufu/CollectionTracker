<?php

use App\Core\App;

/* Validate the POST data first. */
$validate = App::resolve('form')::validate($_POST);

/* Store the user input raw for pre-filling the form on errors, and store a processed verion for storing in the database. */
$oInput = $_POST;
$pInput = App::resolve('process')->store('items', $_POST);

/* Check if a file cover was added via the HTML input or isbn search or barcode scanner, and add it to both the old and processed input. */
$plaatje = FALSE;

if(!empty($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
    $cover = App::resolve('file')->procFile($_FILES['cover']);
    if(!is_array($cover)) {
        $oInput['cover'] = $cover ?? '';
        $pInput['Item_Plaatje'] = $cover;
        $plaatje = TRUE;
    }
} else if(isset($_SESSION['_flash']['newCover'])) {
    $cover = $_SESSION['_flash']['newCover'];
    $olInput['cover'] = $cover ?? '';
    $pInput['Item_Plaatje'] = $cover;
    $plaatje = TRUE;
}

/* Check for any error in the above process, and prepare the return data, before going back to the pop-in.  */
if(is_array($validate) || is_string($pInput) || !$plaatje) {
    $feedback = [];

    /* The order i store things here, is relevant to prevent overwriting already stored errors. */
    if(is_array($validate)) {
        $feedback = $validate;
    }
    
    if(is_string($pInput)) {
        $feedback['process-error'] = $pInput;
    }

    if(!$plaatje && isset($cover)) {
        $feedback['cover-error'] = $cover;
    }

    $flash = [
        'feedback' => $feedback,
        'oldForm' => $oInput,
        'tags' => [
            'pop-in' => 'items-maken'
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('beheer#items-maken-pop-in', TRUE); 
}

/* Attempt to store the processed input, and deal with any errors after. */
$store = App::resolve('items')->createItem($pInput);

if(is_string($store)) {
    $flash = [
        'oldForm' => $oInput,
        'feedback' => [
            'error' => $store
        ],
        'tags' => [
            'pop-in' => 'items-maken'
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('beheer#items-maken-pop-in', TRUE);
}

/* If the store had no errors, i start by refreshing the session page-data. */
if(isset($_SESSION['page-data']['items'])) {
    unset($_SESSION['page-data']['items']);

    App::resolve('session')->setVariable('page-data', [
        'items' => App::resolve('items')->getAllFor([
            'Item_Reeks' => $_POST['rIndex']
        ])
    ]);
}

/* Then i clean up all useless _flash data. */
if(isset($_SESSION['_flash']['tags']['pop-in'])) {
    $tags = ['oldForm', 'oldItem', 'newCover', 'tags'];

    App::resolve('session')->remVar('_flash', $tags);
}

/* Then i prepare the user feedback before returning back to default page. */
App::resolve('session')->flash('feedback', [
    'success' => "Het item: {$pInput['Item_Naam']} \n Is aangemaakt en zou nu in de lijst moeten staan!"
]);

return App::redirect('beheer', TRUE);