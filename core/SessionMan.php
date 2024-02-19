<?php

namespace App\Core;

/*  SessionMan Class:
        The concept is fairly simple, i want to save the session files in the main project folder, and start a session on page entry.
        So instead of just starting a session on the entry point, i opted for a constructor triggered by the bootstrap.

        __construct():
            The main function to init sessions and the default settings.
            Settings include Garbage Collection, and a costum savepath, dont need much else atm.
        
        Public functions:
            setVariable($data)          - A function to more easily set various things in the session data.
            clearVariable($data = [])   - ...
 */

class SessionMan {
    protected $savePath = '../tmp/sessions/';

    function __construct() {
        // Garbage Collection .ini settings.
        ini_set('session.gc_probability', '1');
        ini_set('session.gc_divisor', '10');
        ini_set('session.gc_maxlifetime', '1800');

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
    public function endSession() {
        session_destroy();
    }

    //  setVariable($data):
    //      $data (associative array) - Data that needs to be added to the session.
    public function setVariable($data) {
        foreach($data as $key => $value) {
            $_SESSION["$key"] = $value;
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