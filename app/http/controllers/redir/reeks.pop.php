<?php

use App\Core\App;

/* If we came here via the create reeks controller, prep the required _flash data. */
if(isset($_POST['naam'])) {
    $flash = [
        'newReeks' => [
            'method' => 'PUT',
            'naam' => $_POST['naam']
        ],
        'tags' => [
            'pop-in' => 'reeks-maken'
    ]];
}

/* If we came here via the edit reeks button, get the item that is requested to be edited, and prep the required _flash data. */
if(isset($_POST['index']) && isset($_POST['_method'])) {
    $reeks = App::resolve('reeks')->getSingReeks([
        'Reeks_Index' => $_POST['index']
    ]);

    $flash = [
        'oldItem' => [
            'method' => $_POST['_method'],
            'index' => $reeks['Reeks_Index'],
            'naam' => $reeks['Reeks_Naam'],
            'makers' => $reeks['Reeks_Maker'],
            'opmerking' => $reeks['Reeks_Opmerk']
        ],
        'tags' => [
            'pop-in' => 'reeks-maken'
    ]];
}

/* Clear old session _flash data, and set the new flash data, before redirecting to the reeks-maken pop-in. */
App::resolve('session')->unflash();
App::resolve('session')->flash($flash);
return App::redirect('beheer#reeks-maken-pop-in', TRUE);