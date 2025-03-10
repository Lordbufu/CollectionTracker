<?php

use App\Core\App;

/* Condition for the 'item-toevoegen' controller menu. */
if(isset($_POST['naam'])) {
    $flash = [
        'tags' => [
            'pop-in' => 'items-maken',
            'method' => 'PUT',
            'rIndex' => App::resolve('reeks')->getKey([
                'Reeks_Naam' => $_POST['naam']],
                'Reeks_Index'
    )]];
}

/* Condition for the item-edit button in the table view. */
if(isset($_POST['iIndex'])) {
    $item = App::resolve('items')->getAllFor([
        'Item_Index' => $_POST['iIndex']
    ])[0];
    
    $editItem = [
        'rIndex' => $item['Item_Reeks'],
        'iIndex' => $item['Item_Index'],
        'naam' => $item['Item_Naam'],
        'nummer' => $item['Item_Nummer'],
        'datum' => $item['Item_Uitgd'],
        'autheur' => $item['Item_Auth'],
        'cover' => $item['Item_Plaatje'],
        'isbn' => $item['Item_Isbn'],
        'opmerking' => $item['Item_Opm']
    ];

    $flash = [
        'oldItem' => $editItem,
        'tags' => [
            'pop-in' => 'items-maken',
            'method' => $_POST['_method']
    ]];
}

/* Store the prepared flash data, and redirect to the 'items-maken-pop-in', preserving the _flash data. */
App::resolve('session')->flash($flash);

return App::redirect('beheer#items-maken-pop-in', TRUE);