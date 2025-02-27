<?php

use App\Core\App;

/* If a redirect or login tag are set, */
if(isset($_SESSION['_flash']['login']) || isset($_SESSION['_flash']['tags']['redirect'])) {
    /* Remove the 'redirect' tag, so the flash is auto-cleaned on new get requests. */
    App::resolve('session')->remVar('_flash', 'redirect');
}

/* Request the newest reeks data, */
$rRequest = App::resolve('reeks')->getAllReeks();

/* and refresh the stored reeks data with the newly requested data. */
if(is_array($rRequest)) {
    App::resolve('session')->setVariable('page-data', [
        'reeks' => $rRequest
    ]);
}

/* Check if any kind of back/return/close button was pressed, */
if(isset($_POST['close']) || isset($_POST['return'])) {
    /* for the return to reeks button i need to clean up the selected tag and all associated items. */
    if(isset($_POST['return'])) {
        App::resolve('session')->remVar('page-data', 'huidige-reeks');
        App::resolve('session')->remVar('page-data', 'items');
    }

    /* Always unset the session _flash memory */
    App::resolve('session')->remVar('_flash', 'tags');

    /* In all other cases, a redirect with out the preserve flash memory tags is enough. */
    return App::redirect('beheer', FALSE);
}

/* If a Reeks was selected by the user, (ensures all items are up-to-date even if a admin added it between page refreshes.). */
if(isset($_SESSION['page-data']['huidige-reeks'])) {
    /* Get the index of said reeks, and request all associated items. */
    $rId = App::resolve('reeks')->getId([
        'Reeks_Naam' => $_SESSION['page-data']['huidige-reeks']
    ]);

    $iRequest = App::resolve('items')->getAllFor([
        'Item_Reeks' => $rId
    ]);

    /* Store all said items in the session. */
    App::resolve('session')->setVariable('page-data', [
        'items' => $iRequest
    ]);
}

/* Return the home view, keeping the '/gebruik' page tag. */
return App::view('home/index.view.php', null, TRUE);