<?php
    use App\Core\App;

    /* Set the correct re-direct route based on user rights, and make sure the expected input is set. */
    $route = ($_SESSION['user']['rights'] === 'user') ? 'gebruik' : 'beheer';

    /* Store the POST data as user input, remove the pw from it, and validate the POST data. */
    $uInput = $_POST;
    unset($uInput['wachtwoord']);
    unset($uInput['wachtwoord-bev']);

    /* If it was a regular user, attempt to get a name */
    if($route === 'gebruik') {
        $cUser = App::resolve('user')->getUser([
            'Gebr_Index' => $_SESSION['user']['id']
        ]);
        
        if(!password_verify($_POST['wachtwoord1'], $cUser['Gebr_WachtW'])) {
            $dbData = $_POST;
            $dbData['Gebr_Index'] = $_SESSION['user']['id'];

            /* Attempt to update the user its password. */
            $store = App::resolve('user')->updateUser($dbData);
        } else {
            App::resolve('session')->flash([
                'oldForm' => $uInput,
                'feedback' => [
                    'error' => 'U probeert uw wachtwoord te veranderen, maar deze is hetzelfde, probeer aub een ander wachtwoord!'
                ],
                'tags' => [
                    'pop-in' => 'ww-reset'
            ]]);
            
            return App::redirect("{$route}#ww-reset-pop-in", TRUE);
        }
        
        /* Clear old session _flash data, store finished feedback, and redirect to the default user page. */
        App::resolve('session')->unflash();

        App::resolve('session')->flash([
            'feedback' => [
                'user-updated' => "Uw wachtwoord is veranderd, en zou nu gebruikt moeten kunnen worden."
            ]
        ]);

        return App::redirect($route, TRUE);
    }

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

    dd($_SESSION['user']);
    
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

    App::resolve('session')->flash([
        'feedback' => [
            'user-updated' => "Het wachtwoord van: {$_POST['email']} \n is veranderd, en zou nu gebruikt moeten kunnen worden."
        ]
    ]);

    dd($_SESSION['user']);

    return App::redirect($route, TRUE);