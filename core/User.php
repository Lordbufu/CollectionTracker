<?php

namespace App\Core;

use Exception;

/*  User Class:
        To clean up the LogicController and processing class, i off-loaded the user related things to this class.
        Everything is based on return values, so i only need to evaluate most of the time, and sometimes pass on a error.
        Binding the user to the session, is for now only done in the Controller.
 */
class User {
    /* Protected user variable, to store the requested user. */
    protected $user;

    /* All potential errors that can occure during user related actions. */
    protected $userNameErr = [ "userError1" => "Deze gebruiker bestaat al." ];
    protected $userEmailErr = [ "userError2" => "E-mail adres reeds ingebruik." ];
    protected $noUserErr = [ "userError1" => "Geen gebruiker gevonden, neem contact op met uw Administrator!" ];
    protected $userAdd = [ "userCreated" => "Gebruiker aangemaakt, u kunt nu inloggen!" ];
    protected $credError = [ "loginFailed" => "Uw inlog gegevens zijn niet correct, probeer het nogmaals!!" ];
    protected $rightsError = [ "loginFailed" => "U heeft geen rechten om de website te bezoeken !!" ];
    protected $authFailed = [ "fetchResponse" => "Access denied, Account authentication failed !" ];
    protected $dbError = [ "fetchResponse" => "Er was een database error, neem contact op met de administrator als dit blijft gebeuren!" ];

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
        /* Undefined error variable, it was generating errors before. */
        $errorMsg;

        /* Attempt to request all current users. */
        $tempUsers = App::get("database")->selectAll("gebruikers");

        /* Check if there where any users set, or if there wa a DB error. */
        if( !is_string( $tempUsers ) ) {
            /* Loop over all users. */
            foreach( $tempUsers as $key => $user ) {
                /* If a duplicate name detected, store the correct error. */
                if( $user["Gebr_Naam"] === $data["Gebr_Naam"] ) { $errorMsg["error"] = $this->userNameErr; }

                /* If a duplicate e-mail is detected, check if a error was set for the name and merge both errors. */
                if( $user["Gebr_Email"] === $data["Gebr_Email"] ) {
                    if( is_array( $errorMsg ) ) {
                        $errorMsg["error"] = array_merge( $this->userNameErr, $this->userEmailErr );
                    /* Otherwhise just add the e-mail error. */
                    } else { $errorMsg["error"] = $this->userEmailErr; }
                }
            }
        /* If there was a DB error getting all users, store the correct error. */
        } else { $errorMsg["error"] = $this->noUserErr; }

        /* Evaluate if there are any errors stored, and attempt to insert the user if not. */
        if( !isset( $errorMsg ) ) {
            $store = App::get("database")->insert( "gebruikers", $data );
        /* If there where, simply return the errors to the caller. */
        } else { return $errorMsg; }

        /* If there was no error string from the DB, return a feedback message, else return a DB error message. */
        return !is_string($store) ? $this->userAdd : [ "error" => $this->dbError ];
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
                $this->user = App::get("database")->selectAllWhere( [ "Gebr_Index" => $_SESSION["user"]["id"] ] );
            } catch(Exception $e) {
                return $this->noUserErr;
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
    public function validateUser($id, $pw) {
        /* If the $id input was a e-mail, i check if a user is stored with said e-mail, and return a error if not. */
        if( filter_var( $id, FILTER_VALIDATE_EMAIL ) ) {
            if( !isset( App::get("database")->selectAllWhere( "gebruikers", [ "Gebr_Email" => $id ] )[0] ) ) {
                return $this->credError;
            } else {
                $this->user = App::get("database")->selectAllWhere( "gebruikers", [ "Gebr_Email" => $id ] )[0];
            }
        /* If the $id input was not a e-mail, i also check if a user was stored with said e-mail, and return a error if not. */
        } else {
            if( !isset (App::get("database")->selectAllWhere( "gebruikers", [ "Gebr_Naam" => $id ] )[0] ) ) {
                return $this->credError;
            } else {
                $this->user = App::get("database")->selectAllWhere( "gebruikers", [ "Gebr_Naam" => $id ] )[0];
            }
        }

        /* If the user is still valid at this point, i verify there password, and return either a error or a bool */
        if( password_verify( $pw, $this->user["Gebr_WachtW"] ) ) {
            return TRUE;
        } else {
            unset($this->user);
            return $this->credError;
        }
    }

    /*  checkUser($id=null, $rights=null):
            This function checks if the uiser is valid, and if the user rights are set to Admin or not.
                $id     (string) - The index of the user we want to check, most likely take from the session.
                $rights (string) - If we want to check the user rights or not, if not it defaults to null so it doesnt need to be set.
            
            Return value:
                On Validate - Boolean
                Failed      - Assoc Array
     */
    public function checkUser($id=null, $rights=null ) {
        /* If the user is not set, and the id was passed, we set the user based on the id. */
        if( !isset( $this->user ) && isset( $id ) ) {
            $this->user = App::get("database")->selectAllWhere( "gebruikers", [ "Gebr_Index" => $id ] )[0];
        }

        /* If the id was passed, and it matches with the current user, */
        if( isset( $id ) && $id === $this->user["Gebr_Index"] ) {
            /* If the rights where set and equal to "Admin", i return TRUE */
            if( isset( $rights ) && $this->user["Gebr_Rechten"] === "Admin" ) {
                return TRUE;
            /* If failed i return the authFailed error */
            } elseif( !isset( $rights ) ) {
                return TRUE;
            } else {
                return $this->authFailed;
            }
        /* If failed i return the authFailed error */            
        } else {
            return $this->authFailed;
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
            return $this->noUserErr;
        }
    }

    /*  evalUser():
            To evaluate the users rights, i return true is a user and false if admin, and a error otherwhise.

            Return Value:
                On user     - Bool
                On admin    - Bool
                On Failed   - Assoc Array
     */
    public function evalUser() {
        if($this->user["Gebr_Rechten"] === "gebruiker") {
            return TRUE;
        } else if($this->user["Gebr_Rechten"] === "Admin") {
            return FALSE;
        } else {
            return $this->rightsError;
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
    public function updateUser($table, $data, $id) {
        $store = App::get("database")->update( $table, $data, $id );

        return is_string($store) ? $this->dbError : TRUE;
    }
}
