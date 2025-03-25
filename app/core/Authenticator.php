<?php

namespace App\Core;

use App\Core\App;

class Authenticator {
    protected $user;

    /*  set($id):
            If no user data was set, attempt to get the user based on the provided $id, and return true/false based on the outcome.
                $id (Assoc Arr) - The Id pair that should represent a user in the database.
            
            Return Value: Boolean.
     */
    protected function set($id) {
        if(!isset($this->user)) {
            $this->user = App::resolve('database')->prepQuery('select', 'gebruikers', $id)->getSingle();
        }

        if(is_string($this->user)) {
            return FALSE;
        }

        return TRUE;
    }

    /*  login($user):
            Function to login a verified user.
                $user (Assoc Array) - The user data.
            
            Return Value: Boolean.
     */
    protected function login($user) {
        /* If a active user is set, destroy the session first. */
        if(isset($_SESSION['user']['id'])) {
            App::resolve('session')->destroy();
        }

        /* If a session isnt started, make sure it is. */
        if(session_status() == 1) {
            session_start();
        }

        /* Store user data for database and middleware logic, guest user rights are simply overwritten. */
        $_SESSION['user']['id'] = $user['Gebr_Index'];
        $_SESSION['user']['rights'] = strtolower($user['Gebr_Rechten']);


        /* Regenerate session id on login as best practice behavior. */
        return session_regenerate_id(true);
    }

    /*  attempt($cred):
            Attempt to verify and login the user that is requesting a login.
                $name (String)      - The name/e-mail provided by the user.
                $password (String)  - The password provided by the user.
                $user (String)      - The result of the db request to find the user.

            Return Value: Boolean.
     */
    public function attempt($cred) {
        /* If no user data was set, loop over the credentials, */
        if(!isset($this->user)) {
            foreach($cred as $key => $value) {
                /* if the key is either the user email or name, check if that user can be set, and return false if not. */
                if($key === 'Gebr_Email' || $key === 'Gebr_Naam') {
                    if(!$this->set([$key => $value])) {
                        return FALSE;
                    }
                }
            }
        }

        /* Check the provide password against the stored password. */
        $verify = password_verify($cred['Gebr_WachtW'], $this->user['Gebr_WachtW']);

        /* If the user was set, verify the provided password against the store password, and login the user with the local function. */
        if($verify) {
            $login = $this->login($this->user);
        } else {
            return FALSE;
        }

        /* If the login was set, and not true, the login failed. */
        if(isset($login) && !$login) {
            return FALSE;
        }

        /* Else the user was logged in. */
        return TRUE;
    }

    /*  logout():
            Use SessionMan to end and destroy the session, return true/false depending on the outcome.
    
            Return Value: Boolean.
     */
    public function logout() {
        if(!App::resolve('session')->destroy()) {
            return FALSE;
        }

        return TRUE;
    }
}