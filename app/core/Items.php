<?php

namespace App\Core;

class Items {
    protected $items;
    protected $duplication;

    /*  setItems($ids): */
    protected function setItems($ids = null) {
        if(!isset($ids)) {
            $this->items = App::resolve('database')->prepQuery('select', 'items')->getAll();
        } else {
            $this->items = App::resolve('database')->prepQuery('select', 'items', $ids)->getAll();
        }

        if(!is_array($this->items)) {
            return FALSE;
        }

        return TRUE;
    }

    // W.I.P.
    /*  dupCheck($id, ): */
    protected function dupCheck($ids, $data) {
        $this->duplication = FALSE;                                             // set check state to false

        if(!isset($this->items) && !$this->setItems()) {                        // attempt to load the item with specific ids
            return App::resolve('errors')->getError('items', 'find-error');
        }

        foreach($this->items as $key => $value) {                               // loop over all stored items,
            if($value['Item_Naam'] === $data['naam']) {                         // check if the name already used,
                if($value['Item_Reeks'] === $data['rIndex']) {                  // then check if there als in the same reeks (serie)
                    $this->duplication = TRUE;
                }
            }
        }
    }

    /*  getAllFor($ids): */
    public function getAllFor($ids) {
        if(!isset($this->items) && !$this->setItems($ids)) {
            return App::resolve('errors')->getError('items', 'find-error');
        }

        return $this->items;
    }

    /*  getName($ids): */
    public function getName($ids) {
        if(!isset($this->items) && !$this->setItems($ids)) {
            return App::resolve('errors')->getError('items', 'find-error');
        }

        return $this->items[0]['Item_Naam'];
    }

    /*  createItem($data): */
    public function createItem($data) {
        $ids = [
            'Item_Reeks' => $data['rIndex']
        ];

        $duplicate = FALSE;

        if(!isset($this->items) && !$this->setItems($ids)) {
            return App::resolve('errors')->getError('items', 'find-error');
        }

        foreach($this->items as $key => $value) {
            if($value['Item_Naam'] === $data['naam']) {
                $duplicate = TRUE;
            }
        }

        if($duplicate) {
            return App::resolve('errors')->getError('items', 'duplicate');
        }

        $dbData = [
            'Item_Reeks' => $data['rIndex'],
            'Item_Nummer' => empty($data['nummer']) ? 0 : $data['nummer'],
            'Item_Auth' => $data['autheur'],
            'Item_Naam' => $data['naam'],
            'Item_Plaatje' => $data['cover'],
            'Item_Uitgd' => empty($data['datum']) ? date('Y-m-d') : $data['datum'],
            'Item_Isbn' => $data['isbn'],
            'Item_Opm' => $data['opmerking']
        ];

        $store = App::resolve('database')->prepQuery(
            'insert', 'items', null, $dbData
        )->getAll();

        return is_string($store) ? App::resolve('errors')->getError('items', 'store-error') : TRUE;
    }

    /*  updateItems($ids): */
    public function updateItems($data) {
        /* Set ids used to get/set data from/in the database, and check if the item is duplicate in the reeks/serie. */
        $ids = [
            'Item_Index' => $data['iIndex'],
            'Item_Reeks' => $data['rIndex']
        ];

        $check = $this->dupCheck($ids, $data);

        /* If there was an error string, return said error string. */
        if(is_string($check)) {
            return $check;
        }

        /* Store the data with the correct key values. */
        $dbData = [
            'Item_Reeks' => $data['rIndex'],
            'Item_Nummer' => empty($data['nummer']) ? 0 : $data['nummer'],
            'Item_Auth' => $data['autheur'],
            'Item_Naam' => $data['naam'],
            'Item_Plaatje' => $data['cover'],
            'Item_Uitgd' => empty($data['datum']) ? date('Y/m/d') : $data['datum'],
            'Item_Isbn' => $data['isbn'],
            'Item_Opm' => $data['opmerking']
        ];

        /* If not duplicate, attempt to update the database entry. */
        if(!$this->duplication && !empty($data['iIndex'])) {
            $store = App::resolve('database')->prepQuery('update', 'items', $ids, $dbData)->getAll();
        /* If duplicate, store a feedback error. */
        } else {
            $store = App::resolve('errors')->getError('items', 'duplicate');
        }

        /* Return either a bool or the error string. */
        return is_string($store) ? $store : TRUE;
    }

    /*  remItems($ids): */
    public function remItems($ids) {
        $store = App::resolve('database')->prepQuery('delete', 'items', $ids)->getAll();

        if(is_string($store)) {
            return App::resolve('errors')->getError('items', 'rem-fail');
        }

        return TRUE;
    }

    // Re-Factored everthing below to fit the new name scheme, and code layout.
    /*  getAlbId($name):
            Get serie index based on album name.
                $name (String)  : The name of the album we want the index for.

            Return Value: INT
     */
    public function getAlbId( $name ) {
        $tempAlbum = App::get("database")->selectAllWhere("albums", ["Album_Naam" => $name ])[0];
        return $tempAlbum["Album_Index"];
    }
}