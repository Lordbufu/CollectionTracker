<?php

namespace App\Core;

use App\Core\App;

class ProcessPost {
    protected $new;

    /*  items($data):
            This function processes all items related post data, and prepares a data array that can be used to interact with the database.
                $data (Assoc Arr)   - The POST data from the controller.
            
            Return Value: None.
     */
    protected function items($data) {
        $this->new = [
            'Item_Auth' => $data['autheur'] ?? '',
            'Item_Naam' => $data['naam'],
            'Item_Nummer' => empty($data['nummer']) ? 0 : (int) $data['nummer'],
            'Item_Uitgd' => empty($data['datum']) ? date('Y-m-d') : $data['datum'],
            'Item_Isbn' => $data['isbn'],
            'Item_Opm' => $data['opmerking'] ?? ''
        ];

        /* Add the index values, depending on witch where set, rather then leaving a empty value. */
        if(isset($data['iIndex']) && !empty($data['iIndex'])) {
            $this->new['Item_Index'] = $data['iIndex'];
        }

        if(isset($data['rIndex']) && !empty($data['rIndex'])) {
            $this->new['Item_Reeks'] = $data['rIndex'];
        }

        return;
    }

    /*  reeks($data):
            This function processes all reeks related post data, and prepares a data array that can be used to interact with the database.
                $data (Assoc Arr)   - The POST data from the controller.
            
            Return Value: None.
     */
    protected function reeks($data) {
        $this->new = [
            'Reeks_Naam' => $data['naam'],
            'Reeks_Maker' => $data['maker'],
            'Reeks_Opmerk' => $data['opmerking']
        ];

        /* Add the index if it was set ? ... not sure this is even relevant */
        if(isset($data['rIndex']) && !empty($data['rIndex'])) {
            $this->new['Reeks_Index'] = $data['rIndex'];
        }

        return;
    }

    /*  collectie($data):
            This function processes all collectie related post data, and prepares a data array that can be used to interact with the database.
                $data (Assoc Arr)   - The POST data from the controller.
            
            Return Value: None.
     */
    protected function collectie($data) {
        $this->new = [
            'Gebr_Index' => $data['Gebr_Index'],
            'Item_Index' => (int) $_POST['index'],
            'Reeks_Index' => App::resolve('items')->getKey([
                'Item_Index' => $data['index']],
                'Item_Reeks'
        )];

        return;
    }

    /*  store($object, $data):
            This function will attempt to store the POST data in the global $new store.
            And will return the result of said operation, the error path isnt possible atm, but there for when i know how i wanne do that.
                $object (String)    - A string value, associated with a function, and sharing the name with the db table that hold said data.
                $data (Assoc Arr)   - The POST data as provided by the controller.

            Return Value: Associative Array
     */
    public function store($object, $data) {
        $this->$object($data);

        if(!isset($this->new)) {
            return App::resolve('errors')->getError('processing', 'failed');
        }

        return $this->new;
    }
}