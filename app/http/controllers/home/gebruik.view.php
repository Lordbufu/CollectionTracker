<?php

use App\Core\App;

/* Unflash data after a login was processed. */
if(isset($_SESSION['_flash']['login']) || !isset($_SESSION['_flash']['tags']['redirect'])) {
    App::resolve('session')->unflash();
}

/* Unflash data after a close/back button was used. */
if(isset($_POST['close']) || isset($_POST['return']) && isset($_SESSION['_flash']['tags']['redirect'])) {
    App::resolve('session')->unflash();
}

/* Always attempt to load the most current reeks data, ommiting any costum errors. */
$reeks = App::resolve('reeks')->getAllReeks();

if(!is_string($reeks)) {
    App::resolve('session')->setVariable('page-data', [
        'reeks' => $reeks
    ]);
}

/* Always attempt to refresh the items for a reeks, ommiting any costum errors. */
if(isset($_SESSION['_flash']['tags']['reeks-index'])) {
    $ids = [
        'Item_Reeks' => $_SESSION['_flash']['tags']['reeks-index']
    ];
}

if(isset($_SESSION['_flash']['tags']['huidige-reeks'])) {
    $ids = [
        'Item_Reeks' => $_SESSION['_flash']['tags']['huidige-reeks']
    ];
}

if(isset($ids)) {
    $items = App::resolve('items')->getAllFor($ids);
}

if(isset($items) && !is_string($items)) {
    App::resolve('session')->setVariable('page-data', [
        'items' => $items
    ]);
}

/* Always refresh the users collection data, ommiting any costum errors. */
if(isset($_SESSION['user']['id'])) {
    $col = App::resolve('collectie')->getColl([
        'Gebr_Index' => $_SESSION['user']['id']
    ]);

    if(!is_string($col)) {
        App::resolve('session')->setVariable('page-data', [
            'collecties' => $col
        ]);
    }
}

/* Return the requested view, preserving the 'gebruik' page tag. */
return App::view('home/index.view.php', null, TRUE);