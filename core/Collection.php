<?php
namespace App\Core;

use App\Core\App, Exception;

//  TODO: Go over all function and inline comments, check if there still up to date/accurate.
class Collection {
    /* Global data storage */
    protected $albums;
    protected $series;
    protected $collections;

    /* Global generic errors */
    protected $dupError = [ "fetchResponse" => "Deze naam bestaat al, gebruik een andere naam gebruiken !" ];
    protected $dupColl = [ "fetchResponse" => "Dit album is al deel van uw collectie !" ];
    protected $dbError = [ "fetchResponse" => "Er was een database error, neem contact op met de administrator als dit blijft gebeuren!" ];

    /* Protected internal functions */
    /*  getSetId($name):
            Get serie index based on serie name.

            $name (String)  : The name of the series we want the index for.

            Return Value    : INT
     */
    protected function getSerId( $name ) {
        $tempSerie = App::get( "database" )->selectAllWhere( "series", [ "Serie_Naam" => $name ] )[0];
        return $tempSerie["Serie_Index"];
    }

    /*  countAlbums():
            We use the database to count the number of albums in a series, and store it in the global.
            So it can be displayed, each time getSeries is called, the number is updated.
     */
    protected function countAlbums() {
        foreach( $this->series as $key => $value ) { $this->series[$key]["Album_Aantal"] = App::get( "database" )->countAlbums( $value["Serie_Index"] ); }
        return;
    }

    /* Generic functions to reduce item specific functions */
    /*  getItemName($type, $id1, $id2=null):
            This function gets all name requests, depending on the type that was passed.
                $type   - The type name of the item we want to get the name of (album/serie).
                $id1    - The serie id of either the serie where the albums is in, or the serie we want the name of.
                $id2    - The album id that we want the name of, defaulting to null so it doesnt have to be set.
            
            Return Value = String.
     */
    public function getItemName( $type, $id1, $id2 = null ) {
        switch( $type ) {
            case "serie":
                if( !isset( $this->series ) ) { $this->getSeries(); }
                foreach( $this->series as $index => $serie ) { if( $id1 == $serie["Serie_Index"] ) { return $serie["Serie_Naam"]; } }
                return $this->$dbError;
            case "album":
                if(! isset( $this->albums ) ) { $this->getAlbums( $id1 ); }
                foreach( $this->albums as $index => $album ) { if( $id2 == $album["Album_Index"] ) { return $album["Album_Naam"]; } }
                return $this->$dbError;
        }
    }

    /*  remItem($table, $id):
            This function deals with all delete/remove requests.
                $table  - The database table where items need to be removed from.
                $id     - The id's associated with said item.
                $store  - The temp store to evaluate the execution of the query.
            
            Return Value:
                On sucess   -> Boolean
                On fail     -> Assoc Array.
     */
    public function remItem( $table, $id ) {
        $store = App::get( "database" )->remove( $table, $id );
        return is_string( $store ) ? $this->dbError : TRUE;
    }

    /*  checkItemName($type, $name, $index=null):
            Function to check item names on request, to prevent duplicate DB entries during various actions.
            The check is done after removing all whitespace from the strings, to detect more identical entry cases.
                $type (String)      - The type of the request, almost identical to the DB table name.
                $name (String)      - The name of item that needs to be checked.
                $sIndex (String)    - The series index number (optional).
                $aIndex (String)    - The albums index number (optional).

            Return Value:
               If not duplicate -> Boolean.
               If is duplicate  -> Assoc Array.
     */
    public function checkItemName( $type, $name, $sIndex = null, $aIndex = null ) {
        switch( $type ) {
            case "serie":
                if( !isset( $this->series ) ) { $this->getSeries(); }
                foreach( $this->series as $index => $serie ) {
                    if( str_replace( " ", "", $name ) === str_replace( " ", "", $serie["Serie_Naam"] ) ) {
                        if( $sIndex !== null && $sIndex != $serie["Serie_Index"] ) {
                            return $this->dupError;
                        } else if ( $sIndex === null ) { return $this->dupError; }
                    }
                }
                return FALSE;
            case "album":
                if( !isset( $this->albums ) ) { $this->getAlbums( $sIndex ); }
                foreach( $this->albums as $index => $album ) {
                    if( str_replace( " ", "", $album["Album_Naam"] ) === str_replace( " ", "", $name ) ) {
                        if( $aIndex !== null && $aIndex != $album["Album_Index"] ) {
                            return $this->dupError;
                        } else if ( $aIndex === null ) { return $this->dupError; }
                    }
                }
                return FALSE;
        }
    }

    /* Series Functions */
    /*  getSeries():
            Simple get all series from DB, add a album count to each serie, and return them all to the caller.

            Return Value: Multi-Dimensional Array.
     */
    public function getSeries() {
        $this->series = App::get( "database" )->selectAll( "series" );
        $this->countAlbums();
        return $this->series;
    }

    /*  getSerInd($name):
            This function takes a serie name, and finds the matching serie index.
            Before i compare the names, i need to remove any whitespaces, so whitespaces are not compared.

            $name (String)  : The name of the serie.

            Return Value    : INT
     */
    public function getSerInd( $name ) {
        $temp_name_1 = str_replace( " ", "", $name );

        if(!isset($this->series)) {
            $this->getSeries();
        }

        foreach( $this->series as $index => $serie ) {
            $temp_name_2 = str_replace( " ", "", $serie["Serie_Naam"] );

            if( $name == $serie["Serie_Naam"] ) {
                return $serie["Serie_Index"];
            }
        }
    }

    /*  setSerie($data, update=null):
            This functions set the a serie in the database, all filtering etc is done in the controller.
            And now also deals with updating series, simply indicated by the optional parameter.
                $data (Assoc Array) : The data that needs to be stored.
                $update (string)    : The tag for is the request was an update request (Optional).

            Return Type:
                On sucess   -> Boolean
                On fail     -> String (the database error)
     */
    public function setSerie( $data, $update = null ) {
        if( $update === null ) {
            $store = App::get( "database" )->insert( "series", $data );
        } else { $store = App::get( "database" )->update( "series", $data, [ "Serie_Index" => $update ] ); }
        return is_string( $store ) ? $this->dbError : TRUE;
    }

    /* Album Functions */
    /*  getAlbums($partId):
            This function gets all albums from a series, based on a serie name or index.
                $partId (String or Int)  - Can both take a serie name or index value, to get the associciated albums.

            Return Value: Multi-Dimensional Array.
     */
    public function getAlbums( $partId ) {
        if( !is_numeric( $partId ) ) {
            $this->albums = App::get( "database" )->selectAllWhere( "albums", [ "Album_Serie" => $this->getSerId($partId) ] );
            return $this->albums;
        } else {
            $this->albums = App::get( "database" )->selectAllWhere( "albums", [ "Album_Serie" => $partId ] );
            return $this->albums;
        }
    }

    /*  getAlbId($name):
            Get serie index based on album name.
                $name (String)  : The name of the album we want the index for.

            Return Value: INT
     */
    public function getAlbId( $name ) {
        $tempAlbum = App::get( "database" )->selectAllWhere( "albums", [ "Album_Naam" => $name ] )[0];
        return $tempAlbum["Album_Index"];
    }

    /*  setAlbum($data, update=null):
            This function either adds or updates the album database, based on the $update parameter.
                $data (Assoc Array) : The Album data that needs to be stored.
                $update ()          : A trigger parameter, to see if its a update or add request (optional).
                $store (Bool/String): The result of the database querry.

            Return types:
                On-success  : Boolean
                On-failure  : String
     */
    public function setAlbum( $data, $update = null ) {
        if($update === null) {
            $store = App::get("database")->insert( "albums", $data );
        } else { $store = App::get("database")->update( "albums", $data, $update ); }
        return is_string($store) ? $this->dbError : TRUE;
    }

    /* Collectie Functions */
    //  TODO: !is_string seems to not be correct for calls im sending here, might want to replace it with !is_array ?¿
        // This needs more testing.
    /*  getColl($table, $userId):
            Get collection for a specific user from the database.
                $table (string) - The db table, so i can just pass that along from other functions, even though its always from collections xD
                $userId (int)   - User id from the session data.
            
            Return Type: Multi-Dimensional Array.
     */
    public function getColl( $table, $userId ) {
        /* Set the correct id format, depending on the provided input. */
        if( !is_string( $userId ) ) {
            if( !is_array( $userId ) ) {
                $id = [ "Gebr_Index" => $userId ];
            } else { $id = $userId; }
        } else { $id = [ "Gebr_Index" => $userId ]; }
        /* Set the requested collection data, and return to caller. */
        $this->collections = App::get( "database" )->selectAllWhere( $table, $id );
        return $this->collections;
    }

    /*  setColl($table, $data):
            Function to set collection data, so the user can add items to a collection.
                $table (String)             : The table name that i want to update, witch is always collections in this case.
                $data (Associative Array)   : The data required to make a collection database entry.

            Return Value: boolean.
     */
    public function setColl( $table, $data ) {
        /* Ensure the most recent user collection data is set. */
        $this->collections = $this->getColl( $table, [ "Gebr_Index" => $data["Gebr_Index"] ] );

        /* Removed for now, as having a duplicate detection isnt very usefull anymore.
            // Loop over the collections, to ensure its not a duplicate entry.
            // foreach($this->collections as $key => $value) {
            //     if($value["Alb_Index"] == $data["Alb_Index"]) {
            //         return $this->dupColl;
            //     }
            // }
         */

        /* Prepare the required data, that isnt included in the App and POST data */
        $data["Alb_Staat"] = "";
        $data["Alb_Aantal"] = 1;
        $data["Alb_Opmerk"] = "";
        $data["Alb_DatumVerkr"] = date( "Y-m-d" );

        /* Attempt to store the collection in, and return the results to the caller  */
        $store = App::get( "database" )->insert( $table, $data );

        return is_string( $store ) ? $this->dbError : TRUE;
    }

    //  TODO: Finish the comments, and update it to the changes made after the initial draft.
    /*  evalColl($fData, $sIndex, $uIndex):
            This function, evaluates if data fetched from the Google API, is set part of a collection or not.
                $fData  (Assoc Array)   - The data that was fetched from the Google API, via the Isbn class.
                $sIndex (Int)           - The index of the serie, where the barcode was scanned for.
                $uIndex (Int)           - The index of the user that scanned the barcode.
            
            Return Value: Boolean.
     */
    public function evalColl( $fData, $sIndex, $uIndex ) {
        $tAlbums = $this->getAlbums( $sIndex );
        $tColl = $this->getColl( "collecties", $uIndex );
        $match;
        /* Loop over all stored albums */
        foreach( $tAlbums as $oKey => $oValue ) {
            /* Loop over every item in a album */
            foreach( $oValue as $iKey => $iValue ) {
                /* If the key is Album_ISBN, and it matches the scanned ISBN */
                if( $iKey === "Album_ISBN" && $iValue === $fData["album-isbn"] ) {
                    /* Store said album data in a temp variable, and set a flag that it is. */
                    $tempFetch = $oValue;
                    $match = [ "inSerie" => TRUE ];
                /* Ensure $match wasnt set already, and set a flag that the album isnt in the current serie. */
                } else if ( !isset( $match ) ) { $match = [ "inSerie" => FALSE ]; }
            }
        }
        /* Check if album was in the selected serie, but no collection data was found. */
        if( $match["inSerie"] && empty( $tColl ) ) {
            // This means album has to be added to the user collection.
            $match = [ "addToColl" => TRUE ];
        /* If it is in the Serie, we check if its part of the collection already. */
        } else if( $match["inSerie"] ) {
            /* Loop over the current collection data */
            foreach( $tColl as $oKey => $oValue ) {
                /* Loop over every entry in said collection data */
                foreach( $oValue as $iKey => $iValue ) {
                    /* Check if the album index values match, only logical action, would be to remove the album from the collection. */
                    if( $iKey == "Alb_Index" && $tempFetch["Album_Index"] == $iValue ) { $match = [ "remFromColl" => TRUE ]; }
                }
            }
        }
        return $match;
    }
}

?>