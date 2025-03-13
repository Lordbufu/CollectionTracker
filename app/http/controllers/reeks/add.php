<?php

use App\Core\App;

/* Sotre raw input for pre-filling the form agian after errors. */
$oInput = [
    'method' => $_POST['_method'],
    'naam' => $_POST['naam'],
    'makers' => $_POST['makers'],
    'opmerking' => $_POST['opmerking']
];

/* Validate the POST data, and process it for the database operation. */
$form = App::resolve('form')::validate($_POST);
$uInput = App::resolve('process')->store('reeks', $_POST);

/* Deal with image data next, so it can be included during errors. */
$plaatje = FALSE;

if(!empty($_FILES['plaatje']) && $_FILES['plaatje']['error'] === 0) {
    $cover = App::resolve('file')->procFile($_FILES['plaatje']);
    if(!is_array($cover)) {
        $oInput['plaatje'] = $cover;
        $uInput['Reeks_Plaatje'] = $cover;
        $plaatje = TRUE;
    }
}

if(is_array($form) || !$plaatje) {
    if(!$plaatje && is_array($form)) {
        $form['plaatje'] = $cover['error'];
    } else {
        $form = $cover;
    }

    App::resolve('session')->flash([
        'feedback' => $form,
        'oldForm' => $oInput,
        'tags' => [
            'pop-in' => 'reeks-maken'
    ]]);

    return App::redirect('beheer#reeks-maken-pop-in', TRUE);
}

/* If the validation passed, attempt to store the POST data, and deal with the potential errors. */
$store = App::resolve('reeks')->createReeks($uInput);

if(is_string($store)) {
    App::resolve('session')->flash([
        'feedback' => [
            'error' => $store
        ],
        'oldForm' => $oldFilData,
        'tags' =>[
            'pop-in' => 'reeks-maken'
    ]]);

    return App::redirect('beheer#reeks-maken-pop-in', TRUE);
}

/* Clear old session _flash data. */
App::resolve('session')->unflash();

/* Update the session page-data for the 'reeks' key. */
if(isset($_SESSION['page-data']['reeks'])) {
    App::resolve('session')->setVariable('page-data', [
        'reeks' => App::resolve('reeks')->getAllReeks()
    ]);
}

/* Provide usefull feedback, and redirect to the default 'beheer' page. */
App::resolve('session')->flash('feedback', [
    'success' => "De reeks: {$oldFilData['naam']} \n Is aangemaakt en zou nu in de lijst moeten staan!"
]);

return App::redirect('beheer', TRUE);