<?php

use App\Core\App;

/* If we came here via the create reeks controller, prep the required _flash data. */
if(isset($_POST['naam'])) {
    $flash = [
        'tags' => [
            'pop-in' => 'reeks-maken',
            'method' => 'PUT',
            'rNaam' => $_POST['naam']
    ]];
}

/* If we came here via the edit reeks button, get the item that is requested to be edited, and prep the required _flash data. */
if(isset($_POST['index']) && isset($_POST['_method'])) {
    $reeks = App::resolve('reeks')->getSingReeks([
        'Reeks_Index' => $_POST['index']
    ]);

    $editReeks = [
        'index' => $reeks['Reeks_Index'],
        'naam' => $reeks['Reeks_Naam'],
        'makers' => $reeks['Reeks_Maker'],
        'opmerking' => $reeks['Reeks_Opmerk']
    ];
    
    $flash = [
        'oldItem' => $editReeks,
        'tags' => [
            'pop-in' => 'reeks-maken',
            'method' => $_POST['_method'],
            'rNaam' => $editReeks['naam']
    ]];
}

/* Set the _flash data, and redirect the reeks-maken-pop-in. */
App::resolve('session')->flash($flash);
return App::redirect('beheer#reeks-maken-pop-in', TRUE);