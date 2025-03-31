<?php

use App\Core\App;

/* Prep the user data, by looking at what crendtials are used first, and then storing the correct data keys. */
if(isset($_POST['accountCred']) && App::resolve('validator')->email($_POST['accountCred'])) {
    $uCred['Gebr_Email'] = $_POST['accountCred'];
} else {
    $uCred['Gebr_Naam'] = $_POST['accountCred'];
}

/* For the password, we just check if it was set. */
if(isset($_POST['wachtwoord'])) { $uCred['Gebr_WachtW'] = $_POST['wachtwoord']; }

/* If the above dint get set properly, store a login failed error for the user, and redirect back to the default page. */
if(count($uCred) !== 2) {
    App::resolve('session')->flash('feedback', ['error' => App::resolve('errors')->getError('forms', 'input-missing')]);
    return App::redirect('', TRUE);
}

/* Attempt to authenticate the user. */
$auth = App::resolve('auth')->attempt($uCred);

/* If authentication failed, store the proper feedback, the account name or email, the popin tag, and redirect back to the pop-in. */
if(!$auth) {
    App::resolve('session')->flash([
        'feedback' => [
            'failed' => App::resolve('errors')->getError('login', 'failed')],
        'tags' => [
            'pop-in' => 'login',
            'log-fail' => TRUE
        ],
        'oldForm' => [
            'accountCred' => $_POST['accountCred']
    ]]);

    return App::redirect('home#login-pop-in', TRUE);
}

/* Set the correct re-direct route based on user rights, and make sure the expected input is set. */
$route = ($_SESSION['user']['rights'] === 'user') ? 'gebruik' : 'beheer';
/* Get user name, and set a welcome message for the user. */
$userName = App::resolve('user')->getName(['Gebr_Index' => $_SESSION['user']['id']]);

/* Clear old session _flash data, and store the new feedback. */
App::resolve('session')->unflash();
App::resolve('session')->flash('feedback', ['login' => "Welcome: {$userName}, Uw login is geslaagd."]);

/* Redirect based on the route that was set. */
return App::redirect($route, TRUE);