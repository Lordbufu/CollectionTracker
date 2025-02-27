<?php

namespace App\Core;

class Collectie {
    /* Global data storage */
    protected $collectie;
    protected $uId = [];

    /* This constructor will set the user-index, if it was stored in the session. */
    public function __construct() {
        if(isset($_SESSION['user']['id']) && empty($this->uId['Gebr_Index'])) {
            $this->uId['Gebr_Index'] = $_SESSION['user']['id'] ?? null;
        }
    }

    /*  setColl($ids):
            This function simply sets the user it collection data, with the supplied identifiers.
            If not data was set, i return a costum error for user feedback.

            Return Value:
                On failure -> String.
                On success -> Boolean.
     */
    protected function setColl() {
        $this->collectie = App::resolve('database')->prepQuery('select', 'collectie', $this->uId)->getAll();
        $error = App::resolve('errors')->getError('collectie', 'set-error');

        return (is_array($this->collectie)) ? TRUE : $error;
    }

    /*  getColl():
            This function simply returns the collection data, based on the current active user.
            If setting the collection had a error, i simply hand down the error to the caller.
                $store (String/Boolean) - The result of attempt to set the global $collectie variable.

            Return Value:
                On failure -> String.
                On success -> Array.
     */
    public function getColl() {
        if(isset($this->uId)) {
            $store = $this->setColl($this->uId);
        }

        return (is_string($store)) ? $store : $this->collectie;
    }

    /*  addColl($data):
            This function attempt to add a item to the users collection, while checking if the item isnt duplicate.
                $lStore (String/Boolean)    - The result of trying to set the user its collection data in the class global.
                $dbStore (String/null)      - The result of trying to add the item to the user its collection.
            
            Return Value:
                On failure  - String.
                On success  - Boolean.
     */
    public function addColl($data) {
        /* Attempt to set users collection data, return error on failure. */
        if(isset($this->uId)) {
            $lStore = $this->setColl($this->uId);
            if(is_string($lStore)) {
                return $lStore;
            }
        }

        /* Check if index is duplicate, return error if this is the case. */
        if(!empty($this->collectie)) {
            if($this->evalColl(['Item_Index' => (int)$data['iIndex']])) {
                return App::resolve('errors')->getError('collectie', 'dup-error');
            }
        }

        /* Attempt to set item to user its collection data. */
        $dbStore = App::resolve('database')->prepQuery('insert', 'collectie', [
                'Gebr_Index' => $this->uId['Gebr_Index'],
                'Item_Index' => $data['iIndex'],
                'Reeks_Index' => $data['rIndex']
        ])->getAll();

        /* Return the result of the above add attempt. */
        return (is_string($dbStore)) ? $dbStore : TRUE;
    }

    /*  remColl($data):
            This function attempts to remove items from the user its collection.
                $lStore (String/Boolean)    - The result of trying to set the user its collection data in the class global.
                $dbStore (String/null)      - The result of trying to add the item to the user its collection.

            Return Value:
                On failure  - String.
                On success  - Boolean.
     */
    public function remColl($data) {
        /* Attempt to set users collection data, return error on failure. */
        if(isset($this->uId)) {
            $lStore = $this->setColl($this->uId);

            if(is_string($lStore)) {
                return $lStore;
            }
        }

        /* Attempt to remove item from user its collection data. */
        $dbStore = App::resolve('database')->prepQuery('delete', 'collectie', [
            'Gebr_Index' => $this->uId['Gebr_Index'],
            'Item_Index' => $data['index']
        ])->getAll();

        /* Return the result of the above remove attempt. */
        return (is_string($dbStore)) ? $dbStore : TRUE;
    }

    /*  evalColl($data):
            This function returns a boolean, based on where a item is present inside a collection or not (basically a duplication check).
                $aanwezig (Boolean)         - The present tag, starting at FALSE, and is switches if a duplicate index is found.
                $lStore (String/Boolean)    - The result of trying to set the user its collection data in the class global.
            
            Return Value: Boolean.
     */
    public function evalColl($data) {
        /* I start with the present tag set to FALSE. */
        $aanwezig = FALSE;

        /* Attempt to set users collection data, return error on failure. */
        if(isset($this->uId)) {
            $lStore = $this->setColl($this->uId);

            if(is_string($lStore)) {
                return $lStore;
            }
        }

        /* Attempt to match the item in with the current collection data, set present tag to TRUE if matching. */
        foreach($this->collectie as $item => $value) {
            if($value['Item_Index'] === $data['Item_Index']) {
                $aanwezig = TRUE;
            }
        }

        /* Finally simply return the present tag to the caller. */
        return $aanwezig;
    }

    /*  remCollAdmin($ids): */
    public function remCollAdmin($ids) {
        $dbStore = App::resolve('database')->prepQuery('delete', 'collectie', $ids)->getAll();
        return (is_string($dbStore)) ? App::resolve('errors')->getError('collectie', 'rem-fail') : TRUE;
    }
}