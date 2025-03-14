<?php

use App\Core\App;

/* If a redirect or login tag are set, remove the 'redirect' tag, so the flash is auto-cleaned on new get requests. */
if(isset($_SESSION['_flash']['login']) || isset($_SESSION['_flash']['tags']['redirect'])) { App::resolve('session')->remVar('_flash', 'redirect'); }

/* Refresh any reeks data stored in the session page-data. */
App::resolve('session')->setVariable('page-data', ['reeks' => App::resolve('reeks')->getAllReeks()]);

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
    if(isset($_SESSION['page-data']['items'])) { unset($_SESSION['page-data']['items']); }
    App::resolve('session')->setVariable('page-data', [
        'items' => App::resolve('items')->getAllFor([
            'Item_Reeks' => App::resolve('reeks')->getKey(['Reeks_Naam' => $_SESSION['page-data']['huidige-reeks']], 'Reeks_Index')
        ])
    ]);
}

/* Return the users home view, keeping the '/beheer' page tag. */
return App::view('home/index.view.php');