<?php

namespace App\Core;

class Items {
    protected $items;
    protected $duplicate;

    /*  setItems($ids):
            A set function to load database Item records, potentially based on a specific id pair.
                $ids (Assoc Arr)    - (Optional) Id pair for getting a specific range of items, or even a single item.
            
            Return Value: Boolean.
     */
    protected function setItems($ids = null) {
        if(!isset($ids)) {
            $this->items = App::resolve('database')->prepQuery(
                'select',
                'items'
            )->getAll();
        } else {
            $this->items = App::resolve('database')->prepQuery(
                'select',
                'items',
                $ids
            )->getAll();
        }

        if(!is_array($this->items)) {
            return FALSE;
        }

        return TRUE;
    }

    /*  dupCheck($id, $data):
            A function that checks if the Item name is duplicate, within the same reeks only.
                $id (Assoc Arr)     - The Id pair associated with the specific item.
            
            Return Value: None.
     */
    protected function dupCheck($ids) {
        $this->duplicate = FALSE;

        if(!$this->setItems(['Item_Reeks' => $ids['iReeks']])) {
            return App::resolve('errors')->getError('items', 'find-error');
        }

        /* Loop over all stored items, and compare the stored names vs the edited name; */
        foreach($this->items as $key => $value) {
            if($value['Item_Naam'] === $ids['naam']) {
                /* If a reeks id was passed in, and its doesnt match, the item name is duplicate; */
                if(isset($ids['iReeks']) && (int) $ids['iReeks'] !== $value['Item_Reeks']) {
                    $this->duplicate = TRUE;
                /* If the id wasnt set, its also duplicate. */
                } elseif(!isset($ids['iReeks'])) {
                    $this->duplicate = TRUE;
                }
            }
        }
        return;
    }

    /*  getAllFor($ids):
            This function get a specific set of items, for example off items in a specific reeks.
                $ids (Assoc Arr)    - The Id pair associated with the specific selection.
            
            Return Value: Associative Array.
     */
    public function getAllFor($ids) {
        if(!isset($this->items) && !$this->setItems($ids)) {
            return App::resolve('errors')->getError('items', 'find-error');
        }

        return $this->items;
    }

    /* Potentially Redundant now that im wrote the getKey($ids, $key) function. */
    /*  getName($ids):
            This function simple return the name of a item, based on the provided id pair.
                $ids (Assoc Arr)    - The id pair to fetch the item we want the name of.
            
            Return Value: String.
     */
    public function getName($ids) {
        if(!isset($this->items) && !$this->setItems($ids)) {
            return App::resolve('errors')->getError('items', 'find-error');
        }

        return $this->items[0]['Item_Naam'];
    }

    /*  getKey($id, $key):
            This function simple returns a specifc key value, based on another key value (id), for a specific item.
                $id (Assoc Arr) - The identifier i want to use to find a specific item.
                $key (String)   - The key i want to have for the logic im using.
     */
    public function getKey($id, $key) {
        return App::resolve('database')->prepQuery(
            'select',
            'items',
            $id
        )->find($key);
    }

    /*  createItem($data):
            This function attempt to add a new Item record to the database, while also checking if a item is duplicate within that Reeks.
                $data (Assoc Arr)       - The POST data we need to create the new database record.
                $ids (Assoc Arr)        - An id pair to check if the items is duplicate within the reeks its being added to.
                $check (String/null)    - A temp store to check if the duplicate check had issue setting the items within a reeks.
                $dbData (Assoc Arr)     - The POST data, prepared for the DB query, filtering out any potential issues.
                $store (String/null)    - A temp store to evaluate the result of the database operation.

            Return Value:
                On failure - String.
                On success - Boolean.
     */
    public function createItem($data) {
        $ids = [
            'Item_Reeks' => $data['rIndex']
        ];

        $check = $this->dupCheck($ids);

        if(is_string($check)) {
            return $check;
        }

        if($this->duplicate) {
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
            'insert',
            'items',
            null,
            $dbData
        )->getAll();

        return is_string($store) ? App::resolve('errors')->getError('items', 'store-error') : TRUE;
    }

    /*  updateItems($ids):
            This function attempt to update database item record, with new POST data provided by the user.
                $data (Assoc Arr)       - The POST data we need to create the new database record.
                $check (String/null)    - A temp store to check if the duplicate check had issue setting the items within a reeks.
                $uIds (Assoc Arr)        - The id pair, used to update a single record, in this case the item-index and item-reeks.
                $dbData (Assoc Arr)     - The POST data, prepared for the DB query, filtering out any potential issues.
                $store (String/null)    - A temp store to evaluate the result of the database operation.
            
            Return Value:
                On failure - String.
                On success - Boolean.
     */
    public function updateItems($data) {
        $check = $this->dupCheck([
            'iReeks' => $data['rIndex'],
            'naam' => $data['naam']
        ]);

        if(is_string($check)) {
            return $check;
        }

        if($this->duplicate) {
            return App::resolve('errors')->getError('items', 'duplicate');
        }

        $uId = [
            'Item_Index' => $this->getKey(['Item_Naam' => $data['naam']], 'Item_Index'),
            'Item_Reeks' => $data['rIndex']
        ];

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

        $store = App::resolve('database')->prepQuery(
            'update',
            'items',
            $uId,
            $dbData
        )->getAll();

        return is_string($store) ? App::resolve('errors')->getError('items', 'store-error') : TRUE;
    }

    /*  remItems($ids):
            This function is used to remove single, or multiple items from the items table.
                $ids (Assoc Arr)        - The id pair associate with the item or items that need to be removed.
                $store (String/null)    - A temp store to evaluate the databse operation.
            
            Return Value:
                On failure - String.
                On success - Boolean.
     */
    public function remItems($ids) {
        $store = App::resolve('database')->prepQuery(
            'delete',
            'items',
            $ids
        )->getAll();

        return is_string($store) ? App::resolve('errors')->getError('items', 'rem-fail') : TRUE;
    }
}