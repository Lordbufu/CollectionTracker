<?php

use App\Core\App;

/* Unflash data after a login was processed. */
if(isset($_SESSION['_flash']['login']) || !isset($_SESSION['_flash']['tags']['redirect'])) {
    App::resolve('session')->unflash();
/* Also unflash data after a close/back button was used. */
} else if(isset($_POST['close']) || isset($_POST['return']) && isset($_SESSION['_flash']['tags']['redirect'])) {
    App::resolve('session')->unflash();
}

/* Always attempt to load the most current reeks data. */
App::resolve('session')->setVariable('page-data', [
    'reeks' => App::resolve('reeks')->getAllReeks()
]);

/* Always attempt to refresh the items for a reeks. */
if(isset($_SESSION['_flash']['tags']['reeks-index'])) {
    $ids = ['Item_Reeks' => $_SESSION['_flash']['tags']['reeks-index']];
}

if(isset($_SESSION['page-data']['huidige-reeks']) && !isset($ids['Item_Reeks'])) {
    $ids = [
        'Item_Reeks' => App::resolve('reeks')->getKey([
            'Reeks_Naam' => $_SESSION['page-data']['huidige-reeks']],
            'Reeks_Index'
    )];
}

if(isset($ids)) {
    App::resolve('session')->setVariable('page-data', [
        'items' => App::resolve('items')->getAllFor($ids)
    ]);
}

/* Always refresh the users collection data. */
$col = App::resolve('collectie')->getColl([
    'Gebr_Index' => $_SESSION['user']['id']
]);

App::resolve('session')->setVariable('page-data', [
    'collecties' => App::resolve('collectie')->getColl([
        'Gebr_Index' => $_SESSION['user']['id']
])]);

/* Return the requested view, preserving the 'gebruik' page tag. */
return App::view('home/index.view.php', null, TRUE);