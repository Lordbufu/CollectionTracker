<?php

/*  TODO List:
        - Review the need for 'verifyUser($id)', not sure i really need it atm.
 */

namespace App\Core;

use App\Core\App;

class Authenticator {
    protected $user;

    /* If no user data was set, attempt to get the user based on the provided $id (Assoc Array), and return true/false based on the outcome. */
    protected function set($id) {
        if(!isset($this->user)) { $this->user = App::resolve('database')->prepQuery('select', 'gebruikers', $id)->getSingle(); }
        if(is_string($this->user)) { return FALSE; }
        return TRUE;
    }

    /*  login($user):
            $user (Assoc Array) - The stored user data that has been verified.
            
            Return Value: Boolean ¿ (not used).
     */
    protected function login($user) {
        /* If a active user is set, destroy the session first. */
        if(isset($_SESSION['user']['id'])) { App::resolve('session')->destroy(); }
        /* If a session isnt started, make sure it is. */
        if(session_status() == 1) { session_start(); }
        /* Remove default guest user tag from the session if present. */
        if(isset($_SESSION['user']['rights'])) { $_SESSION['user']['rights'] = $this->user['Gebr_Rechten']; }
        /* Store user index for later authentication, includes and db requests. */
        $_SESSION['user']['id'] = $user['Gebr_Index'];
        $_SESSION['user']['rights'] = strtolower($user['Gebr_Rechten']);
        /* Regenerate session id on login as best practice behavior. */
        return session_regenerate_id(true);
    }

    /*  attempt($cred):
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
                    if(!$this->set([$key => $value])) { return FALSE; }
                }
            }
        }

        /* If the user was set, verify the provided password against the store password, and login the user with the local function. */
        if(password_verify($cred['Gebr_WachtW'], $this->user['Gebr_WachtW'])) { $this->login($this->user); }
        /* Return true/false based on if the user was set in the session. */
        if(!isset($_SESSION['user']['id'])) { return FALSE; }
        return TRUE;
    }

    /* Use SessionMan to end the session, return true/false depending on the outcome. */
    public function logout() {
        if(!App::resolve('session')->destroy()) { return FALSE; }

        return TRUE;
    }

    /* W.I.P. */
    /*  verifyUser($id):
            Function to verify the logged in user, based on the provided user index ($_SESSION['user']['id']).
                $id (Assoc Array)   - The user id tag, ready to be used for database requests.
            
            Return Value: Boolean.
     */
    public function verifyUser($id) {
        if(!isset($this->user)) {
            $this->user = App::resolve('database')->prepQuery('select', 'gebruikers', $id)->getSingle();
        }

        if(is_string($this->user)) {
            return FALSE;
        }

        return TRUE;
    }
}