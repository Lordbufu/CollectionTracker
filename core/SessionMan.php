<?php

namespace App\Core;

/*  SessionMan Class:
        The concept is fairly simple, i want to save the session files in the main project folder, and start a session on page entry.
        So instead of just starting a session on the entry point, i opted for a constructor triggered by the bootstrap.
        As with most things in this App, im considering everything is set to default, so i change .ini settings to ensure the expected result.
        For that reason im also using the build in GC, instead of requiring the end user to make timed cron jobs, despite it not being best pratice.

        __construct():
            The main function to init sessions and the default settings.
            Settings include Garbage Collection, and a costum savepath, dont need much else atm.
        
        Public functions:
            setVariable($data)          - A function to more easily set various things in the session data.
            clearVariable($data = [])   - ...
        
        $_SESSION content:
            - header    : Content that i required in the header, like js related things.
                - error (Assoc Array)   : Error feedback for the end user.
                - feedB (Assoc Array)   : General feedback for the end user.
            - user      : Content that i use check user related data.
                - id    (int)           : The id to bind the user to a specific session.
                - Admin (bool)          : To seperate users from admins, only set if said user is a admin.
            
 */

class SessionMan {
    protected $savePath = '../tmp/sessions/';

    function __construct() {
        // Set current hostname + uri.
        $adress = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        
        // Various ini settings to improve session security.
        ini_set('session.gc_probability', '1');
        ini_set('session.gc_divisor', '10');
        ini_set('session.gc_maxlifetime', '1800');
        ini_set('session.user_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', '1');  // Only use for production when its on https
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_trans_sid', '1');
        ini_set('session.referer_check', $adress);
        ini_set('session.cache_limiter', 'private');
        ini_set('session.sid_length', '128');   // Changed to 128 as adviced, because 256 (max) is a to long file name ?
        ini_set('session.side_bits_per_character', '6');
        ini_set('session.hash_function', 'sha512');

        // Check, create and set the save path.
        if(!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777, true);
            session_save_path($this->savePath);
        } else {
            session_save_path($this->savePath);
        }

        // (re)Start the session.
        session_start();
    }

    // endSession(): Destroy the current session and its related data.
    //      session_destroy does not clean the session variables, so we do that manually first.
    public function endSession() {
        session_unset();
        session_destroy();
    }

    //  setVariable($data): Designed to store data sets in multi-dimensional array format.
    //      $name (string)              - The name of the first session data key.
    //      $data (associative array)   - Data that needs to be added to the session.
    public function setVariable($name, $data = []) {
        foreach($data as $key => $value) {
            $_SESSION["$name"]["$key"] = $value;
        }
    }

    // W.I.P.
    public function clearVariable($data = []) {
        foreach($data as $key => $value){
            unset($_SESSION["$value"]);
        }
    }
}
?>