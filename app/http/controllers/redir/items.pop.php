<?php

use App\Core\App;

if(isset($_POST['naam'])) {                             // If we came here via the item-toevoegen controller menu.
    $flash = [
        'tags' => [
            'pop-in' => 'items-maken',
            'method' => 'PUT',
            'rIndex' => App::resolve('reeks')->getId([
                'Reeks_Naam' => $_POST['naam']
            ])
    ]];
}

if(isset($_POST['iIndex'])) {                           // If we came here via the table edit item button.
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

App::resolve('session')->flash($flash);

return App::redirect('beheer#items-maken-pop-in', TRUE);