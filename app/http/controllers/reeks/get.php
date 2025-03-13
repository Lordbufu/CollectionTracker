<?php

use App\Core\App;

/* If a name was in the post, we use that to set the reeks data. */
if(isset($_POST['naam'])) {
    $reeks = App::resolve('reeks')->getSingReeks([
        'Reeks_Naam' => $_POST['naam']
    ]);
/* If a index was in the post, we use that to set the reeks data. */
} else if(isset($_POST['index'])) {
    $reeks = App::resolve('reeks')->getSingReeks([
        'Reeks_Index' => $_POST['index']
    ]);
}

/* If no error was returned, store the currect select reeks name in the page-data (filtered). */
if(!is_string($reeks)) {
    App::resolve('session')->setVariable('page-data', [
        'huidige-reeks' => $reeks['Reeks_Naam']
    ]);

    /* Attemp to get all items associated with the selected reeks. */
    $items = App::resolve('items')->getAllFor([
        'Item_Reeks' => $reeks['Reeks_Index']
    ]);
}

/* If no error was returned, store the items inthe session. */
if(!is_string($items)) {
    App::resolve('session')->setVariable('page-data', [
        'items' => $items
    ]);
}

/* Clear old session _flash data. */
App::resolve('session')->unflash();

/* Switch the user rights, and perform additional logic, and redirect to the correct page keeping the _flash data. */
switch($_SESSION['user']['rights']) {
    case 'user':
        App::resolve('session')->setVariable('page-data', [
            'collecties' => App::resolve('collectie')->getColl()
        ]);
        
        return App::redirect('gebruik', TRUE);
    case 'admin':
        return App::redirect('beheer', TRUE);
}