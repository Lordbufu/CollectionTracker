<?php

namespace App\Core;

use App\Core\App;

class Isbn {
    /* Global data variables, for requesting/parsing and processing the API request. */
    protected $reqUrl;
    protected $stringData;
    protected $jsonData;
    protected $checkedData;
    /* Global detection variables, to detect errors or title choices. */
    protected $errors;
    protected $titles;

    /*  __construct:
            Each time the class is constrcuted, i want the basis required url to be set, so i can append to it.

            Return Value: None.
     */
    public function __construct() {
        $this->reqUrl = 'https://www.googleapis.com/books/v1/volumes?q=';

        return;
    }

    /*  set_url($isbn):
            Simply adds the isbn to the url, and then adds the parameters to get the max amount of results.
            After that it also checks if the the string length matches the expected length for isbn 10 & 13 values.

            Return Value: Boolean.
     */
    protected function setUrl($isbn) {
        if(isset($this->reqUrl)) {
            $this->reqUrl = $this->reqUrl . 'ISBN:' . $isbn;
            $this->reqUrl = $this->reqUrl . '&startIndex=0&maxResults=40';
        }

        if(strlen($this->reqUrl) === 88 || strlen($this->reqUrl) === 91) {
            return TRUE;
        }

        $this->errors['set-error'] = 'De Google Books API actie is mislukt, probeer het nogmaals of neem contact op met uw Administrator!';
        
        return FALSE;
    }

    /*  getData():
            Set the initial string data from the Google Books API, and check if a string was actually set.

            Return Value: Boolean.
     */
    protected function getData() {
        $this->stringData = file_get_contents($this->reqUrl);

        return is_string($this->stringData);
    }

    /*  convData():
            Convert the Google Books API string to a PHP associative array.

            Return Value: None.
     */
    protected function convData() {
        $this->jsonData = json_decode($this->stringData, TRUE);
        return;
    }

    /*  countItems():
            This function return the stored item count for logic evaluation.

            Return Value: Int.
     */
    protected function countItems() {
        return $this->jsonData['totalItems'];
    }

    /*  getTitles():
            If more then 1 item is found, this function prepares the titles of said items, so the user can make a choice based on that.

            Return Value: none.
     */
    protected function getTitles() {
        foreach($this->checkedData as $key => $item) {
            if(isset($this->checkedData[$key]['volumeInfo']['title'])) {
                array_push($this->titles, $this->checkedData[$key]['volumeInfo']['title']);
            }
        }

        return;
    }

    /*  processData():
            This function request data from the Google Books API, and then parses it for use in PHP.
            Once parsed it counts the returned items, stores either a error or said items, and triggers a titlle search if required.

            Return Value: none.
     */
    protected function processData() {
        if($this->getData()) {
            $this->convData();
        }

        switch($this->countItems()) {
            case 0:
                $this->errors['parse-error'] = App::resolve('errors')->getError('isbn', 'no-items');
                break;
            case 1:
                $this->checkedData = $this->jsonData['items'][0];
                break;
            default:
                $this->checkedData = $this->jsonData['items'];
                $this->titles = ['Titles'];
                break;
        }

        if(isset($this->titles)) {
            $this->getTitles();
        }

        return;
    }

    /*  startRequest($isbn):
            This function deal with the initial Google API request logic.
            And delegates the remaining process to the controller, regardless of the user or path it took.
                $isbn (String)  - The isbn that needs to be searched for in the Google Books API.

            Return Value: Associative Array.
     */
    public function startRequest($isbn) {
        if(!$this->setUrl($isbn)) {
            return $this->errors;
        }

        $this->processData();

        if(isset($this->errors)) {
            return $this->errors;
        }

        if(isset($this->titles)) {
            return $this->titles;
        }

        return $this->checkedData['volumeInfo'];
    }

    /*  confirmChoice($data):
            This function is dealing with returning the correct items, after a title choice was made by the user.
                $data (Assoc Arr)   - Both the isbn that was searched, and the title choice that was made.
                $cItem (Assoc Arr)  - The current item being looped over, that needs to be returned if its a match.

            Return Value: Associative Array.
     */
    public function confirmChoice($data) {
        /* Ensure the current data, is at the same point as it was during the initial startRequest() call. */
        if(!isset($this->checkedData)) {
            if(!$this->setUrl($data['isbn'])) {
                return $this->errors;
            }

            if($this->getData()) {
                $this->convData();
            }

            $this->checkedData = $this->jsonData['items'];
        }

        /* Loop over the items, check the title, and return the item if it matches. */
        foreach($this->checkedData as $key => $item) {
            $cItem = $this->checkedData[$key]['volumeInfo'];

            if($cItem['title'] === $data['title']) {
                return $cItem;
            }
        }

        /* Return a error if nothing was found. */
        return App::resolve('errors')->getError('isbn', 'choice-fail');
    }
}