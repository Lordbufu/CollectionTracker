<?php

namespace App\Core;

/*  SessionMan Class:
        Designed to manage sessions, with mostly default stuff, dint need anything to complex for this project.

        $savepath (string)  - Hardcoded savepath outside the webroot so they cant be requested.

        $_SESSION data structure reminder:
            - header    : Content that i required in the header, like js related things.
                - error (Assoc Array)   : Error feedback for the end user.
                - feedB (Assoc Array)   : General feedback for the end user.
                - broSto (Assoc Array)  : Data specific for the browser storage for JS.
            - user      : Content that i use to check user related data.
                - id    (int)           : The id to bind the user to a specific session.
                - Admin (bool)          : To seperate users from admins, only set if said user is a admin.
            - page-data : Content that is related to albums/series and collections.
                - albums (Assoc Array)      : All albums that needs to be displayed.
                - series (Assoc Array)      : All series that needs to be displayed.
                - huidige-serie (string)    : The current selected serie, for both the user and admin.
                - collections (Assoc Array) : All collection data that needs to be displayed.
 */

//  TODO: Maybe figure out if the SID Length can be longer, not sure if even required tbh ?
class SessionMan {
    protected $savePath = '../tmp/sessions/';

    /*  __construct():
            The session manager constructor, that ensure all settings are applied correctly.
            All ini settings are either security related, or garbage collection related.
            We also set a costum save path for sessions, so the files are stored outside the projects webroot.
            And it starts the session, since this is triggered via the user entry point.
     */
    function __construct() {
        $adress = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];   // Set current hostname + uri.
        
        // Various ini settings to improve session security.
        ini_set('session.gc_probability', '1');
        ini_set('session.gc_divisor', '10');
        ini_set('session.gc_maxlifetime', '1800');
        ini_set('session.user_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_trans_sid', '1');
        ini_set('session.referer_check', $adress);
        ini_set('session.cache_limiter', 'private');
        ini_set('session.sid_length', '128');                                       // Changed to 128 as adviced, because 256 (max) is a to long file name ?
        ini_set('session.side_bits_per_character', '6');
        ini_set('session.hash_function', 'sha512');

        // If the save path is not there, recursivly create the savepath, and set session savepath.
        if(!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777, true);
            session_save_path($this->savePath);
        // If the path is there, just set the session to be stored there.
        } else {
            session_save_path($this->savePath);
        }

        session_start();

        return;
    }

    /*  endSession():
            To properly end a session, we need to first unset the session variables, and then destroy the session.

            Return Type: None.
     */
    public function endSession() {
        session_unset();
        session_destroy();
        
        return;
    }

    /*  setVariable($data):
            Desgined to append data to session data keys, since i have to do this a lot, i made a function for it.

            $name (string)              - The name of the first session data key.
            $data (assoc/multiD array)  - Data that needs to be added to the session.

            Return Type: None.
     */
    public function setVariable($name, $data) {
        foreach($data as $key => $value) {
            if(!is_array($value)) {
                $_SESSION[$name][$key] = $value;
            } else {
                //die(print_r($data));    // Debug-line.
                //die(print_r($value));    // Debug-line.
                if(isset($value['Serie_Index'])) {
                    $_SESSION[$name]['series'] = $data;
                } elseif(isset($value['Album_Index'])) {
                    $_SESSION[$name]['albums'] = $data;
                } elseif(isset($value['Col_Index'])) {
                    $_SESSION[$name]['collections'] = $data;
                } elseif($key == 'feedB') {
                    $_SESSION[$name][$key] = $value;
                } elseif($key == 'error') {
                    $_SESSION[$name][$key] = $value;
                } elseif($key == 'broSto') {
                    $_SESSION[$name][$key] = $value;
                } else {
                    //die(print_r($data));    // Debug-line.
                    //die(print_r($key));    // Debug-line.
                    //die(print_r($value));    // Debug-line.
                    die('New condition required in setVariable()!!');   // Debug-line/condition.
                }
            }
        }

        return;
    }

    // W.I.P. ... i think its easier to just unset where i need it atm.
    public function clearVariable($key) {
        unset($_SESSION["$key"]);
    }
}
?>