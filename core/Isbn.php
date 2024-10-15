<?php
/*
    test class for getting book info using the ISBN,
    this uses the Google API to querry the ISBN,
    it has some potentially usefull information,
    but the majority is not info that i store.
 */

//  TODO/Notes:
//      - Test what happens if a request returns more then 1 item, example isbn = 0123456789.
//      - Review what the max results is that i want to handle, the example isbn could just be a worst case scenario.
//      - Review how i handle the search/request, atm im just pre-filling data that i have, but maybe the 'client' wants something else ?¿
//      - Clean up comments and left over code from the testing phase.

namespace App\Core;

use App\Core\App;

class Isbn {
    /* Define the Google API base url, and make a new empty data array to store the requested data when processed. */
    protected $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:";
    protected $new= [];

    /* set_url($isbn): This function simply adds the isbn value, at the end of the url string, so we can request data from the Google API. */
    protected function set_url( $isbn ) {
        $this->url = $this->url . $isbn;
        // test code to see if i can get all results back.
        $this->url = $this->url . "&startIndex=0&maxResults=40";
    }

    /*  get_data($isbn, $index=null):
            This function uses a isbn from either a user or scanned input, and parses the google API to find related data.
            If there is related data, that data is then checked and parsed for usefull things, and stored in the global new data array.
                $isbn (string)                  - The isbn code from the user, or the build-in scan function.
                $index (string)                 - A optional parameter, that can store the serie index, when there a several choices to pick from.
                $page (string)                  - The Google API data converted to a string.
                $data (Multi dimentional Array) - The string data converted via Json into easier to process data for PhP.
                $temp (Multi dimentional Array) - The temp storage for the parsed data, to evaluated if we need to process the data.
            
            Return Value: Assoc Array.
     */
    public function get_data( $isbn, $index=null, $name=null ) {
        /* Combine the isbn code and url. */
        if( isset( $isbn ) ) {
            $this->set_url( $isbn );
        }

        /* Parsing the google api using the combined url. */
        $page = file_get_contents( $this->url );
        $data = json_decode( $page, true );

        /* A temp data array, so i can easily detect how many items there are parsed. */
        $temp = [];
        $tempNames = [];

        //die( var_dump( $name ) );
        /* If a name is passed, i need to pick a item from several results, and i need to do that here */

        /* Check how many items where parsed, more then 1 items is currenctly not supported. */
        if( $data["totalItems"] === 1 ) {
            $temp = $data["items"][0];
        } elseif( $data["totalItems"] === 0 ) {
            $this->new["error"] = "No items found !!";
        // This loop is W.I.P.: For more then 1 entry, i need something to show the user, so they can pick what they want to use.
        } elseif( $data["totalItems"] > 1 && $name === null ) {
            // Store all entries
            $temp = $data["items"];

            // Store something that i can use to detect the need for a user choice.
            array_push( $tempNames, "Titles" );
            array_push( $tempNames, $isbn );

            // If a serie index was passed along with this function, i need to store/re-use that.
            if( $index != null ) { array_push( $tempNames, $index ); }

            // Loop over all items, and store there title to display to the user.
            foreach( $temp as $key => $item ) {
                array_push( $tempNames, $temp[$key]["volumeInfo"]["title"] );
            }

            // Return all the titles for the user to pick from.
            return $tempNames;
            
        // If a name was set, the user made a choice, and i need to present that to the user.
        } elseif( $data["totalItems"] > 1 && $name != null ) {
            // remove white space from the title,
            $tempName_1 = str_replace( " ", "", $name );
            // loop over all items from said isbn again,
            foreach($data["items"] as $key => $value) {
                // store the name of each item, with the whitespace removed,
                $tempName_2 = str_replace( " ", "", $value["volumeInfo"]["title"] );
                // check if the names are equal
                if( $tempName_1 === $tempName_2 ) {
                    // store the item that matches as $temp item for processing.
                    $temp = $value;
                }
            }

        }

        /* Check if something was stored, loop over the data to parse it into the new data array. */
        if( !empty( $temp ) && !isset($this->new["error"]) ) {
            foreach( $temp as $key => $value ) {
                if( $key === "volumeInfo" ) {
                    foreach( $value as $iKey => $iValue ) {
                        /* The album-naam, is the parsed title. */
                        if( $iKey === "title" ) { $this->new["Album_Naam"] = $iValue; }

                        /* The publishedDate need to be parsed/converted again, to match the HTML format. */
                        if( $iKey === "publishedDate" ) {
                            $temp = strtotime( explode("T", $iValue)[0] );
                            $this->new["Album_UitgDatum"] = date("Y-m-d", $temp );
                        }

                        /* The cover can be parsed via the imageLinks, and is stored as a base64 blob */
                        if( $iKey === "imageLinks" ) {
                            $imageType = get_headers( $iValue["smallThumbnail"], 1 )["Content-Type"];
                            $base64Image = "data:" . $imageType . ";charset=utf8;base64," . base64_encode( file_get_contents( $iValue["smallThumbnail"] ) );
                            $this->new["Album_Cover"] = $base64Image;
                        }

                        /* For the isbn/ean, i need a bit more logic, as its nested inside the industryIdentifiers */
                        if( $iKey === "industryIdentifiers" ) {
                            /* If there is only 1 parsed result, check the type of code first */
                            if( count( $iValue ) == 1) {
                                if( $iValue[0]["type"] !== "ISBN_10" || $iValue[0]["type"] !== "ISBN_13" ) {
                                    /* If the codes match, store the parsed one. */
                                    if( $iValue[0]["identifier"] == $isbn ) {
                                        $this->new["Album_ISBN"] = $iValue[0]["identifier"];
                                    }
                                }

                            /* If there are more, attempt to find the correct one. */
                            } else {
                                for( $i=0; $i < 2; $i++) {
                                    if( $iValue[$i]["type"] == "ISBN_10" || $iValue[$i]["type"] == "ISBN_13" ) {
                                        $x = $i + 1;

                                        if( isset( $iValue[$x]["type"] ) ) {
                                            if( $iValue[$x]["identifier"] !== $isbn ) {
                                                $this->new["Album_ISBN"] = $iValue[$x]["identifier"];
                                            }

                                        } else {
                                            if( $iValue[$i]["identifier"] !== $isbn ) {
                                                $this->new["Album_ISBN"] = $iValue[$i]["identifier"];
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        /* Retained for potential futhure usecases
                            // The book author, currently not used, but might be usefull going to need feedback for this:
                            if( $iKey === "authors" ) { $this->new["album-authors"] = $iValue[0]; }
                            // The long format description, sometimes as long as the text on the back cover, so not very useable imo:
                            if( $iKey === "description" ) { $this->new["album-opm"] = $iValue; }
                            // The print type is similar to a categorie, so leaving this in just in case:
                            if( $iKey === "printType" ) { $this->new["album-type"] = $iValue; }
                            // There is a categorie, that i might be able to use in somekind of way later:
                            if( $iKey === "categories" ) { $this->new["album-categories"] = $iValue[0]; }
                            // Preview link, might be usefull later in some way ?:
                            if( $iKey === "previewLink" ) { $this->new["album-preview"] = $iValue; }
                         */

                    }
                /* The short description is stored outside of the volumeInfo  */
                } else if( $key === "searchInfo" ) {
                    $this->new["Album_Opm"] = $value["textSnippet"];
                }
            }
        }

        /* If there was no valid isbn found, we simply use the function global one that was used to search the API (likely never happens). */
        if( !isset( $this->new["Album_ISBN"] ) ) {
            $this->new["Album_ISBN"] = $isbn;
        }

        return $this->new;
    }
}
?>