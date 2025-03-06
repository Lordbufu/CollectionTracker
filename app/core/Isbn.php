<?php
/*
    // Debug info, for testing the isbn manual search functions.
    // Optional isbn 1:
    //      9781875750214 -> returns only 1 result

    // Optional isbn 2
    //      9020667505
    //      9789020642506
    //          De Kameleon in het goud

    // Optional isbn 3 ( 200+ items found )
    //      0123456789
 */

/*  Isbn Class:
        This class uses the Google Book API, to request data based on either a scanned barcode, or a specific ISBN number.
        After the request, it is evaluated and broken down, either to add/remove things for the user there collection.
        Or if the admin used it, to pre-fill item forms, to potentially make adding them a little bit easier.

        The choice for the Google Book API was made, because its a free to use API, that has a fair amount of data in it.
        Idealy this should have been a paid Book API that is kept up-to-date, and maybe also a similar type of API for music/movies/board games etc.
 */

namespace App\Core;

use App\Core\App;

class Isbn {
    /* Base variable for processing the request, and parsing its data. */
    protected $base_url = 'https://www.googleapis.com/books/v1/volumes?q=';
    protected $req_url;
    protected $requested;
    protected $checked;
    protected $errors;
    protected $titles = ['Titles'];
    protected $isbns = [];

    /*  set_url($isbn):
            This function simply adds the isbn value, at the end of the url string, so we can request data from the Google API.
            It also set a increases the default amount of items that is returned/show, incase there are more result found.
            Though its likely that a max of 40 items per request, is going to be overkill for default operations.
                $isbn (String)  - The ISBN number as parsed from the scanned barcode, or manual user request.
            
            Return Value: Boolean.
     */
    protected function set_url($isbn) {
        /* If no request url was set, i use the base url to make it, together with the provide isbn code. */
        if(!isset($req_url)) {
            $this->req_url = $this->base_url . 'ISBN:' . $isbn;
            $this->req_url = $this->req_url . '&startIndex=0&maxResults=40';
        }

        /* If the request url is the correct length (isbn_10 = 88 / isbn_13 = 91), return TRUE to caller. */
        if(strlen($this->req_url) === 88 || strlen($this->req_url) === 91) {
            return TRUE;
        }
        
        /* If something was wrong, return FALSE to caller. */
        return FALSE;
    }

    /*  request_data():
            This function simply requests, and parses the initial API request.
                $content (String)   - The unfiltered data request from the Google API.
            
            Return Value: None.
     */
    protected function request_data() {
        /* If API data was not stored yet, i get it as a string, and then convert it to associative array data. */
        if(!isset($this->requested)) {
            $content = file_get_contents($this->req_url);
            $this->requested = json_decode($content, TRUE);
        }

        /* If for some reason no array data was store, return FALSE to the caller. */
        if(!is_array($this->requested)) {
            return FALSE;
        }

        /* If all is well, i return TRUE to the caller. */
        return TRUE;
    }

    /*  check_data():
            This function checks the initial API request, to see how many items are returned.
            So i can futher process/take action, depending on the result of this function.
        
            Return Value: Int.
                0   -> No items found.
                1   -> Only 1 item was found.
                2   -> More then 1 items where found.
     */
    protected function check_data() {
        /* Check if no items where found, and prep a error to return to the user, unset the request and return 0 to the caller. */
        if($this->requested['totalItems'] === 0) {
            $this->errors['error'] = App::resolve('errors')->getError('isbn', 'no-items');
            unset($this->requested);
            return 0;
        }

        /* If only 1 item was found, set that to the checked variable, unset the request and return 1 to the caller. */
        if($this->requested['totalItems'] === 1) {
            $this->checked = $this->requested['items'][0];
            unset($this->requested);
            return 1;
        }

        /* If more items where found, set the all to the checked variable, unset the request and return 2 to the caller. */
        $this->checked = $this->requested['items'];
        unset($this->requested);
        return 2;
    }

    /*  get_titles():
            Very straight forward function, that simply gets all the titles in the checked request data.

            Return Value: None.
     */
    protected function get_titles() {
        /* Loop over all checked items, store the titel of each item in the titles variable for later us. */
        foreach($this->checked as $key => $item) {
            if(isset($this->checked[$key]['volumeInfo']['title'])) {
                array_push($this->titles, $this->checked[$key]['volumeInfo']['title']);
            }
        }

        /* If no titles where stored, return FALSE to the caller. */
        if($this->titles === 1) {
            return FALSE;
        }

        /* If all is well return TRUE to the caller. */
        return TRUE;
    }

    /*  check_items($index):
            This functions checks if the returned API results, have a match based on the items names, inside the current reeks.
            If a match is found, it will also seperate the ISBN numbers, so we can compare those at a later stage.
                $index (Int)   - The index of the currently selected Reeks.
                $rItems (Array) - Then items that belong to the currently selected Reeks.

            Return Value:
                On success -> Array (Item_Index)
                On failure -> Boolean (FALSE)
     */
    protected function check_items($index) {
        /* Request all items associated with the requested reeks. */
        $rItems = App::resolve('items')->getAllFor([
            'Item_Reeks' => $index
        ]);

        /* The loop over all reeks items, and right after that over all stored titles. */
        foreach($rItems as $iKey => $iValue) {
            foreach($this->titles as $item) {
                /* If the item name of the reeks item, matches a stored title from the API request, */
                if($iValue['Item_Naam'] == $item) {
                    /* I need to process the said item, to ensure only that items is tored in '$checked' */
                    $isbn = $this->process_choice($item);
                    /* Then i need to store the reeks item its isbn tag as a string (either ISBN_10 or ISBN_13). */
                    $string = 'ISBN_' . strlen($iValue['Item_Isbn']);

                    /* Then i compare the reeks items its isbn value, against the matching isbn type value from the API request. */
                    if($iValue['Item_Isbn'] === $isbn[$string]) {
                        /* And if they match i return the reeks item its indexes to the caller. */
                        return [
                            'Item_Index' => $iValue['Item_Index'],
                            'Item_Reeks' => $iValue['Item_Reeks']
                        ];
                    }
                }
            }
        }

        /* If no match was found, i return FALSE to the caller. */
        return FALSE;
    }

    /*  process_choice($title):
            This function attempts to get the ISBN numbers associated with the provided item title.
            And is used in tandem with the check_items() function.
     */
    protected function process_choice($title) {
        /* I start by looping over all checked items, */
        foreach($this->checked as $key => $item) {
            /* Check if we are dealing with one of more stored items, and grab volume info based on that knowledge. */
            if(array_key_exists(0, $this->checked)) {
                $cItem = $this->checked[$key]['volumeInfo'];
            } else {
                $cItem = $this->checked['volumeInfo'];
            }
            
            /* and comp the requested title against the provided title. */
            if($cItem['title'] === $title) {
                /* If there is a match, i loop over the requested identifiers, */
                foreach($cItem['industryIdentifiers'] as $key => $value) {
                    /* And store the ISBN_10 globally. */
                    if($value['type'] === 'ISBN_10') {
                        $this->isbns[$value['type']] = $value['identifier'];
                    }

                    /* And store the ISBN_13 globally. */
                    if($value['type'] === 'ISBN_13') {
                        $this->isbns[$value['type']] = $value['identifier'];
                    }
                }
            }
        }

        /* If no isbn numbers where found, i store a invalid tag globally. */
        if(empty($this->isbns)) {
            $this->isbns['invalid'] = TRUE;
        }

        /* Regardless of the outcome, i return the stored isbn array. */
        return $this->isbns;
    }

    /* get_choice($title):
            This function gets a entire item, based on a item its title, and is used when a user has confirmed a title choice.
     */
    protected function get_choice($title) {
        /* Set checked to all requested items. */
        if(!isset($this->checked)) {
            $this->checked = $this->requested['items'];
        }
        
        /* I start by looping over all checked items, */
        foreach($this->checked as $key => $item) {
            /* Check if we are dealing with one of more stored items, and grabe volume info based on that knowledge. */
            if(array_key_exists(0, $this->checked)) {
                $cItem = $this->checked[$key]['volumeInfo'];
            } else {
                $cItem = $this->checked['volumeInfo'];
            }

            /* and comp the requested title against the provided title. */
            if($cItem['title'] == $title) {
                /* Return the entire matched item to the caller. */
                return $cItem;
            }
        }

        /* Return false if no match was found. */
        return FALSE;
    }

    /*  startRequest($isbn, $reeks):
            This is the start of the Google API request, it will do the request in parts, so its easier to debug issues.
            Because the scope is so wide, it has a variable return value, strongly depending on what route it hits.
            Most comments are also left inside the function, because of the various outcomes.
                $isbn (Int)             - The isbn code from the scanner or the search button.
                $reeks (Int)            - The index value of the currently selected reeks.
                $request (Bool)         - The result of the API request.
                $check (Int)            - The result of the data check, to see how much the request returned.
                $iCheck (Array/Bool)    - The result of the item check, so i know of the item is in the current reeks.
     */
    public function startRequest($isbn, $reeks, $admin = FALSE) {
        /* If the request URL could not be set, i store a error for user feedback. */
        if(!$this->set_url($isbn)) {
            $this->errors = App::resolve('errors')->getError('isbn', 'no-request');
        }

        /* If a request URL was set, i attempt to request data from the Google API. */
        $request = $this->request_data();

        /* If the request failed, */
        if(!$request || isset($this->errors)) {
            /* and no error were set yet, i return a error for user feedback. */
            if(!isset($this->error)) {
                return App::resolve('errors')->getError('isbn', 'no-request');
            }

            /* If a error was already set, i return the stored errors to the caller. */
            return $this->errors;
        }

        /* Check the requested data, for how many items there are. */
        $check = $this->check_data();

        /* If the request returned 0 items, return the check_data() error to the caller. */
        if($check === 0) {
            return $this->errors;
        }

        /* If more then 1 item was found & there titles where stored, */
        if($check === 2 && $this->get_titles()) {
            /* check if the items are in the currently selected series, */
            $iCheck = $this->check_items($reeks);

            /* if the check failed, */
            if(!is_array($iCheck) || $admin) {
                /* and the user is a regular user, i return a no-match error. */
                if(isset($_SESSION['user']['rights']) && $_SESSION['user']['rights'] === 'user') {
                    return App::resolve('errors')->getError('isbn', 'no-match');        
                }

                /* If the user is a Admin user, i return a title choice. */
                return $this->titles;
            }

            /* Return the stored id's, if a match was found. */
            return $iCheck;
        }

        /* If the admin switch was set, this was a isbn search operation, and we need to return the item it found. */
        if($admin) {
            return $this->checked['volumeInfo'];
        }

        /* Push the single items title into the titles array, */
        array_push($this->titles, $this->checked['volumeInfo']['title']);

        /* evaluate if said item is also in the current reeks, */
        $iCheck = $this->check_items($reeks);

        /* if there is no index value returned, return a error for user feedback.  */
        if(!is_array($iCheck)) {
            return App::resolve('errors')->getError('isbn', 'no-match');
        }

        /* If all is well, return the array data to the caller. */
        return $iCheck;
    }

    /*  confirmChoice($isbn, $title):
            This function deals with processing a title choice made by the user, atm this is only relevant for the Administrator.
     */
    public function confirmChoice($data) {
        /* If the request URL could not be set, i store a error for user feedback. */
        if(!$this->set_url($data['isbn-choice'])) {
            return App::resolve('errors')->getError('isbn', 'no-request');
        }

        /* If a request URL was set, i attempt to request data from the Google API. */
        if(!$this->request_data()) {
            return App::resolve('errors')->getError('isbn', 'no-request');
        }

        /* Get the item matching the title the user picked. */
        $item = $this->get_choice($data['title-choice']);

        /* Return a error if no item was returned. */
        if(!is_array($item)) {
            return App::resolve('errors')->getError('isbn', 'choice-fail');
        }

        /* Set all potentially usefull info to a new variable. */
        $newItem = [
            'title' => (isset($item['title'])) ? $item['title'] : '',
            'autheurs' => (isset($item['authors'])) ? $item['authors'] : '',
            'date' => (isset($item['publishedDate'])) ? $item['publishedDate'] : '',
            'opmerking' => (isset($item['description'])) ? $item['description'] : '',
            'isbn' => (isset($item['industryIdentifiers'])) ? $item['industryIdentifiers'] : '',
            'cover' => (isset($item['imageLinks'])) ? $item['imageLinks'] : ''
        ];

        /* Return all ptentially usefull information to the caller. */
        return $newItem;
    }
}
?>