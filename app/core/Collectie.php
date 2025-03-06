<?php

namespace App\Core;

class Collectie {
    /* Global data storage */
    protected $collectie;
    protected $uId = [];

    /*  __construct():
            Each time this class is resolved\called, i want to set the user id globally, and load the users collection data.
     */
    public function __construct() {
        if(isset($_SESSION['user']['id']) && empty($this->uId['Gebr_Index'])) {
            $this->uId['Gebr_Index'] = $_SESSION['user']['id'];
            $this->setColl();
        }
    }

    /*  setColl():
            This function sets the collection data, using the globally stored userid.
            And returns a feedback error for the user, if no array data was set.
                $error (String) - The error for failing to set the collection data.

            Return Value:
                On failure -> String.
                On success -> Boolean.
     */
    protected function setColl() {
        $this->collectie = App::resolve('database')->prepQuery(
            'select',
            'collectie',
            $this->uId
        )->getAll();

        $error = App::resolve('errors')->getError('collectie', 'set-error');

        return (is_array($this->collectie) || is_string($this->collectie)) ? TRUE : $error;
    }

    /*  getColl():
            This function simply returns the collection data, based on the current active user.
            If setting the collection had a error, i simply hand down the error to the caller, so the check should be done in the controller.

            Return Value:
                On failure -> String.
                On success -> Array.
     */
    public function getColl() {
        if(!is_array($this->collectie)) {
            $this->setColl();
        }

        return $this->collectie;
    }

    /*  addColl($data):
            This function attempt to add a item to the users collection, while checking if the item isnt duplicate.
                $id (Assoc Arr)         - Id pair for evaluating the item its collection status.
                $dbStore (String/null)  - The result of trying to add the item to the user its collection.
            
            Return Value:
                On failure  - String.
                On success  - Boolean.
     */
    public function addColl($data) {
        /* Attempt to set users collection data, return error on failure. */
        if(!is_array($this->collectie)) {
            $this->setColl();
        }

        if(is_string($this->collectie)) {
            return $this->collectie;
        }

        /* Check if index is duplicate, return error if this is the case. */
        $id = [
            'Item_Index' => (int)$data['iIndex']
        ];

        if($this->evalColl($id)) {
            return App::resolve('errors')->getError('collectie', 'dup-error');
        }

        /* Attempt to set item to user its collection data. */
        $dbStore = App::resolve('database')->prepQuery('insert',
            'collectie', [
                'Gebr_Index' => $this->uId['Gebr_Index'],
                'Item_Index' => $data['iIndex'],
                'Reeks_Index' => $data['rIndex']
            ]
        )->getAll();

        /* Return the result of the above add attempt. */
        return (is_string($dbStore)) ? $dbStore : TRUE;
    }

    /*  remColl($data):
            This function attempts to remove specific single items from the user its collection.
                $dbStore (String/null)      - The result of trying to add the item to the user its collection.

            Return Value:
                On failure  - String.
                On success  - Boolean.
     */
    public function remColl($data) {
        /* Attempt to remove item from user its collection data. */
        $dbStore = App::resolve('database')->prepQuery(
            'delete',
            'collectie', [
                'Gebr_Index' => $this->uId['Gebr_Index'],
                'Item_Index' => $data['index']
            ]
        )->getAll();

        /* Return the result of the above remove attempt. */
        return (is_string($dbStore)) ? $dbStore : TRUE;
    }

    /*  evalColl($data):
            This function returns a boolean, based on where a item is present inside a collection or not (basically a duplication check).
                $present (Boolean)  - The present tag, starting at FALSE, and is switches if a duplicate index is found.
            
            Return Value: Boolean.
     */
    public function evalColl($data) {
        /* Attempt to set users collection data, return error on failure. */
        if(!is_array($this->collectie)) {
            $this->setColl();
        }

        if(is_string($this->collectie)) {
            return $this->collectie;
        }

        /* We start assuming the item isnt in the collection, and then check each record to see if we have a match. */
        $present = FALSE;

        foreach($this->collectie as $item => $value) {
            if($value['Item_Index'] === $data['Item_Index']) {
                $present = TRUE;
            }
        }

        /* Then i simply return the tag for further evaluation. */
        return $present;
    }

    /*  remCollAdmin($ids):
            This function is designed to remove a whole set of collection data, as a result of administrator actions.
                $dbStore (String/null)  - The result of trying to remove the items from the users collection.

            Return Value:
                On failure  - String.
                On success  - Boolean.
     */
    public function remCollAdmin($ids) {
        $dbStore = App::resolve('database')->prepQuery(
            'delete',
            'collectie',
            $ids
        )->getAll();

        return (is_string($dbStore)) ? App::resolve('errors')->getError('collectie', 'rem-fail') : TRUE;
    }
}