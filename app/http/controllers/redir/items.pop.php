<?php

use App\Core\App;

/* Condition for the 'item-toevoegen' controller menu. */
if(isset($_POST['naam'])) {
    $flash = [
        'newItem' => [
            '_method' => 'PUT',
            'rIndex' => App::resolve('reeks')->getKey(['Reeks_Naam' => $_POST['naam']], 'Reeks_Index')
        ],
        'tags' => [
            'pop-in' => 'items-maken'
    ]];
}

/* Condition for the item-edit button in the table view. */
if(isset($_POST['iIndex'])) {
    $item = App::resolve('items')->getAllFor(['Item_Index' => $_POST['iIndex']])[0];
    $editItem = [
        'rIndex' => $item['Item_Reeks'],
        'iIndex' => $item['Item_Index'],
        'naam' => $item['Item_Naam'],
        'nummer' => $item['Item_Nummer'],
        'datum' => $item['Item_Uitgd'],
        'autheur' => $item['Item_Auth'],
        'plaatje' => $item['Item_Plaatje'],
        'isbn' => $item['Item_Isbn'],
        'opmerking' => $item['Item_Opm'],
        '_method' => $_POST['_method']
    ];

    $flash = [
        'oldItem' => $editItem,
        'tags' => [
            'pop-in' => 'items-maken'
    ]];
}

/* Clear old session _flash data, set the new flash data, and redirect to the pop-in. */
App::resolve('session')->unflash();
App::resolve('session')->flash($flash);
return App::redirect('beheer#items-maken-pop-in', TRUE);