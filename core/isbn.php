<?php
// test class for getting book info using the ISBN,
// this uses the Google API to querry the ISBN,
// it has some potentially usefull information,
// but the majority is not info that is stored.

// register class to App namespace
namespace App\Core;

// use the app namespace
use App\Core\App;

class Isbn {
    // Define base URL for books.
    protected $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:";
    // Create empty array to store data
    protected $new= [];

    // Set url function.
    public function set_url($isbn) {
        $this->url = $this->url . $isbn;
    }

    // TODO: Figure out how to deal with multiple items.
    // TODO: Store useable data in the correct format, so it can be stored in the database.
    // get url function for testing.
    public function get_data() {
        // Parsing the google api using the stored url.
        $page = file_get_contents($this->url);
        $data = json_decode($page, true);

        // Storage for processing the google api information.
        $info = [];
        $temp = [];

        // Store usefull data from the parsed url.
        foreach($data as $key => $value) {
            // Single item
            if($key === 'totalItems' && $value === 1) {
                $temp = $data['items'][0]['volumeInfo'];
            // Several items ?
            } else if ($key === 'totalItems' && $value < 1) {
                $info = $data['items'];
            }
        }

        // If something was stored, and thus there was a single item
        if(!empty($temp)) {
            // Store all relevant items of the the 
            foreach($temp as $key => $value) {
                if($key === "title") {
                    $this->new['title'] = $value;
                } elseif($key === "authors") {
                    $this->new['authors'] = $value[0];
                } elseif($key === "publishedDate") {
                    $this->new['publishedDate'] = $value;
                } elseif($key === "description") {
                    $this->new['description'] = $value;
                } elseif($key === "printType") {
                    $this->new['type'] = $value;
                } elseif($key === "categories") {
                    $this->new['categories'] = $value;
                } elseif($key === "imageLinks") {
                    $this->new['cover'] = $value['thumbnail'];
                } elseif($key === "previewLink") {
                    $this->new['preview'] = $value;
                }
            }
        }

        // return new info
        return $this->new;
    }
}

?>