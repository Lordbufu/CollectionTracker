<?php

namespace App\Core;

class Reeks {
    protected $reeks;
    protected $duplicate;

    /*  setReeks($ids): */
    protected function setReeks($ids = null) {
        if(!isset($ids)) {
            $this->reeks = App::resolve('database')->prepQuery('select', 'reeks')->getAll();
        } else {
            $this->reeks = App::resolve('database')->prepQuery('select', 'reeks', $ids)->getAll();
        }

        return $this->countItems();
    }

    /*  countItems(): */
    protected function countItems() {
        foreach($this->reeks as $key => $value ) {
            $this->reeks[$key]['Item_Aantal'] = App::resolve('database')->countItems(['Item_Reeks' => $value['Reeks_Index']]);
        }

        return;
    }

    /*  checkDup($name) */
    protected function checkDup($name) {
        foreach($this->reeks as $key => $items) {
            if($items['Reeks_Naam'] === $name) {
                $this->duplicate = TRUE;
                return TRUE;
            }
        }

        $this->duplicate = FALSE;
        return FALSE;
    }

    /*  getSingReeks(): */
    public function getSingReeks($ids) {
        if(!isset($this->reeks)) {
            $this->setReeks($ids);
        }

        return $this->reeks[0];
    }

    /*  getAllReeks(): */
    public function getAllReeks() {
        if(!isset($this->reeks)) {
            $this->setReeks();
        }

        return $this->reeks;
    }

    /*  getId($ids): */
    public function getId($ids) {
        if(!isset($this->reeks)) {
            $this->setReeks($ids);
        }

        return $this->reeks[0]['Reeks_Index'];
    }

    /*  getName($ids): */
    public function getName($ids) {
        if(!isset($this->reeks)) {
            $this->setReeks($ids);
        }

        return $this->reeks[0]['Reeks_Naam'];
    }

    /*  createReeks($data): */
    public function createReeks($data) {
        /* Check if any reeks info was set, if not set all reeks data. */
        if(!isset($this->reeks)) {
            $this->setReeks();
        }

        /* If duplicate is true now, return a duplicate error for user feedback. */
        if($this->checkDup($data['naam'])) {
            return App::resolve('errors')->getError('reeks', 'duplicate');
        }

        /* If all is well, prep the user info for the database. */
        $dbData = [
            'Reeks_Naam' => $data['naam'],
            'Reeks_Maker' => $data['makers'],
            'Reeks_Opmerk' => $data['opmerking']
        ];

        /* Delegate the store action to the databse class, and request the results with getAll. */
        $store = App::resolve('database')->prepQuery('insert', 'reeks', $dbData)->getAll();

        /* Either return a feedback error or TRUE, depending on the store result. */
        return is_string($store) ? App::resolve('errors')->getError('items', 'store-error') : TRUE;
    }

    /*  updateReeks($ids, $data): */
    public function updateReeks($ids, $data) {
        /* Check if any reeks info was set, if not set all reeks data. */
        if(!isset($this->reeks)) {
            $this->setReeks();
        }

        /* If duplicate is true now, return a duplicate error for user feedback. */
        if($this->checkDup($data['naam'])) {
            return App::resolve('errors')->getError('reeks', 'duplicate');
        }

        /* If all is well, prep the user info for the database. */
        $dbData = [
            'Reeks_Naam' => $data['naam'],
            'Reeks_Maker' => $data['makers'],
            'Reeks_Opmerk' => $data['opmerking']
        ];

        /* Delegate the update action to the databse class, and request the results with getAll. */
        $store = App::resolve('database')->prepQuery('update', 'reeks', $ids, $dbData)->getAll();

        /* Either return a feedback error or TRUE, depending on the store result. */
        return is_string($store) ? App::resolve('errors')->getError('items', 'store-error') : TRUE;
    }

    /*  remReeks($ids): */
    public function remReeks($ids) {
        $store = App::resolve('database')->prepQuery('delete', 'reeks', $ids)->getAll();

        if(is_string($store)) {
            return App::resolve('errors')->getError('reeks', 'rem-fail');
        }

        return TRUE;
    }
}