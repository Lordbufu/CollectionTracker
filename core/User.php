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

    /*  setUser($data):
            Attempt to add user to database, and check for duplicate e-mails & names.

            $data (Assoc Array) - The user input, sanitized in the controller.

            Return Value:
                On sucess: Boolean
                On failed: Assoc Array -> example: ['type-of-message' => ['browser-storage-tag' => 'error-string']]
     */
    public function setUser($data) {
        $userNameErr = "Deze gebruiker bestaat al.";
        $userEmailErr = "E-mail adres reeds ingebruik.";
        $noUserErr = "No users found, plz contact the Administrator!";

        $tempUsers = App::get('database')->selectAll('gebruikers');

        if(!empty($tempUsers)) {
            foreach($tempUsers as $key => $user) {
                if($user['Gebr_Naam'] === $data['Gebr_Naam']) {
                    $errorMsg['error']['userError1'] = $userNameErr;
                }

                if($user['Gebr_Email'] === $data['Gebr_Email']) {
                    $errorMsg['error']['userError2'] = $userEmailErr;
                }
            }
        } else {
            $errorMsg = ['error' => ['userError1' => $noUserErr]];
        }

        if(!empty($errorMsg)) {
            return $errorMsg;
        } else {
            App::get('database')->insert('gebruikers', $data);

            return TRUE;
        }
    }

    // W.I.P.
    /*  validateUser($id, $pw):
            Validate if the user id is in the database, and validate if the stored PW matches the user input.

            $id (string)    - The user its credentials, either a valid e-mail or user name.
            $pw (string)    - The password input from the user.

            Return Value:
                On Validate - (int)
                Failed - (Assoc Array)
     */
    public function validateUser($id, $pw) {
        // Set error message for failing validation.
        $credError = [
            'loginFailed' => 'Uw inlog gegevens zijn niet correct, probeer het nogmaals!!'
        ];

        // If the id is a valid e-mail adress,
        if(filter_var($id, FILTER_VALIDATE_EMAIL)) {
            // Check if the database has a valid entry for the users e-mail,
            if(!isset(App::get('database')->selectAllWhere('gebruikers', ['Gebr_Email' => $id])[0])) {
                // return the error if there wasn't.
                return $credError;
            // If the DB has a valid entry for the user,
            } else {
                // set the matched user to the user object.
                $this->user = App::get('database')->selectAllWhere('gebruikers', ['Gebr_Email' => $id])[0];
            }
        // If the id was not a valid e-mail,
        } else {
            // Check if the database has a valid entry for the users name,
            if(!isset(App::get('database')->selectAllWhere('gebruikers', ['Gebr_Naam' => $id])[0])) {
                // return the error if there wasn't.
                return $credError;
            // If the DB has a valid entry for the user,
            } else {
                // set the matched user to the user object.
                $this->user = App::get('database')->selectAllWhere('gebruikers', [
                    'Gebr_Naam' => $id
                ])[0];
            }
        }

        // If the stored password matches the input,
        if(password_verify($pw, $this->user['Gebr_WachtW'])) {
            // return 1 to the caller.
            return 1;
        // If they dint match,
        } else {
            // return the error.
            return $credError;
        }
    }

    // W.I.P.
    public function checkUser($id, $rights = null) {
        // If there was no user object set, then check if the DB returns a user, and set the user if it does.
        if(empty($this->user)) {
            if(isset(App::get('database')->selectAllWhere('gebruikers', ['Gebr_Index' => $id])[0])) {
                $this->user = App::get('database')->selectAllWhere('gebruikers', ['Gebr_Index' => $id])[0];
            // if database return nothing we return FALSE and end the check.
            } else {
                return FALSE;
            }
        }

        // Now we are sure the user object is set, we check the user id
        if($id === $this->user['Gebr_Index']) {
            // if we also wanted to check the user rights (only for the admins),
            if(isset($rights)) {
                // if the rights are a match we return TRUE,
                if($this->user['Gebr_Rechten'] === 'Admin') {
                    return TRUE;
                // and FALSE if not regardless of the id check outcome.
                } else {
                    return FALSE;
                }
            }

            return TRUE;
        // If the id check failed, we don't need to check the rights either, and can just return FALSE.
        } else {
            return FALSE;
        }
    }

    //  TODO: Need to figure out if and when this can fails, so i know what to return when that happens.
    /*  getUserId(): W.I.P.
            To bind users and sessions, i only need there Database index.

            Return Value:
                With Errors     - ....
                Without Errors  - INT

     */
    public function getUserId() {
        if(isset($this->user)) {                                                    // Check if a user was set,
            return $this->user['Gebr_Index'];                                       // return said users index.
        } else {                                                                    // If no user was set,
            return 'No user defined';                                               // we return a string for now.
        }
    }

    // W.I.P.
    public function getUserName() {
        return $this->user['Gebr_Naam'];
    }

    // W.I.P.
    public function evalUser() {
        $rightsError = [ 'loginFailed' => 'U heeft geen rechten om de website te bezoeken !!' ];

        if($this->user['Gebr_Rechten'] === 'gebruiker') {
            return 1;
        } elseif($this->user['Gebr_Rechten'] === 'Admin') {
            return 0;
        } else {
            return $rightsError;
        }
    }
}
