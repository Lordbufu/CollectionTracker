<?php
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
            This function prepares the url for the Google Book API Request, incl the search parameters, returning the max amount of results.
                $isbn (String)  - The ISBN number as parsed from the scanned barcode, or manual user request.
            
            Return Value: Boolean.
     */
    protected function set_url($isbn) {
        if(!isset($req_url)) {
            $this->req_url = $this->base_url . 'ISBN:' . $isbn;
            $this->req_url = $this->req_url . '&startIndex=0&maxResults=40';
        }

        if(strlen($this->req_url) === 88 || strlen($this->req_url) === 91) {    // Check if the lenght makes sense for a isbn 10 and 13 value.
            return TRUE;
        }
        
        return FALSE;
    }

    /*  request_data():
            This function simply requests, and parses the initial API request, for further processing.
                $content (String)   - The unfiltered data request from the Google API.
            
            Return Value: None.
     */
    protected function request_data() {
        if(!isset($this->requested)) {
            $content = file_get_contents($this->req_url);
            $this->requested = json_decode($content, TRUE);
        }

        if(!is_array($this->requested)) {
            return FALSE;
        }

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

            Return Value: Boolean.
     */
    protected function get_titles() {
        foreach($this->checked as $key => $item) {
            if(isset($this->checked[$key]['volumeInfo']['title'])) {
                array_push($this->titles, $this->checked[$key]['volumeInfo']['title']);
            }
        }

        if($this->titles === 1) {
            return FALSE;
        }

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
        $rItems = App::resolve('items')->getAllFor(['Item_Reeks' => $index]);

        foreach($rItems as $iKey => $iValue) {                                      // Start looping over all the reeks items.
            foreach($this->titles as $item) {
                if($iValue['Item_Naam'] == $item) {                                 // Compare the item names.
                    $isbn = $this->process_choice($item);
                    $string = 'ISBN_' . strlen($iValue['Item_Isbn']);

                    if($iValue['Item_Isbn'] === $isbn[$string]) {                   // Compare isbn value, if matching return id's for db operations.
                        return ['Item_Index' => $iValue['Item_Index'], 'Item_Reeks' => $iValue['Item_Reeks']];
                    }
                }
            }
        }

        return FALSE;                                                               // Return false if nothing matches.
    }

    /*  process_choice($title):
            This function attempts to get the ISBN numbers associated with the provided item title.
            And is used in tandem with the check_items() function.
     */
    protected function process_choice($title) {
        /* I start by looping over all checked items, check if we are dealing with one of more stored items, and grab volume info based on that knowledge. */
        foreach($this->checked as $key => $item) {
            if(array_key_exists(0, $this->checked)) {
                $cItem = $this->checked[$key]['volumeInfo'];
            } else {
                $cItem = $this->checked['volumeInfo'];
            }
            
            /* Compare the requested title against the provided title, and store the relevant id's if matching. */
            if($cItem['title'] === $title) {
                foreach($cItem['industryIdentifiers'] as $key => $value) {
                    if($value['type'] === 'ISBN_10') { $this->isbns[$value['type']] = $value['identifier']; }
                    if($value['type'] === 'ISBN_13') { $this->isbns[$value['type']] = $value['identifier']; }
                }
            }
        }

        if(empty($this->isbns)) { $this->isbns['invalid'] = TRUE; }     // If no isbn numbers where found, i store a invalid tag globally.
        return $this->isbns;                                            // Always the isbn variable.
    }

    /* get_choice($title):
            This function gets a entire item, based on a item its title, and is used when a user has confirmed a title choice.
     */
    protected function get_choice($title) {
        if(!isset($this->checked)) { $this->checked = $this->requested['items']; }      // Set checked to all requested items.
        
        foreach($this->checked as $key => $item) {                                      // Loop over the checked items, and store the information i need\want.
            if(array_key_exists(0, $this->checked)) {
                $cItem = $this->checked[$key]['volumeInfo'];
            } else {
                $cItem = $this->checked['volumeInfo'];
            }

            if($cItem['title'] == $title) { return $cItem; }                            // If the title matches, return the entire matched item to the caller.
        }

        return FALSE;                                                                   // Return false if no match was found.
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
        if(!$this->set_url($isbn)) { $this->errors = App::resolve('errors')->getError('isbn', 'no-request'); }
        
        /* If a request URL was set, i attempt to request data from the Google API. */
        $request = $this->request_data();

        /* If the request failed, return the correct error string. */
        if(!$request || isset($this->errors)) {
            if(!isset($this->error)) {
                return App::resolve('errors')->getError('isbn', 'no-request');
            }

            return $this->errors;
        }

        /* Check the requested data, for how many items there are, return errors if 0 items are found. */
        $check = $this->check_data();

        if($check === 0) {
            return $this->errors;
        }

        /* If more then 1 item was found & there titles where stored, */
        if($check === 2 && $this->get_titles()) {
            /* check if the items are in the currently selected series, */
            $iCheck = $this->check_items($reeks);

            /* if the check failed or the user is a Administrator, */
            if(!is_array($iCheck) || $admin) {
                /* if the user is a regular user, i return a no-match error. */
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

        return $item;
    }
}
?>