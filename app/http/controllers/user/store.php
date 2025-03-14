<?php

use App\Core\App;

/* Store the POST data as user input, and remove the pw from it, and validate the POST data. */
$uInput = $_POST;
unset($uInput['wachtwoord']);
unset($uInput['wachtwoord-bev']);

$form = App::resolve('form')::validate($_POST);

/* Clean up user inpit, store the errors and user input, and return to the registration pop-in. */
if(is_array($form)) {
    App::resolve('session')->flash([
        'oldForm' => $uInput,
        'feedback' => $form,
        'tags' => [
            'pop-in' => 'register'
    ]]);

    return App::redirect('#account-maken-pop-in', TRUE);
}

/* Attempt to create the user from the provide input */
$user = App::resolve('user')->createUser($_POST);

/* Check for errors, prep user feedback and store oldForm data (if not stored), then redirect back to the register pop-in. */
if(is_string($user)) {
    App::resolve('session')->flash([
        'oldForm' => $uInput,
        'tags' => [
            'pop-in' => 'register'
        ],
        'feedback' => [
            'store-error' => $user
    ]]);

    return App::redirect('#account-maken-pop-in', TRUE);
}

/* Clear old session _flash data. */
App::resolve('session')->unflash();

/* Prep the user name for the welcome message, and set the session _flash data, before redirecting to the login. */
$uName = App::resolve('user')->getName(['Gebr_Email' => $_POST['email']]);

/* Store a tag for the login pop-in, the user feedback, and redirect to the user login pop-in. */
App::resolve('session')->flash([
    'tags' => [
        'pop-in' => 'login'
    ],
    'feedback' => [
        'user-created' => "De Gebruiker: {$uName}. <br> Is aangemaakt, en u kan nu inloggen!"
]]);

return App::redirect('home#login-pop-in', TRUE);