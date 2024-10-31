<?php
/*  TODO List:
        - Review setVariable() its special tags, because i think "isbn-search" isnt usefull at the end of the loop anymore.
        - Remove/replace the last debug lines, they should not longer be usefull, or need to be replaced with something more constructive.
        - Do a final review of the ini_set() settings in the __construct() function, to ensure im not doing something stupid or unwanted.
 */

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
                - albums (Assoc Array)      : All album data that needs to be displayed.
                - series (Assoc Array)      : All serie data that needs to be displayed.
                - collections (Assoc Array) : All collection data that needs to be displayed.
            - page-data : Special flags used to trigger specific logic.
                - serie-dupl (Assoc Array)      : The POST data from the duplicate serie.
                - alb-dupl (Assoc Array)        : The POST data from the duplicate album.
                - Album_Cover (blob)            : Temp store for any uploaded album covers, when a duplicate name was detected.
                - isbn-search (Assoc Array)     : The results of searching the Google API for a ISBN number.
                - huidige-serie (string)        : The current selected serie, for both the user and admin.
                - new-serie (string)            : The serie name that was added using the admin controller for creating a serie.
                - edit-serie (int)              : The series index of the serie that is requested for editing.
                - add-album (int)               : The serie index key, that the user wants to add a album to.
                - isbn-scan (string)            : A state that indicated the users wants to scan a barcode for its isbn/ean code.
                - mobile-details (Assoc Array)  : A temp store for album details on mobile phones, this might still be removed later on.
                - shown-titles (Assoc Array)    : Here i store what pop-in de user came from.
                    - bewerken  (boolean)       : Indicates the isbn search was doing via the edit pop-in.
                    - toevoegen (boolean)       : Indicates the isbn search was doing via the add pop-in.
 */
class SessionMan {
    protected $savePath = "../tmp/sessions/";

    /*  __construct():
            The session manager constructor, that ensure all settings are applied correctly.
            All ini settings are either security related, or garbage collection related.
            We also set a costum save path for sessions, so the files are stored outside the projects webroot.
            And it starts the session, since this is triggered via the user entry point.

            Return Type: None.
     */
    function __construct() {
        $adress = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        ini_set( "session.gc_probability", "1" );
        ini_set( "session.gc_divisor", "10" );
        ini_set( "session.gc_maxlifetime", "1800" );
        ini_set( "session.user_strict_mode", "1" );
        ini_set( "session.cookie_httponly", "1" );
        ini_set( "session.cookie_secure", "1" );
        ini_set( "session.cookie_samesite", "Strict" );
        ini_set( "session.use_trans_sid", "1" );
        ini_set( "session.referer_check", $adress );
        ini_set( "session.cache_limiter", "private" );
        ini_set( "session.sid_length", "128" );
        ini_set( "session.side_bits_per_character", "6" );
        ini_set( "session.hash_function", "sha512" );

        if(!is_dir($this->savePath)) {
            mkdir( $this->savePath, 0777, true );
            session_save_path( $this->savePath );
        } else {
            session_save_path( $this->savePath );
        }

        return session_start();
    }

    /*  endSession():
            To properly end a session, we need to first unset the session variables, and then destroy the session.

            Return Type: None.
     */
    public function endSession() {
        session_unset();
        return session_destroy();
    }

    /*  setVariable($data):
            Desgined to append data to session data keys, since i have to do this a lot, i made a function for it.
                $name (string)              - The name of the first session data key.
                $data (assoc/multiD array)  - Data that needs to be added to the session.

            Return Type: None.
     */
    public function setVariable( $name, $data ) {
        foreach( $data as $key => $value ) {

            /* Loop specifically for isbn-searches */
            if( $key == "isbn-search" ) {
                $_SESSION[$name][$key] = $value;
                return;
            }

            /* Main loop starting with anything that isnt a array */
            if( !is_array( $value ) ) {
                $_SESSION[$name][$key] = $value;
                return;

            /* Everything else that isnt single value */
            } else {

                /* Serie index values */
                if( isset( $value["Serie_Index"] ) ) {
                    $_SESSION[$name]["series"] = $data;
                    return;
                /* Album index values */
                } elseif( isset( $value["Album_Index"] ) ) {
                    $_SESSION[$name]["albums"] = $data;
                    return;
                /* Collection index values */
                } elseif( isset( $value["Col_Index"] ) ) {
                    $_SESSION[$name]["collections"] = $data;
                    return;
                /* All remaining tags that are expected */
                } elseif( $key == "feedB"  || $key == "error" || $key == "broSto" || $key == "album-dupl" || $key == "serie-dupl" || "isbn-search") {
                    $_SESSION[$name][$key] = $value;
                    return;

                //  TODO: Temp debug error, should never be reached during production.
                } else {
                    die("Unexpected data is being passed to the session setVariable function!");
                }
            }
        }
    }

    /*  checkVariable($store, $key):
            For certain processes, i need to be able to check if certain variables are set, or a combination of variables.
            Mostly designed to see if i can unset a variable, and not mess up the workflow of the App.
                $store  - String        -> The name of the store, for example 'page-data'.
                $keys   - Assoc Array   -> The keys in the store, that i need to know are set or not, can be a single key.

            Return Value: Boolean.
     */
    public function checkVariable( $store, $keys = [] ) {

        /* Look for the session store that as requested. */
        if( isset( $_SESSION[$store] ) ) {

            /* Loop over all entries in set store, and if the key was a string compare it with entry and return true. */
            foreach( $_SESSION[$store] as $entry => $value ) {
                if( is_string($keys) && $keys == $entry ) {
                    return TRUE;

                /* Loop over the keys array, and compare it with the entry from the outer foreach, return true if it matches. */
                } else {
                    foreach($keys as $key) {
                        if($key === $entry) {
                            return TRUE;
                        }
                    }
                }
            }

            /* If no matches are found return false. */
            return FALSE;
        /* If there was not session store set, i also return false. */
        } else {
            return FALSE;
        }
    }
}

?>