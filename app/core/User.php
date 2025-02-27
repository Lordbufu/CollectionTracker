<?php

namespace App\Core;

class User {
    protected $user;

    /*  setUser($ids):*/
    protected function setUser($ids) {
        $this->user = App::resolve('database')->prepQuery('select', 'gebruikers', $ids)->getSingle();
    }

    /*  getUser($ids):*/
    public function getUser($ids) {
        if(!isset($this->user)) {
            $this->setUser($ids);
        }

        return $this->user;
    }

    /*  createUser($data):*/
    public function createUser($data) {
        /* Prepare all the user data for the database. */
        if(isset($data['naam']) && isset($data['email']) && isset($data['wachtwoord'])) {
            $sqlData = [
                'Gebr_Naam' => $data['naam'],
                'Gebr_Email' => $data['email'],
                'Gebr_WachtW' => password_hash($data['wachtwoord'], PASSWORD_BCRYPT),
                'Gebr_Rechten' => 'User'
            ];

            $store = App::resolve('database')->prepQuery('insert', 'gebruikers', null, $sqlData)->getErrorCode();
        // just sequence of numbers, not a actual error code ¿ (triggers the default store error).
        } else {
            $store = '123456';
        }

        /* Check if the error code wasnt the default nothing burger, */
        if($store !== '00000') {
            /* '23000' means there is a duplicate entry detected. */
            if($store === '23000') {
                return App::resolve('errors')->getError('user', 'user-dupl');
            /* The rest gets a default store error for now. */
            } else {
                return App::resolve('errors')->getError('database', 'store-error');
            }
        }

        if(!isset($this->user)) {
            $this->user = $this->setUser(['Gebr_Email' => $data['email']]);
        }

        return TRUE;
    }

    /*  getName($id):*/
    public function getName($ids) {
        /* Check if a user is set, if not attempt to set it, discarding any return values. */
        if(!isset($this->user)) {
            $this->setUser($ids);
        }

        /* If the user was set to a error string, or if the id's dont match, i return FALSE. */
        if(is_string($this->user)) {
            return App::resolve('errors')->getError('user', 'user-fetch');
        }

        /* If all seem well, i return the user name. */
        return $this->user['Gebr_Naam'];
    }
}