<?php
/*  Test code to see how i want to handle sessions in this App, so far i have been doing ok without it.
    Things to consider for using this, is to prevent non-users from using the database, so i need to link accounts to sessions.
    There might also be an added benefit for account validation, and potentially reduce the amount of JS being used atm.
 */

//  TODO: Figure out how i can remove old session cookies properly.
//  TODO: See if i can force a Garbage collection when i close a account session.
namespace App\Core;

class SessionMan {
    protected $savePath = '../tmp/sessions/';                                           // Save path i want on the server side.

    protected function init_session() {                                                 // Initialize the session settings,
        ini_set('session.gc_probability', '1');                                         // set session garbage collection probability to 1,
        ini_set('session.gc_divisor', '10');                                            // set divisor to 10 (should be 10% chance it fires on session init ?),
        ini_set('session.gc_maxlifetime', '1800');                                      // set max lifetime to 30min.

        if(!is_dir($this->savePath)) {                                                  // If the savepath is not present,
            mkdir($this->savePath, 0777, true);                                         // make the directory,
            session_save_path($this->savePath);                                         // then set the save path.
        } else {                                                                        // If the save path is already present,
            session_save_path($this->savePath);                                         // set the save path.
        }
    }

    public function default_session() {                                                 // Default session for the initial session,
        $this->init_session();                                                          // ensure every thing is configured correctly,
        session_start();                                                                // then start a default session.
    }

    public function bind_account($name) {                                               // Bind account to a new session name/id,
        session_destroy();                                                              // kill all previous session data,
        session_name($name);                                                            // set new session name use the user name,
        session_start();                                                                // start the new session,
        session_regenerate_id();                                                        // generate new id so its not the same as the default session.
    }

    public function remove_session() {                                                  // destroy function incase i need more things on logout.
        session_destroy();
    }
}
?>