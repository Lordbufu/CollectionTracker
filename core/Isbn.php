<?php
/*  Isbn Class:
        This class uses the free to use Google books API, to search for items based on there ISBN\EAN number.
        I have opted to allow a max results of 40, if it finds more then 1 item for the same ISBN.
        It's unlikely that it will ever find more then 40, but i also do not have a large sample size to test this on.

        The data is parsed and then processed to be presented to the user, either in the correct pop-in, or a new one with choices between items.
        Once a choice between items is made, i can use this same class to get the correct item, based on the item names that i had returned.

        variables:
            $url
            $new
            $temp

        functions:
            set_url($isbn)                  - Set the url with the provided isbn, so we can make a request.
            check_data($data)               - 
            prep_item($isbn)                - 
            get_data($isbn, $index, $name)  - Parse and present the data to the user.
 */

//  TODO/Notes:
//      - Not getting the expected results anymore, seems like google changed something, or im using a method that wasnt intended.
//              So far i have tested the isbn codes i have noted in the LogicController, i may simply need other codes or a larger sample size.
//              Maybe i need to user proper HTTP requests, instead of the 'hack' im using right now.
//              Seems like the issue is solved, by replacing 'isbn:' with 'ISBN:' in the url. ¿
//      - Figure out what todo with several authors on album.

namespace App\Core;

use App\Core\App;

class Isbn {
    /* Define the Google API base url, and make a new empty data array's to store the requested data when processed. */
    protected $url = "https://www.googleapis.com/books/v1/volumes?q=";
    protected $new = [];
    protected $temp = [];

    /*  set_url($isbn):
            This function simply adds the isbn value, at the end of the url string, so we can request data from the Google API.
            It also set a increases the default amount of items that is returned/show, incase there are more result found.
     */
    protected function set_url( $isbn ) {
        $this->url = $this->url . "ISBN:" . $isbn;
        $this->url = $this->url . "&startIndex=0&maxResults=40";
    }

    /*  check_data($data):
            This function processes the parsed data, to see if there was anything usefull in the Google API database.
            And either stores it in the temp array, or it stores a error in the new array

                $data (Assoc Array) - The API data that was requested in get_data()

            Return value: Boolean
     */
    protected function check_data( $data ) {
        /* If there was only 1 item, store that in the $temp array, and return true. */
        if( $data["totalItems"] === 1 ) {
            $this->temp = $data["items"][0];
            return true;
        /* If nothing was found, store a error in the $new array, and return false */
        } elseif( $data["totalItems"] === 0 ) {
            $this->new["error"] = "No items found !!";
            return false;
        /* If more then 1 item was found, store all in the $temp array, and return true. */
        } elseif( $data["totalItems"] > 1 ) {
            $this->temp = $data["items"];
            return true;
        }
    }

    /*  prep_item($isbn):
            Converts the remaining API data, to something that fits my database structure.
            For now some of the potentially usefull data, is uncommented incase i want to use it later.
                $isbn (string)  - The ISBN the user wanted to search data for

            Return value: None
     */
    protected function prep_item( $isbn ) {
        /* Temp store for converting the data into a useable format. */
        $tempDate;

        /* Loop over the item that the user wants to use. */
        foreach( $this->temp as $key => $value ) {

            /* Create a new inner foreach loop for the volumeInfo */
            if( $key === "volumeInfo" ) {
                foreach( $value as $iKey => $iValue ) {

                    /* The album-naam, is the parsed title. */
                    if( $iKey === "title" ) {
                        $this->new["Album_Naam"] = $iValue;
                    }

                    /* Album writer if one is known */
                    if( $iKey === "authors" ) {
                        $this->new["Album_Schrijver"] = $iValue[0];
                    }

                    /* The publishedDate need to be parsed/converted again, to match the HTML format. */
                    if( $iKey === "publishedDate" ) {
                        $tempDate = strtotime( explode("T", $iValue)[0] );
                        $this->new["Album_UitgDatum"] = date("Y-m-d", $tempDate );
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

                    if( $iKey === "description" ) {
                        $this->new["Album_Opm"] = $iValue;
                    }
                }
            /* If not description was found, pick the other potential usefull field (for some reason located outside volumeInfo).  */
            } else if( $key === "searchInfo" && !isset( $this->new["Album_Opm"] ) ) {
                $this->new["Album_Opm"] = $value["textSnippet"];
            }
        }
    }

    /*  get_data($isbn, $index, name):
            This function uses a isbn from either a user or scanned input, and parses the google API to find related data.
            If there is related data, that data is then checked and parsed for usefull things, and stored in the global new data array.
                $isbn (string)      - The isbn code from the user, or the build-in scan function
                $index (string)     - A optional parameter, that can store the serie index, when there a several choices to pick from
                $name (string)      - The title the user picked, when we returned a item choice
                $data (Array)       - File content of the url, converted to string data, and json decoded to make it more workable.
                $tempNames (Array)  - A default array, for storing names from multiple items, that the user has to pick one from
            
            Return Value: Array
     */
    public function get_data( $isbn, $index=null, $name=null ) {
        /* A temp data array, so i can easily detect how many items there are parsed. */
        $tempNames = [];

        /* Combine the isbn code and url. */
        if( isset( $isbn ) ) {
            $this->set_url( $isbn );
        }

        /* Convert data to string, and make array out of that, then pre-process it with the check_data() function */
        $data = json_decode( file_get_contents( $this->url ), true );
        $proc_data = $this->check_data( $data );

        /* If something was found, and it was more then a single item */
        if( $proc_data && isset( $this->temp[0] ) ) {

            /* If a choice hasnt been made yet, i prep and return a array with title choices for the user */
            if( $name === null ) {
                array_push( $tempNames, "Titles" );
                array_push( $tempNames, $isbn );

                if( $index != null ) {
                    array_push( $tempNames, $index );
                }

                foreach( $this->temp as $key => $item ) {
                    array_push( $tempNames, $this->temp[$key]["volumeInfo"]["title"] );
                }

                return $tempNames;
            /* If there was a title choice made, i find the item that was picked, and prep that for further processing */
            } else {
                $tempName_1 = str_replace( " ", "", $name );

                foreach( $data["items"] as $key => $value ) {
                    $tempName_2 = str_replace( " ", "", $value["volumeInfo"]["title"] );

                    if( $tempName_1 === $tempName_2 ) {
                        $this->temp = $value;
                    }
                }
            }
        }

        /* Check if something was stored and there are no errors, prep the data for the user. */
        if( !empty( $this->temp ) && !isset( $this->new["error"] ) ) {
            $this->prep_item( $isbn );

            if( $index != null && !isset( $this->new["Album_Serie"] ) ) {
                $this->new["Album_Serie"] = $index;
            }
        }

        /* If there was no valid isbn found, we simply use the function global one that was used to search the API. */
        if( !isset( $this->new["Album_ISBN"] ) ) {
            $this->new["Album_ISBN"] = $isbn;
        }

        return $this->new;
    }
}
?>