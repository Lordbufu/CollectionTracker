<?php
// test class for getting book info using the ISBN,
// this uses the Google API to querry the ISBN,
// it has some potentially usefull information,
// but the majority is not info that i store.

// register class to App namespace
namespace App\Core;

// use the app namespace
use App\Core\App;
use DateTime;

class Isbn {
    // Define base URL for books.
    protected $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:";
    // Create empty array to store data
    protected $new= [];

    // Set url function.
    protected function set_url( $isbn ) {
        $this->url = $this->url . $isbn;
    }

    // TODO: Figure out how to deal with multiple items, if that can even happen at all.
    // TODO: Figure out what to do with the left over data, maybe the clients wants a preview first, or can use the extra info.
    // get url function for testing.
    public function get_data( $isbn ) {
        // Combine the isbn code and url.
        if( isset( $isbn ) ) { $this->set_url( $isbn ); }

        // Parsing the google api using the stored url.
        $page = file_get_contents( $this->url );
        $data = json_decode( $page, true );
        
        //die( var_dump( print_r( $data ) ) );

        // Storage for processing the google api information.
        $info = [];
        $temp = [];

        // Store usefull data from the parsed url.
        foreach( $data as $key => $value ) {
            // Single item
            if( $key === "totalItems" && $value === 1 ) {
                $temp = $data["items"][0];
            // Several items ?
            } else if( $key === "totalItems" && $value < 1 ) {
                $info = $data["items"];
            }
        }

        // If something was stored, and thus there was a single item
        if( !empty( $temp ) ) {
            foreach( $temp as $key => $value ) {
                if( $key === "volumeInfo" ) {
                    foreach( $value as $iKey => $iValue ) {
                        if( $iKey === "title" ) {                            // Album Naam
                            $this->new["album-naam"] = $iValue;
                        } elseif( $iKey === "authors" ) {                    // Album schrijver evt voor later
                            $this->new["album-authors"] = $iValue[0];
                        } elseif( $iKey === "publishedDate" ) {              // Album Uitgave datum
                            $temp = strtotime( explode("T", $iValue)[0] );  // Convert to time string
                            $this->new["album-uitgDatum"] = date("Y-m-d", $temp );  // Get date info we are going to use
                        } elseif( $iKey === "description" ) {                // Album Opmerking
                            //$this->new["album-opm"] = $iValue;
                        } elseif( $iKey === "printType" ) {                  // Album Type wat ook lijkt op Categorie voor later.
                            $this->new["album-type"] = $iValue;
                        } elseif( $iKey === "categories" ) {                 // Album Categorie evt voor later
                            $this->new["album-categories"] = $iValue[0];
                        } elseif( $iKey === "imageLinks" ) {                 // Album-cover image
                            $imageType = get_headers( $iValue["smallThumbnail"], 1 )["Content-Type"];   // Get image type
                            $base64Image = "data:" . $imageType . ";charset=utf8;base64," . base64_encode( file_get_contents( $iValue["smallThumbnail"] ) );    // create blob string for the database
                            $this->new["album-cover"] = $base64Image;
                        } elseif( $iKey === "previewLink" ) {                // Album preview, geen idee maar kan wellicht handig zijn ?
                            $this->new["album-preview"] = $iValue;
                        }
                    }
                // Sort desciption is located seperately, basically the first line/sentance of the desciption is seems.
                } elseif( $key === "searchInfo" ) { $this->new["album-opm"] = $value["textSnippet"]; }
            }
        }

        // Add the isbn that was passed for the search, instead of pulling it from the request, to prevent confusing the user.
        $this->new["album-isbn"] = $isbn;

        // return new info
        return $this->new;
    }
}
?>