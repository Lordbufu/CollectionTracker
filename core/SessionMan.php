<?php
/*  Test code to see how i want to handle sessions in this App, so far i have been doing ok without it.
    Things to consider for using this, is to prevent non-users from using the database, so i need to link accounts to sessions.
    There might also be an added benefit for account validation, and potentially reduce the amount of JS being used atm.

    I still want to try and not use any cookies for this, but i might have to end up having to use cookies.

    This is going to be a hot mess of code for a while.
 */

namespace App\Core;

class SessionMan {
    protected $session_save_loc = '../tmp/sessions/';                                   // Set a path for storing sessions
    protected $gc_time = '../tmp/php_session_last_gc';                                  // Create file path for tacking the last garbage collection
    protected $gc_period = 1800;                                                        // Create a set interval for when the collection should trigger

    //  TODO: Do something constructive if the name and rights din't pass the validation.
    protected function set_session_id($name, $rights) {                                 // Function to set the session ID and start the session
        $valName = preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $name);                       // Check if name is a valid string for the id
        $valRights = preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $rights);                   // Check if rights is a valid string for the id

        if($valName > 0 && $valRights > 0) {                                            // Evaluate the outcome of the params validation
            if(session_status() == PHP_SESSION_NONE) {                                  // check if there are no sessions active
                session_name('user-' . $name);                                          // set a session name
                session_id($name . '-' . $rights);                                      // give a new id for the session
                session_start();                                                        // start new session
            } else {                                                                    // If there are session active
                session_unset();                                                        // remove all session data
                session_destroy();                                                      // close the current session
                session_id($name, $rights);                                             // give a new id for the session
                session_start();                                                        // start new session
            }
        } else {
            return;                                                                     // For now we do nothing if the strings are invalid
        }

        return;                                                                         // Return to caller
    }

    // concept for default session if think in need those.
    public function start_default_session() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        } else {
            session_destroy();
            session_start();
        }

        return;
    }

    public function start_account_session($name, $rights) {                             // function specifically for account related sessions.
        ini_set('session.use_cookies', '0');                                            // the cookieless journey stays alive
        session_save_path($this->session_save_loc);                                     // set save path for session files
        $this->set_session_id($name, $rights);                                          // set session id and start a new session.

        return;                                                                         // return to caller
    }

    public function stop_account_session() {                                            // function to stop and clean up session data
        session_unset();                                                                // Remove session variables

        if(file_exists($this->gc_time)) {                                               // Check if there is a gc tracking file
            if(filemtime($this->gc_time < time() - $this->gc_period)) {                 // if the clean up period has passed
                session_gc();                                                           // do the garbage collection
                touch($this->gc_time);                                                  // touch file and reset the time
            }
        } else {                                                                        // If there is no file set
            touch($this->gc_time);                                                      // make it so we can track if a clean up is likely required
        }

        session_destroy();                                                              // destroy all session data
        session_write_close();                                                          // close session ?

        return;                                                                         // return to caller
    }
}
?>