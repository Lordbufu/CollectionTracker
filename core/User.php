<?php

namespace App\Core;

/*  User Class:
        To clean up the LogicController and processing class, i off-loaded the user related things to this class.
        Everything is based on return values, so i only need to evaluate most of the time, and sometimes pass on a error.
        Binding the user to the session, is for now only done in the Controller.

            $user   - Protected user variable, to store the requested user.
 */
class User {
    protected $user;
    protected $userNameErr = [ "userError1" => "Deze gebruiker bestaat al." ];
    protected $userEmailErr = [ "userError2" => "E-mail adres reeds ingebruik." ];
    protected $noUserErr = [ "userError1" => "No users found, plz contact the Administrator!" ];
    protected $credError = [ "loginFailed" => "Uw inlog gegevens zijn niet correct, probeer het nogmaals!!" ];
    protected $rightsError = [ "loginFailed" => "U heeft geen rechten om de website te bezoeken !!" ];

    /*  setUser($data):
            Attempt to add user to database, and check for duplicate e-mails & names.
                $data (Assoc Array) - The user input, sanitized in the controller.

            Return Value:
                On sucess: Boolean
                On failed: Assoc Array -> example: [ 'type-of-message' => [' browser-storage-tag' => 'error-string' ] ]
     */
    public function setUser($data) {
        $tempUsers = App::get("database")->selectAll("gebruikers");

        if( !empty( $tempUsers ) ) {
            foreach( $tempUsers as $key => $user ) {
                if( $user["Gebr_Naam"] === $data["Gebr_Naam"] ) { $errorMsg["error"] = $this->userNameErr; }

                if($user["Gebr_Email"] === $data["Gebr_Email"]) { $errorMsg["error"] = $this->userEmailErr; }
            }
        } else { $errorMsg["error"] = $this->noUserErr; }

        if( !isset( $errorMsg ) ) {
            $store = App::get("database")->insert( "gebruikers", $data );
        } else { return $this->errorMsg; }

        return is_string($errorMsg) ? $store : TRUE;
    }

    /* getUserName(): Simple get user name function, that sets the user based on the stored id in the session. */
    public function getUserName() {
        if( !isset( $this->user ) ) { $this->user = App::get("database")->selectAllWhere( [ "Gebr_Index" => $_SESSION["user"]["id"] ] ); }

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
        if( filter_var( $id, FILTER_VALIDATE_EMAIL ) ) {
            if( !isset( App::get("database")->selectAllWhere( "gebruikers", [ "Gebr_Email" => $id ] )[0] ) ) {
                return $this->credError;
            } else { $this->user = App::get("database")->selectAllWhere( "gebruikers", [ "Gebr_Email" => $id ] )[0]; }
        } else {
            if( !isset (App::get("database")->selectAllWhere( "gebruikers", [ "Gebr_Naam" => $id ] )[0] ) ) {
                return $this->credError;
            } else { $this->user = App::get("database")->selectAllWhere( "gebruikers", [ "Gebr_Naam" => $id ] )[0]; }
        }

        if( password_verify( $pw, $this->user["Gebr_WachtW"] ) ) {
            return TRUE;
        } else {
            unset($this->user);
            return $this->credError;
        }
    }

    /*  checkUser($id, $rights=null):
            This function checks if the uiser is valid, and if the user rights are set to Admin or not.
                $id     - The index of the user we want to check, most likely take from the session.
                $rights - If we want to check the user rights or not, if not it defaults to null so it doesnt need to be set.
            
            Return value = Boolean.
     */
    public function checkUser($id, $rights=null ) {
        if( empty( $this->user ) ) {
            if( isset(App::get("database")->selectAllWhere( "gebruikers", [ "Gebr_Index" => $id ] )[0] ) ) {
                $this->user = App::get("database")->selectAllWhere( "gebruikers", [ "Gebr_Index" => $id ] )[0];
            } else { return FALSE; }
        }

        if( $id === $this->user["Gebr_Index"] ) {
            if( isset( $rights ) ) {
                if( $this->user["Gebr_Rechten"] === "Admin" ) {
                    return TRUE;
                } else { return FALSE; }
            } else { return TRUE; }
        } else { return FALSE; }
    }

    //  TODO: figure out a better way to deal with the error loop.
    /*  getUserId():
            To bind users and sessions, i only need there Database index key.

            Return Value:
                With Errors     - String
                Without Errors  - INT

     */
    public function getUserId() {
        if( isset( $this->user ) ) {
            return $this->user["Gebr_Index"];
        } else { return "No user defined"; }
    }

    //  TODO: add some kind of meaningfull comment to this funcion.
    /*  evalUser():
     */
    public function evalUser() {
        if($this->user["Gebr_Rechten"] === "gebruiker") {
            return TRUE;
        } elseif($this->user["Gebr_Rechten"] === "Admin") {
            return FALSE;
        } else { return $this->rightsError; }
    }

    /*  updateUser($table, $data, $id):
            This function deals with updating the user, and return a boolean.
                $table  - The DB table that needs updating (always 'gebruikers').
                $data   - The data that needs to be updated.
                $id     - The id of the user that needs to be updated.
            
            Return Value: Boolean.
     */
    public function updateUser($table, $data, $id) {
        $store = App::get("database")->update( $table, $data, $id );

        return is_string($store) ? FALSE : TRUE;
    }
}
