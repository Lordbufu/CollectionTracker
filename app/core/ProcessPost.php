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
        /* Set required data first, cant make a new record without these. */
        $this->new = [
            'Item_Reeks' => $data['rIndex'],
            'Item_Naam' => $data['naam'],
            'Item_Isbn' => $data['isbn']
        ];

        /* Add the index values, depending on witch where set, rather then leaving a empty value. */
        if(isset($data['iIndex']) && !empty($data['iIndex'])) {
            $this->new['Item_Index'] = $data['iIndex'];
        }

        /* Not required inputs, only add if set. */
        if(isset($data['nummer'])) { $this->new['Item_Nummer'] = $data['nummer']; }
        if(isset($data['datum'])) { $this->new['Item_Uitgd'] = $data['datum']; }
        if(isset($data['autheur'])) { $this->new['Item_Auth'] = $data['autheur']; }
        if(isset($data['opmerking'])) { $this->new['Item_Opm'] = $data['opmerking']; }
        if(isset($data['plaatje'])) { $this->new['Item_Plaatje'] = $data['plaatje']; }

        return;
    }

    /*  reeks($data):
            This function processes all reeks related post data, and prepares a data array that can be used to interact with the database.
                $data (Assoc Arr)   - The POST data from the controller.
            
            Return Value: None.
     */
    protected function reeks($data) {
        $this->new['Reeks_Naam'] = $data['naam'];

        if(isset($data['maker'])) { $this->new['Reeks_Maker'] = $data['maker']; }
        if(isset($data['opmerking'])) { $this->new['Reeks_Opmerk'] = $data['opmerking']; }
        if(isset($data['plaatje'])) { $this->new['Reeks_Plaatje'] = $data['plaatje']; }

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