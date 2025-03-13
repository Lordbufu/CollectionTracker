<?php

use App\Core\App;

$oInput = [                                                                         // Store raw user input, for pre-filling the form on errors.
    'method' => $_POST['_method'],
    'naam' => $_POST['naam'],
    'makers' => $_POST['makers'],
    'opmerking' => $_POST['opmerking']
];

$uInput = App::resolve('process')->store('reeks', $_POST);                          // Process the user input specfically for reeks data.
$form = App::resolve('form')::validate($_POST);                                     // Validate the user input.
$plaatje = FALSE;

if(!empty($_FILES['plaatje']) && $_FILES['plaatje']['error'] === 0) {               // Deal with image data next, so it can be included during errors.
    $cover = App::resolve('file')->procFile($_FILES['plaatje']);
    if(!is_array($cover)) {
        $oInput['plaatje'] = $cover;
        $uInput['Reeks_Plaatje'] = $cover;
        $plaatje = TRUE;
    }
} else if(isset($_SESSION['_flash']['newCover'])) {                                 // This might not even be used anymore, review this.
    $cover = $_SESSION['_flash']['newCover'];
    $olInput['plaatje'] = $cover;
    $uInput['Reeks_Plaatje'] = $cover;
    $plaatje = TRUE; 
}

if(is_array($form) || !$plaatje) {
    if(!$plaatje && is_array($form)) {                                              // Add image error to form errors if both are set
        $form['plaatje'] = $cover['error'];
    } else {                                                                        // Set image error as form error if no form error was set.
        $form = $cover;
    }

    App::resolve('session')->flash([                                                // Process the validation error, return the correct data.
        'feedback' => $form,
        'oldForm' => $oInput,
        'tags' => [
            'pop-in' => 'reeks-maken'
    ]]);

    return App::redirect('beheer#reeks-maken-pop-in', TRUE);                        // Redirect to the pop-in, preserving the flash memory.
}

/* If the validation passed, attempt to store the POST data. */
$store = App::resolve('reeks')->createReeks($uInput);                               // Attempt to store the data in the database.

if(is_string($store)) {                                                             // Deal with store errors, preparing the correct data.
    App::resolve('session')->flash([
        'feedback' => [
            'error' => $store
        ],
        'oldForm' => $oldFilData,
        'tags' =>[
            'pop-in' => 'reeks-maken'
    ]]);

    return App::redirect('beheer#reeks-maken-pop-in', TRUE);                        // Redirect to the pop-in with the prepared data.
}

/* If the data was stored, provide usefull feedback in the _flash data, refresh the reeks paga-data and redirect to the 'beheer' */
App::resolve('session')->flash('feedback', [
    'success' => "De reeks: {$oldFilData['naam']} \n Is aangemaakt en zou nu in de lijst moeten staan!"
]);

App::resolve('session')->setVariable('page-data', [
    'reeks' => App::resolve('reeks')->getAllReeks()
]);

return App::redirect('beheer', TRUE);