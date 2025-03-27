<?php

namespace App\Core;

class ProcessApiReq {
    protected $new;

    /*  add($key, $value):
            Add specific data to the new array, with a pre-defined key.
                $key (String)   - The key that a value should be associated to.
                $value (String) - The value from the Google API, that i need to bind to a specific key.
            
            Return Value: None.
     */
    protected function add($key, $value) {
        $this->new[$key] = $value;
    }

    /*  processData($data):
            This function take the Google API data, and prepares it for the item-maken pop-in, so the data can be used to add things to the database.
                $data (Assoc Arr)   - The raw item data that was parsed from the Google Books API.
            
            Return Value: Associative Arrray.
     */
    public function processData($data) {
        foreach($data as $oKey => $oValue) {
            /* Deal with the item publish date, no need to truncate. */
            if($oKey === 'publishedDate') {
                $this->add('datum', $oValue);
            }

            /* Deal with the item title, and truncate anything greate then .. length. */
            if($oKey === 'title') {
                if(strlen($oValue) > 50) {
                    $this->add('naam', substr($oValue, 0, 50));
                } else {
                    $this->add('naam', $oValue);
                }
            }

            /* Deal with the item description, and truncate anything greate then 254 length. */
            if($oKey === 'description') {
                if(strlen($oValue) > 254) {
                    $this->add('opmerking', substr($oValue, 0, 254));
                } else {
                    $this->add('opmerking', $oValue);
                }
            }

            /* Deal with the item author(s). */
            if($oKey === 'authors') {
                foreach($oValue as $name) {
                    /* If nothing was set, add the name only. */
                    if(!isset($this->new['autheur'])) {
                        $this->add('autheur', $name);
                    }

                    /* If something was set, add the previous autheur first and add ', '  between the entries. */
                    if(isset($this->new['autheur']) && $this->new['autheur'] !== $name) {
                        $this->add('autheur', $this->new['autheur'] . ', ' . $name);
                    }

                    /* Truncate the string, if its longer then the formvalidation allows. */
                    if(strlen($this->new['autheur'])) {
                        $this->add('autheur', substr($this->new['autheur'], 0, 50));
                    }
                }
            }

            /* Deal with the item indentifier(s), prefering to store the ISBN 13 over the ISBN 10. */
            if($oKey === 'industryIdentifiers') {
                foreach($oValue as $pair){
                    foreach($pair as $iKey => $iValue) {
                        if($iKey === 'type' && $iValue === 'ISBN_13') {
                            $this->add('isbn', $pair['identifier']);
                        }

                        if($iKey === 'type' && $iValue === 'ISBN_10' && !isset($this->new['isbn'])) {
                            $this->add('isbn', $pair['identifier']);
                        }
                    }
                }

                /* If no ISBN was found, witch would be odd, store at least a 0. */
                if(!isset($this->new['isbn'])) {
                    $this->add('isbn', 0);
                }
            }

            /* Deal with processing the image links in the correct order, incl converting it to a base64 blob */
            if($oKey === 'imageLinks') {
                foreach($oValue as $iKey => $iValue) {
                    if($iKey === 'thumbnail') {
                        $this->add('plaatje', App::resolve('file')->procUrl($iValue));
                    }

                    if($iKey === 'smallThumbnail') {
                        $this->add('plaatje', App::resolve('file')->procUrl($iValue));
                    }
                }
            }
        }

        return $this->new;
    }
}