<?php

namespace App\Core;

class ProcessPost {
    protected $new;

    /*  items($data):
     */
    protected function items($data) {
        $this->new = [
            'Item_Index' => $data['iIndex'] ?? '',
            'Item_Reeks' => $data['rIndex'] ?? '',
            'Item_Auth' => $data['autheur'] ?? '',
            'Item_Naam' => $data['naam'] ?? '',
            'Item_Nummer' => $data['nummer'] ?? '',
            'Item_Uitgd' => $data['datum'] ?? '',
            'Item_Isbn' => $data['isbn'] ?? '',
            'Item_Opm' => $data['opmerking'] ?? ''
        ];

        return;
    }

    /*  reeks($data):
     */
    protected function reeks($data) {

    }

    /*  collectie($data):
     */
    protected function collectie($data) {

    }

    /*  store($object, $data):
     */
    public function store($object, $data) {
        $this->$object($data);

        if(!isset($this->new)) {
            return App::resolve('errors')->getError('processing', 'failed');
        }

        return $this->new;
    }
}