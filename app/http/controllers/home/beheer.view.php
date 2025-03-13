<?php

use App\Core\App;

/* If a redirect or login tag are set, remove the 'redirect' tag, so the flash is auto-cleaned on new get requests. */
if(isset($_SESSION['_flash']['login']) || isset($_SESSION['_flash']['tags']['redirect'])) {
    App::resolve('session')->remVar('_flash', 'redirect');
}

/* Request the newest reeks data, and refresh the stored reeks data with the newly requested data. */
$rRequest = App::resolve('reeks')->getAllReeks();

if(is_array($rRequest)) {
    App::resolve('session')->setVariable('page-data', [
        'reeks' => $rRequest
    ]);
}

/* Check if any kind of back/return/close button was pressed, and clean up the session, before redirectin to the users home page. */
if(isset($_POST['close']) || isset($_POST['return'])) {
    if(isset($_POST['return'])) {
        App::resolve('session')->remVar('page-data', 'huidige-reeks');
        App::resolve('session')->remVar('page-data', 'items');
    }

    App::resolve('session')->remVar('_flash', 'tags');
    return App::redirect('beheer', FALSE);
}

/*  If a Reeks was selected, ensures all items are up-to-date. */
if(isset($_SESSION['page-data']['huidige-reeks'])) {
    $rId = App::resolve('reeks')->getKey([
        'Reeks_Naam' => $_SESSION['page-data']['huidige-reeks']],
        'Reeks_Index'
    );

    $iRequest = App::resolve('items')->getAllFor([
        'Item_Reeks' => $rId
    ]);

    App::resolve('session')->setVariable('page-data', [
        'items' => $iRequest
    ]);
}

/* Return the users home view, keeping the '/beheer' page tag. */
return App::view('home/index.view.php', null, TRUE);