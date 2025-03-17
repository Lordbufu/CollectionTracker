<?php
    use App\Core\App;

    // Added incase i also want to allow the user to change there own password.
    /* Set the correct re-direct route based on user rights, and make sure the expected input is set. */
    $route = ($_SESSION['user']['rights'] === 'user') ? 'gebruik' : 'beheer';

    /* Store the POST data as user input, remove the pw from it, and validate the POST data. */
    $uInput = $_POST;
    unset($uInput['wachtwoord']);
    unset($uInput['wachtwoord-bev']);

    $form = App::resolve('form')->validate($_POST);

    /* Deal with any validation errors, and redirect to the correct pop-in. */
    if(is_array($form)) {
        App::resolve('session')->flash([
            'oldForm' => $uInput,
            'feedback' => $form,
            'tags' => [
                'pop-in' => 'ww-reset'
        ]]);
    
        return App::redirect("{$route}#ww-reset-pop-in", TRUE);
    }

    /* Attempt to update the user its password. */
    $store = App::resolve('user')->updateUser($_POST);

    if(is_array($store)) {
        App::resolve('session')->flash([
            'oldForm' => $uInput,
            'feedback' => $store,
            'tags' => [
                'pop-in' => 'ww-reset'
        ]]);
    
        return App::redirect("{$route}#ww-reset-pop-in", TRUE);
    }

    /* Clear old session _flash data, store finished feedback, and redirect to the default user page. */
    App::resolve('session')->unflash();
    App::resolve('session')->flash(['feedback' => ['user-updated' => "Het wachtwoord van: {$_POST['email']} \n is veranderd, en zou nu gebruikt moeten kunnen worden."]]);
    return App::redirect($route, TRUE);