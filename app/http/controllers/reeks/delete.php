<?php

use App\Core\App;

/* Set the required id pairs to remove all item, collection and reeks data. */
$idPairs = [
    'reeks' => [
        'Reeks_Index' => $_POST['index'],
        'Reeks_Naam' => $_POST['naam']
    ],
    'items' => [
        'Item_Reeks' => $_POST['index']
    ],
    'collectie' => [
        'Reeks_Index' => $_POST['index']
]];

/* First i attempt to remove the items that are part of the reeks. */
$remItems = App::resolve('items')->remItems($idPairs['items']);

if(is_string($remItems)) {
    App::resolve('session')->flash([
        'feedback' => $remItems
    ]);

    return App::redirect('beheer', TRUE);
}

/* Then i attempt to remove the reeks itself. */
$remReeks = App::resolve('reeks')->remReeks($idPairs['reeks']);

if(is_string($remReeks)) {
    App::resolve('session')->flash([
        'feedback' => $remReeks
    ]);

    return App::redirect('beheer', TRUE);
}

/* Then i attempt to clean up all related collection data. */
$remCollectie = App::resolve('collectie')->remCollAdmin($idPairs['collectie']);

if(is_string($remCollectie)) {
    App::resolve('session')->flash([
        'feedback' => $remCollectie
    ]);

    return App::redirect('beheer', TRUE);
}

/* Set the feedback message for the user, in the _flash memory. */
App::resolve('session')->flash('feedback', [
    'success' => "De reeks: {$_POST['naam']} en al de items en collecties ervan zijn verwijderd !"
]);

/* Incase items where set, i unset them as they are not required for the reeks view. */
if(isset($_SESSION['page-data']['items'])) {
    unset($_SESSION['page-data']['items']);
}

/* Update the reeks page-data to reflect the change. */
if(isset($_SESSION['page-data']['reeks'])) {
    App::resolve('session')->setVariable('page-data', [
        'reeks' => App::resolve('reeks')->getAllReeks()
    ]);
}

/* Redirect back to the beheer-page, preserving the _flash memory. */
return App::redirect('beheer', TRUE);