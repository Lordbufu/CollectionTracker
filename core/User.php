<?php

namespace App\Core;

class User {
    protected $user;

    function __construct ($id = []) {
        if(!empty($id)) {
            $filtered = htmlspecialchars($id);

            if(filter_var($filtered, FILTER_VALIDATE_EMAIL)) {
                $this->user = App::get('database')->selectAllWhere('gebruikers', [
                    'Gebr_Email' => $filtered
                ]);
            } else {
                $this->user = App::get('database')->selectAllWhere('gebruikers', [
                    'Gebr_Naam' => $filtered
                ]);
            }
        }

        return;
    }

    public function setUser($data) {
        $errorMsg = [];
        $userNameErr = "Deze gebruiker bestaat al.";
        $userEmailErr = "E-mail adres reeds ingebruik.";

        // Get all user in database
        $tempUsers = App::get('database')->selectAll('gebruikers');

        // Check for duplicate names and e-mails.
        if(!empty($tempUsers)) {
            foreach($tempUsers as $key => $user) {
                if($user['Gebr_Naam'] === $data['Gebr_Naam']) {
                    $errorMsg['error']['userError1'] = $userNameErr;
                }

                if($user['Gebr_Email'] === $data['Gebr_Email']) {
                    $errorMsg['error']['userError2'] = $userEmailErr;
                }
            }
        }

        // Evaluate if there were any errors, and return those.
        if(!empty($errorMsg)) {
            return $errorMsg;
        // Filter the user name, before putting in the database.
        } else {
            App::get('database')->insert('gebruikers', $data);
            return 1;
        }
    }

    // Function to return only the user but not the entire object.
    public function getUserId() {
        if(isset($this->user)) {
            return $this->user['Gebr_Index'];
        } else {
            return 'No user defined';
        }
    }

    public function getUserName() {
        return $this->user['Gebr_Naam'];
    }

    public function validateUser($pw) {
        $credError = ['loginFailed' => 'Uw inlog gegevens zijn niet correct, probeer het nogmaals!!'];

        if(password_verify($pw, $this->user['Gebr_WachtW'])) {
            return 1;
        } else {
            return $credError;
        }
    }

    public function evalUser() {
        $rightsError = ['loginFailed' => 'U heeft geen rechten om de website te bezoeken !!'];
        if($this->user['Gebr_Rechten'] === 'gebruiker') {
            return 1;
        } elseif($this->user['Gebr_Rechten'] === 'Admin') {
            return 0;
        } else {
            return $rightsError;
        }
    }

    function storeUser($data) {

    }

    function checkUserId($id) {

    }
}
