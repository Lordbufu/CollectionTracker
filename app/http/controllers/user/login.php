<?php

use App\Core\App;

/* Prep the user data, by looking at what crendtials are used first, and then storing the correct data keys. */
if(isset($_POST['accountCred']) && App::resolve('validator')->email($_POST['accountCred'])) {
    $uCred['Gebr_Email'] = $_POST['accountCred'];
} else {
    $uCred['Gebr_Naam'] = $_POST['accountCred'];
}

/* For the password, we just check if it was set. */
if(isset($_POST['wachtwoord'])) {
    $uCred['Gebr_WachtW'] = $_POST['wachtwoord'];
}

/* If the above dint get set properly, store a login failed error for the user, and redirect back to the default page. */
if(count($uCred) !== 2) {
    App::resolve('session')->flash('feedback', [
        'error' => App::resolve('errors')->getError('forms', 'input-missing')
    ]);
    
    return App::redirect('', TRUE);
}

/* Attempt to authenticate the user. */
$auth = App::resolve('auth')->attempt($uCred);

/* If authentication failed, store the proper feedback, the account name or email, the popin tag, and redirect back to the pop-in. */
if(!$auth) {
    $flash = [
        'feedback' => [
            'failed' => App::resolve('errors')->getError('login', 'failed')],
        'tags' => [
            'pop-in' => 'login',
            'log-fail' => TRUE
        ],
        'oldForm' => [
            'accountCred' => $_POST['accountCred']
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('home#login-pop-in', TRUE);
}

/* If a user was set from the database, get the user data, and store a welcome message in the '_flash'. */
if(isset($_SESSION['user']['id'])) {
    $user = App::resolve('user')->getUser([
        'Gebr_Index' => $_SESSION['user']['id']
    ]);

    $userName = $user['Gebr_Naam'];

    App::resolve('session')->flash('feedback', [
        'login' => "Welcome: {$userName}, Uw login is geslaagd."
    ]);
}

/* If the user is a regular user, unset the pop-in tag, and redirect to the correct controller. */
if($user['Gebr_Rechten'] === 'User') {
    unset($_SESSION['_flash']['tags']['pop-in']);
    return App::redirect('gebruik', TRUE);
}

/* If the user is a administrator user, unset the pop-in tag, and redirect to the correct controller. */
if($user['Gebr_Rechten'] === 'Admin') {
    unset($_SESSION['_flash']['tags']['pop-in']);
    return App::redirect('beheer', TRUE);
}