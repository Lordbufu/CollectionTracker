<?php

use App\Core\App;

/* Validate the user input in the POST. */
$form = App::resolve('form')::validate($_POST);

/* Store the filted string inputs, that can be returned to re-fill the form on failures. */
$temp = [
    'naam' => $_POST['naam'],
    'email' => $_POST['email']
];

/* Store the errors and filered form data, and return to the registration pop-in. */
if(is_array($form)) {
    $flash = [
        'oldForm' => $temp,
        'feedback' => $form
    ];

    App::resolve('session')->flash($flash);
    return App::redirect('#account-maken-pop-in');
}

/* Attempt to create the user from the provide input */
$user = App::resolve('user')->createUser($_POST);

/* Check for errors, prep user feedback and store oldForm data (if not stored), then redirect back to the register pop-in. */
if(is_string($user)) {
    $flash = [
        'oldForm' => $temp,
        'feedback' => [
            'store-error' => $user
    ]];

    App::resolve('session')->flash($flash);
    return App::redirect('#account-maken-pop-in');
}

/* Unset any 'redundant' session tags that might need to be removed */
if(isset($_SESSION['_flash'])) {
    App::resolve('session')->remVar('_flash', [
        'register', 'oldForm'
    ]);
}

$uName = App::resolve('user')->getName([
    'Gebr_Email' => $_POST['email']
]);

$flash = [
    'tags' => [
        'pop-in' => 'login'
    ],
    'feedback' => [
        'user-created' => "De Gebruiker: {$uName}. <br> Is aangemaakt, en u kan nu inloggen!"
    ]
];

/* Store a tag for the login pop-in, the user feedback, and redirect to the user login pop-in. */
App::resolve('session')->flash($flash);
return App::redirect('home#login-pop-in', TRUE);