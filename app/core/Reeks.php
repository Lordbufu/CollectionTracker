<?php

namespace App\Core;

class Reeks {
    protected $reeks;
    protected $duplicate;

    /*  setReeks($ids):
            This function attempt to set the global reeks variable, and also count the items inside them.
                $ids (Assoc Arr)    - (Optional) Id pair associate with the reeks data that need to be set.
            
            Return Value: None.
     */
    protected function setReeks($ids = null) {
        if(!isset($ids)) {
            $this->reeks = App::resolve('database')->prepQuery('select', 'reeks')->getAll();
        } else {
            $this->reeks = App::resolve('database')->prepQuery('select', 'reeks', $ids)->getAll();
        }

        return $this->countItems();
    }

    /*  countItems():
            This function counts all items with each reeks that has been set globally.

            Return Value: None.
     */
    protected function countItems() {
        foreach($this->reeks as $key => $value ) {
            $this->reeks[$key]['Item_Aantal'] = App::resolve('database')->countItems([
                'Item_Reeks' => $value['Reeks_Index']
            ]);
        }

        return;
    }

    /*  checkDup($name):
            This function checks for duplicate reeks names, and optionally check if its not the same item, if a index as provided.
            The result of this operation, is stored in the class global $duplicate.
                $name (String)      - The name of the reeks that is being created/edited.
                $index (froced Int) - The index value of the reeks that is being edited.
            
            Return Value: None.
     */
    protected function checkDup($name, int $index=null) {
        foreach($this->reeks as $key => $items) {
            if($items['Reeks_Naam'] === $name) {
                if($items['Reeks_Index'] === $index && !isset($this->duplicate)) {
                    $this->duplicate = FALSE;
                }

                if($items['Reeks_Index'] != $index && !isset($this->duplicate)) {
                    $this->duplicate = TRUE;
                }
            }
        }

        return;
    }

    /*  getSingReeks():
            This functions gets a single reeks, requested by specific user actions.
                $ids (Assoc Arr)    - The id pair associated with the requested reeks.
            
            Return Value: Associative Array.
     */
    public function getSingReeks($ids) {
        if(!isset($this->reeks)) {
            $this->setReeks($ids);
        }

        return $this->reeks[0];
    }

    /*  getAllReeks():
            This function simplet gets all reeks data for the controller request.

            Return Value: Multi-dimensional Associative Array.
     */
    public function getAllReeks() {
        if(!isset($this->reeks)) {
            $this->setReeks();
        }

        return $this->reeks;
    }

    // Replaced with getKey();
    /*  getId($ids):
            This function attempt to get a reeks Index, based on another id pair (like name or autheur).
                $ids (Assoc Arr)    - The id pair associated with the reeks the id is required for.
            
            Return Value: Int.
     */
    public function getId($ids) {
        if(!isset($this->reeks)) {
            $this->setReeks($ids);
        }

        return $this->reeks[0]['Reeks_Index'];
    }

    // Replaced with getKey();
    /*  getName($ids):
            This function attempts to get a reeks name, based on other id pairs (like index or autheur)
                $ids (Assoc Arr)    - The id pair associate with the Reeks that we need the name of.
            
            Return Value: String.
     */
    public function getName($ids) {
        if(!isset($this->reeks)) {
            $this->setReeks($ids);
        }

        return $this->reeks[0]['Reeks_Naam'];
    }

    /*  getKey($id, $key):
     */
    public function getKey($id, $key) {
        return App::resolve('database')->prepQuery(
            'select',
            'reeks',
            $id
        )->find($key);
    }

    /*  createReeks($data):
            This function attempts to create a new Reeks record in the database, including a duplicate name check.
                $data (Assoc Arr)       - The POST data as presented by the controller/user.
                $dbData (Assoc Arr)     - The POST data prepared for the PDO action.
                $store (String/null)    - A temp store to evaluate the database operation
            
            Return Value:
                On failure - String.
                On success - Boolean.
     */
    public function createReeks($data) {
        if(!isset($this->reeks)) {
            $this->setReeks();
        }

        $this->checkDup($data['naam']);

        if($this->duplicate) {
            return App::resolve('errors')->getError('reeks', 'duplicate');
        }

        $dbData = [
            'Reeks_Naam' => $data['naam'],
            'Reeks_Maker' => $data['makers'],
            'Reeks_Opmerk' => $data['opmerking']
        ];

        $store = App::resolve('database')->prepQuery('insert', 'reeks', $dbData)->getAll();

        return is_string($store) ? App::resolve('errors')->getError('items', 'store-error') : TRUE;
    }

    /*  updateReeks($ids, $data):
            This function attempts to update a reeks record in the database, incl a duplicate name check.
                $ids (Assoc Arr)        - An id pair that is associated with the reeks record that needs to be updated.
                $data (Assoc Arr)       - The POST data as presented by the controller/user.
                $dbData (Assoc Arr)     - The POST data prepared for the PDO action.
                $store (String/null)    - A temp store to evaluate the database operation
            
            Return Value:
                On failure - String.
                On success - Boolean.
     */
    public function updateReeks($ids, $data) {
        if(!isset($this->reeks)) {
            $this->setReeks();
        }

        $this->checkDup($data['naam'], $data['index']);

        if($this->duplicate) {
            return App::resolve('errors')->getError('reeks', 'duplicate');
        }

        $dbData = [
            'Reeks_Naam' => $data['naam'],
            'Reeks_Maker' => $data['makers'],
            'Reeks_Opmerk' => $data['opmerking']
        ];

        $store = App::resolve('database')->prepQuery('update', 'reeks', $ids, $dbData)->getAll();

        return is_string($store) ? App::resolve('errors')->getError('items', 'store-error') : TRUE;
    }

    /*  remReeks($ids):
            This function attempt to remove a reeks records from the database.
                $ids (Assoc Arr)        - An id pair that is associated with the reeks record that needs to be removed.
                $store (String/null)    - A temp store to evaluate the database operation
            
            Return Value:
                On failure - String.
                On success - Boolean.
     */
    public function remReeks($ids) {
        $store = App::resolve('database')->prepQuery('delete', 'reeks', $ids)->getAll();

        if(is_string($store)) {
            return App::resolve('errors')->getError('reeks', 'rem-fail');
        }

        return is_string($store) ? App::resolve('errors')->getError('reeks', 'rem-fail') : TRUE;
    }
}