<?php
/*  TODO List:
        - Search Tag -> PHP Warning:
            Some issues when making duplicate account names and duplicate e-mails, need to reproduce the error.
        - Search Tag -> Got error:
            Not entirely sure what caused this, seems like somehow there was no valid array returned for getting a user ?
        - Search Tag -> Debug Notes:
            Unexplained un-reproducable error, there is likely some code somewhere, that is missing the correct parameter.
 */

namespace App\Core;

/*  Reminder of the error array structure, that is required to display the errors:
        [ "error" => [ "fetchResponse" => { Message that needs to be displayed } ] ];
 */

use Exception;

// Refactored and tested for the new errors class
/*  User Class:
        To clean up the LogicController and processing class, i off-loaded the user related things to this class.
        Everything is based on return values, so i only need to evaluate most of the time, and sometimes pass on a error.
        Binding the user to the session, is for now only done in the Controller.
 */
class User {
    /* Protected user variable, to store the requested user. */
    protected $user;
    
    /*  setUser($data):
            Attempts to add user to database, and check for duplicate e-mails & names.
                $data           (Assoc Array)           - The user input, sanitized in the controller.
                $tempUsers      (Assoc Array)           - Temp local storage for all current users.
                $store          (String or Bool)        - The result of trying to store the requested user.
                $errorMsg (Assoc Array or Undefined)    - The potentially store errors during the user name and e-mail checks.

            Return Value:
                On Sucess   : Assoc Array
                On Duplicate: Assoc Array
                On Failed   : Assoc Array
     */
    public function setUser($data) {
        /* Message for when the user was added to the database */
        $userAdd = [ "userCreated" => "Gebruiker aangemaakt, u kunt nu inloggen!" ];
        /* Undefined error variable, it was generating errors before */
        $errorMsg;

        /* Attempt to request all current users */
        $tempUsers = App::get( "database" )->selectAll( "gebruikers" );

        /* Check if there where any users set, or if there wa a DB error */
        if( !is_string( $tempUsers ) ) {
            /* Loop over all users */
            foreach( $tempUsers as $key => $user ) {
                /* If a duplicate name detected, store the correct error */
                if( $user["Gebr_Naam"] === $data["Gebr_Naam"] ) {
                    $errorMsg["error"]["userError1"] = App::get( "errors" )->getError( "userNameErr" );
                }

                /* If a duplicate e-mail is detected, check if a error was set for a duplicate name and merge both errors */
                if( $user["Gebr_Email"] === $data["Gebr_Email"] ) {
                    $errorMsg["error"]["userError2"] = App::get( "errors" )->getError( "userEmailErr" );
                }
            }
        /* If there was a DB error getting all users, store the correct error */
        } else {
            $errorMsg["error"]["userError1"] = App::get( "errors" )->getError( "noUserErr" );
        }

        /* Evaluate if there are any errors stored, and attempt to insert the user if not */
        if( !isset( $errorMsg ) ) {
            $store = App::get( "database" )->insert( "gebruikers", $data );
        /* If there where, simply return the errors to the caller */
        } else {
            return $errorMsg;
        }

        /* If there was no error string from the DB, return a feedback message, else return a DB error message */
        return !is_string($store) ? $userAdd : [ "error" => App::get( "errors" )->getError( "dbFail" ) ];
    }

    /*  getUserName():
            Simple get user name function, that sets the user based on the stored id in the session.
            Returns either the user name, or an error that there was no user found (from the register functions).

            Return Value:
                On Success  -> String (user name).
                On Fail     -> Assoc Array (error).
     */
    public function getUserName() {
        /* If no user is set, attempt to set one, and return an error if failed. */
        if( !isset( $this->user ) ) {
            try {
                $this->user = App::get( "database" )->selectAllWhere( [ "Gebr_Index" => $_SESSION["user"]["id"] ] );
            } catch(Exception $e) {
                return [ "error" => [ "fetchResponse" => App::get( "errors" )->getError( "noUserErr" ) ] ];
            }
        }

        /* Return the user object it's name. */
        return $this->user["Gebr_Naam"];
    }

    /*  validateUser($id, $pw):
            Validate if the user id is in the database, and validate if the stored PW matches the user input.
                $id (string)    - The user its credentials, either a valid e-mail or user name.
                $pw (string)    - The password input from the user.

            Return Value:
                On Validate - Boolean
                Failed      - Assoc Array
     */
    public function validateUser( $id, $pw ) {
        /* If the $id input was a e-mail, i check if a user is stored with said e-mail, and return a error if not. */
        if( filter_var( $id, FILTER_VALIDATE_EMAIL ) ) {
            if( !isset( App::get( "database" )->selectAllWhere( "gebruikers", [ "Gebr_Email" => $id ] )[0] ) ) {
                return [ "error" => [ "fetchResponse" => App::get( "errors" )->getError( "credError" ) ] ];
            } else {
                $this->user = App::get( "database" )->selectAllWhere( "gebruikers", [ "Gebr_Email" => $id ] )[0];
            }
        /* If the $id input was not a e-mail, i also check if a user was stored with said e-mail, and return a error if not. */
        } else {
            if( !isset( App::get( "database" )->selectAllWhere( "gebruikers", [ "Gebr_Naam" => $id ] )[0] ) ) {
                return [ "error" => [ "fetchResponse" => App::get( "errors" )->getError( "credError" ) ] ];
            } else {
                $this->user = App::get( "database" )->selectAllWhere( "gebruikers", [ "Gebr_Naam" => $id ] )[0];
            }
        }

        /* If the user is still valid at this point, i verify there password, and return either a error or a bool */
        if( password_verify( $pw, $this->user["Gebr_WachtW"] ) ) {
            return TRUE;
        } else {
            unset($this->user);
            return [ "error" => [ "fetchResponse" => App::get( "errors" )->getError( "credError" ) ] ];
        }
    }

    /*  checkUser($id=null, $rights=null):
            This function checks if the user is valid, and if the user rights are set to Admin or not.
                $id     (string) - The index of the user we want to check, most likely take from the session.
                $rights (string) - If we want to check the user rights or not, if not it defaults to null so it doesnt need to be set.
            
            Return value:
                On Validate - Boolean
                Failed      - Assoc Array
     */
    public function checkUser( $id = null, $rights = null ) {
        /* If the user is not set, and the id was passed, we set the user based on the id. */
        if( !isset( $this->user ) && isset( $id ) ) {
            $this->user = App::get( "database" )->selectAllWhere( "gebruikers", [ "Gebr_Index" => $id ] )[0];
        }

        /* If the id was passed, and it matches with the current user, */
        if( isset( $id ) && $id === $this->user["Gebr_Index"] ) {
            /* If the rights where set and equal to "Admin", i return TRUE */
            if( isset( $rights ) && $this->user["Gebr_Rechten"] === "Admin" ) {
                return TRUE;
            /* If rights was not set, the user is still valid for a user login. */
            } elseif( !isset( $rights ) && $this->user["Gebr_Rechten"] === "gebruiker" ) {
                return TRUE;
            /* If failed i return the authFailed error */
            } else {
                return [ "error" => [ "fetchResponse" => App::get( "errors" )->getError( "authFailed" ) ] ];
            }
        /* If failed i return the authFailed error */
        } else {
            return [ "error" => [ "fetchResponse" => App::get( "errors" )->getError( "authFailed" ) ] ];
        }
    }

    /*  getUserId():
            To bind users and sessions, i only need there Database index key.

            Return Value:
                With Errors     - Assoc Array
                Without Errors  - INT
     */
    public function getUserId() {
        if( isset( $this->user ) ) {
            return $this->user["Gebr_Index"];
        } else {
            return [ "error" => [ "fetchResponse" => App::get( "errors" )->getErrors( "noUserErr" ) ] ];
        }
    }

    /*  evalUser():
            To evaluate the users rights, i return true if account is a user and false if a admin, and a error otherwhise.
            This is only used during the login process, the rest can be done with 'checkUser($id, $rights)'.

            Return Value:
                On user     - Bool
                On admin    - Bool
                On Failed   - Assoc Array
     */
    public function evalUser() {
        if( $this->user["Gebr_Rechten"] === "gebruiker" ) {
            return TRUE;
        } else if( $this->user["Gebr_Rechten"] === "Admin" ) {
            return FALSE;
        } else {
            return [ "error" => [ "loginFailed" => App::get( "errors" )->getErrors( "rightsError" ) ] ];
        }
    }

    /*  updateUser($table, $data, $id):
            This function deals with updating the user, and return a boolean.
                $table  (string)        - The DB table that needs updating (always 'gebruikers').
                $data   (assoc array)   - The data that needs to be updated.
                $id     (string)        - The id of the user that needs to be updated.
                $store  (string/int)    - The result of trying to update the user in the database.
            
            Return Value:
                On Update - Boolean.
                On Failed - Assoc Array.
     */
    public function updateUser( $table, $data, $id ) {
        $store = App::get( "database" )->update( $table, $data, $id );

        return is_string( $store ) ? [ "error" => [ "fetchResponse" => App::get( "errors" )->getErrors( "dbFail" ) ] ] : TRUE;
    }
}
