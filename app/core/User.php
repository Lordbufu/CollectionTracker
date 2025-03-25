<?php

namespace App\Core;

class User {
    protected $user;

    /*  setUser($ids):
            This function set the global user, based on a specific Id pair only.
                $ids (Assoc Arr)    - The id pair associated with the requested user.
            
            Return Value: None.
     */
    protected function setUser($ids) {
        $this->user = App::resolve('database')->prepQuery('select', 'gebruikers', $ids)->getSingle();
    }

    /*  getUser($ids):
            This function attempt to get the requested user, db error are passed on to the caller.
                $ids (Assoc Arr)    - The id pair associated with the requested user.
            
            Return Value: Associative Array
     */
    public function getUser($ids) {
        if(!isset($this->user)) {
            $this->setUser($ids);
        }

        return $this->user;
    }

    /*  createUser($data):
            This function attempts to create a user record, incl hashing the password, and set the global ser after that is done.
                $data (Assoc Arr)       - The POST data as presented to us by the controller.
                $sqlData (Assoc Arr)    - The user data prepared for the PDO request.
                $store (String/null)    - A temp store to evaluate the database opperation.
                                        - Set to a default number string, if input went missing, to trigger a default store error.
            
            Return Value:
                On failure - String.
                On success - Boolean.
     */
    public function createUser($data) {
        if(isset($data['gebr-naam']) && isset($data['email']) && isset($data['wachtwoord'])) {
            $sqlData = [
                'Gebr_Naam' => $data['gebr-naam'],
                'Gebr_Email' => $data['email'],
                'Gebr_WachtW' => password_hash($data['wachtwoord'], PASSWORD_BCRYPT),
                'Gebr_Rechten' => 'User'
            ];

            $store = App::resolve('database')->prepQuery('insert', 'gebruikers', null, $sqlData)->getErrorCode();
        } else {
            $store = '123456';
        }

        /* Evaluate what type of error we are dealing with. */
        if($store !== '00000') {
            if($store === '23000') {
                return App::resolve('errors')->getError('user', 'user-dupl');
            } else {
                return App::resolve('errors')->getError('database', 'store-error');
            }
        }

        if(!isset($this->user)) {
            $this->user = $this->setUser([
                'Gebr_Email' => $data['email']
            ]);
        }

        return TRUE;
    }

    /* updateUser($data):
            Fairly straight forward update querry, to change a specific user there password.
                $data (Assoc Arr)       - The POST data from the password change request, as recieved by the controller itself.
                $sqlData (Assoc Array)  - The user data prepared for the PDO request.
                $userId (Assoc Array)   - The email adress from the user that needs its password changed.
                $store (String/null)    - A temp store to evaluate the database opperation.
            
            Return Value:
                On failure - String.
                On success - Boolean.
     */
    public function updateUser($data) {
        if(isset($data['email'])) {
            $sqlData = ['Gebr_WachtW' => password_hash($data['wachtwoord1'], PASSWORD_BCRYPT)];
            $userId = ['Gebr_Email' => $data['email']];
        }

        $store = App::resolve('database')->prepQuery('update', 'gebruikers', $userId, $sqlData)->getErrorCode();

        return ($store === '00000') ? TRUE : App::resolve('errors')->getError('database', 'store-error');
    }

    /*  getName($id):
            This function simple attempts to retrieve the user name, based on a provided id pair.
                $ids (Assoc Arr)    - The id pair that is associated with the record we want the name of.

            Return Value: String.
     */
    public function getName($ids) {
        if(!isset($this->user)) {
            $this->setUser($ids);
        }

        return is_string($this->user) ? App::resolve('errors')->getError('user', 'user-fetch') : $this->user['Gebr_Naam'];
    }
}