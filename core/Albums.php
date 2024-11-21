<?php

/* TODO List: Review all function and inline comments, to make sure they are up-to-date. */

namespace App\Core;

use Exception;

/*  Album class short explanation:
        This class handles all albums related actions, so we can keep the controllers somewhat nice and clean.
        Errors are dealt with via try/catch/throw statements, so any feedback to users, has to be done in the controller.

        Below a few short examples of how to use this:
            # get/load Albums: The getAlbums() & loadAlbums() function.
                To load all albums:
                    App::get("albums")->getAlbums();
                To load a speccific or set of albums (more then 2 id pairs returns a exception):
                    App::get("albums")->getAlbums( [ {db_col_name} => {value}, {db_col_name} => {value} ] );

            # get Album Attribute: The getAlbAtt() & loadAlbums() function.
                The default syntax:
                    App::get("albums")->getAlbAtt( {db_col_name}, [ {db_col_name} => {value} ], [ {db_col_name} => {value} ] );
                To get a album index, from a album name, within a specific series:
                    App::get("albums")->getAlbAtt( "Album_Index", ["Album_Naam" => "Test Album"], ["Album_Serie" => "63"] );
            
            # set or update album entries: The setAlbums() & loadAlbums() function.
                To set a new album:
                    App::get("albums")->setAlbum($albumData)
                To update a album:
                    App::get("albums")->setAlbum($albumData, [ "Album_Index" => "{index_value}" ] )

            # delete album entries: The delAlbum() function.
                To delete a album:
                    App::get("albums")->delAlbum( [ "Album_index" => "{index_value}", "{db_col_name}" => "{value}" ] )
                
 */
class Albums {
    protected $albums;

    /*  loadAlbums($id):
            This function load the requested data, in the class global $albums variable, with no more then 2 identifier pairs.
            If any exceptions are thrown, they should be caught in the functions using this, PDO exceptions are filtered out with a dbFail error.
                $id (Assoc Array)   - Optional indentifier(s), to narrow down the requested albums.
            
            Return Value:
                On failure - String
                On success - Boolean
     */
    protected function loadAlbums( $id ) {
        /* Ensure the class global $albums is always empty before loading */
        if( !empty( $this->albums )  || !isset( $this->albums ) ) {
            unset( $this->albums );
        }

        /* Normaly albums are requested with atleast 1 identifier, but never more then 2 */
        if( count( $id ) <= 2 ) {
            $this->albums = App::get( "database" )->selectAllWhere( "albums", $id );
        /* If the id has more then 2 identifier pairs, i throw a id error */
        } elseif( count( $id ) >= 3 ) {
            throw new Exception( App::get( "errors" )->getError( "idToBig" ) );
        /* This is likely never reached, but just in case i give a default load error here. */
        } else {
            throw new Exception( App::get( "errors" )->getError( "load" ) );
        }

        /* If the class global is a string, i need to throw a DB error */
        if( is_string( $this->albums ) ) {
            throw new Exception( App::get( "errors" )->getError( "dbFail" ) );
        } else {
            return TRUE;
        }
    }

    // Totally Finished
    /*  getAlbum($id):
            The function attempts to load, and return, the requested album data.
                $id (Assoc Array)   - id pair(s) we want to get, for example a serie id (max 2).
            
            Return Type: Multi-Dimensional Array
     */
    public function getAlbums( $id ) {
        try {
            /* Attempt to load/refresh the album data and return said data, Exception from the load function is passed on on fail */
            if( $this->loadAlbums( $id ) ) {
                return $this->albums;
            } else {
                throw new Exception( App::get( "errors" )->getError( "noItems" ) );
            }
        /* Handle any exception messages during this process */
        } catch( Exception $e ) { return [ "error" => [ "fetchResponse" => $e->getMessage() ] ]; }
    }

    /*  setAlbum($data, $id):
            This function can update and insert album data, based on the optional id parameter.
            It also does a duplicate name check, inside the serie the album is in, using the getAlbAtt() function.
                $data (Assoc Array)     - The album data represented in a Associative Array
                $id (Assoc Array)       - The id of the album that needs to be updated.
                $nameCheck (String/Int) - Temp variable to check for duplicate names, using the getAlbAtt() function.
                $store (String/Bool)    - Temp variable to store the outcome of the database action.
            
            Return Value:
                On failure - Multi-Dimensional Array
                On success - Boolean
     */
    public function setAlbum( $data, $id=null ) {
        try {
            /* If the $id is empty, */
            if( empty( $id ) ) {
                /* i need to do a duplicate name check first, within the same Serie */
                $nameCheck = $this->getAlbAtt( "Album_Index", [ "Album_Naam" => $data["Album_Naam" ] ], [ "Album_Serie" => $data["Album_Serie"] ] );

                /* If the $nameCheck is a int value, we have a duplicate name, and throw a exception for that */
                if( is_int( $nameCheck ) ) {
                    throw new Exception( App::get( "errors" )->getError( "dupl" ) );
                /* In all other cases, i can simply add the album, any errors in getAlbAtt() are void/irrelevant */
                } else {
                    $store = App::get( "database" )->insert( "albums", $data );
                }
            /* If the $id is not empty, we need to update a entry, based on said $id */
            } else {
                /* If there are more then 3 id pairs, there needs to be a exception */
                if( count( $id ) >= 3 ) {
                    throw new Exception( App::get( "errors" )->getError( "idToBig" ) );
                /* If all is good, we update the database entry */
                } else {
                    $store = App::get( "database" )->update( "albums", $data, $id );
                }
            }

            /* If either the insert or update returned an error, i return a generic error, else i return TRUE */
            return !empty( $store ) ? throw new Exception( App::get( "errors" )->getError( "db" ) ) : TRUE;
        /* Handle any exception messages during this process */
        } catch( Exception $e ) { return [ "error" => [ "fetchResponse" => $e->getMessage() ] ]; }
    }

    // Totally Finished
    /*  delAblum($id):
            This function deals with all delete requests, database errors are replaced with a generic error.
                $id    (Array)          - The id('s) associated with said item, can support up to 2 id pairs.
                $store (Bool\String)    - The temp store to evaluate the execution of the query.
            
            Return Value:
                On failure   -> Multi-Dimensional Array
                On success   -> Boolean
     */
    public function delAlbum( $id ) {
        try {
            /* If there are more then 3 id pairs, there needs to be a exception */
            if( count( $id ) >= 3 ) {
                throw new Exception( App::get( "errors" )->getError( "idToBig" ) );
            /* If all is good, we remove the database entry */
            } else {
                $store = App::get( "database" )->remove( "albums", $id );
            }

            return is_string( $store ) ? throw new Exception( App::get( "errors" )->getError( "db" ) ) : TRUE;
        /* Handle any exception messages during this process */
        } catch( Exception $e ) { return [ "error" => [ "fetchResponse" => $e->getMessage() ] ]; }
    }

    // Totally Finished
    /*  getAlbAtt($name, $id_1, $id_2):
            This is basically a extended version, of a getId() function, but then for any attribute from a object.
            The return value when a match is found, depends slightly on the requested attribute.
                $name (String)  - The name of the database column we want the value of.
                $id_1 (Array)   - The identifier of the album that selects the specific album (name, index, isbn).
                $id_2 (Array)   - The identifier for the serie the album is in, intentionally seperated from id_1.
                $key  (String)  - The $id_1 index as string value, so i can compare it to indexes in the itteration loops.
            
            Return Value:
                On failure -> Array
                On success -> What ever the database has stored.
     */
    public function getAlbAtt( $name, $id_1, $id_2=null ) {
        try {
            /* Get the array key for the identifier 1 */
            $key = implode( array_keys( array_slice( $id_1, 0, 1 ) ) );

            /* Make sure the correct album data is loaded */
            if( !empty( $id_2 ) ) {
                if( count( $id_2 ) >= 3 ) {
                    throw new Exception( App::get( "errors" )->getError( "idToBig" ) );
                } else {
                    $this->loadAlbums( $id_2 );
                }
            } else {
                $this->loadAlbums( $id_1  );
            }

            if( isset( $this->albums ) && !empty( $this->albums) ) {
                /* Loop over the multi-dimensional array and thus each stored album */
                foreach( $this->albums as $oKey => $oValue ) {
                    /* Then loop over each attribute in each album */
                    foreach( $oValue as $iKey => $iValue ) {
                        /* Compare the key we are looking for, with the iKey of the current album */
                        if( $iKey == $key ) {
                            /* If both values without whitespace match, i return the requested data */
                            if( str_replace( " ", "", $id_1[$key] ) == str_replace( " ", "", $iValue ) ) {
                                return $this->albums[$oKey][$name];
                            }
                        }
                    }
                }
            } else {
                throw new Exception( App::get( "errors" )->getError( "noItems" ) );
            }

            /* Throw exception message, if no match was found */
            throw new Exception( App::get( "errors" )->getError( "attr" ) );
        /* Handle any exception messages during this process */
        } catch( Exception $e ) { return [ "error" => [ "fetchResponse" => $e->getMessage() ] ]; }
    }

    // Totally Finished
    /*  albChDup($name, $id):
            This function simply checks, if a album name is duplicate with the specfified serie.
                $name (String)      - The name of the album that needs to be checked.
                $sId  (Assoc Array) - The serie index the album is part of.
                $aId  (String)      - The index of the album being edited.
            
            Return Value:
                On failure  - Multi-Dimensional Array
                On success  - Boolean.
     */
    public function albChDup( $name, $sId, $aId=null ) {
        try {
            $duplicate;

            /* Attempt to load the required albums */
            if( $this->loadAlbums( $sId ) ) {
                /* Loop over all loaded albums */
                foreach( $this->albums as $oKey => $oValue) {
                    foreach( $oValue as $iKey => $iValue ) {
                        /* Check if the name equals the name being added/edited, and set duplicate if that is the case */
                        if( $iKey == "Album_Naam" ) {
                            if( $name == $iValue ) {
                                $duplicate = TRUE;
                                /* Check if there is a album id, and it it matches the current items id */
                                if( isset( $aId ) && $oValue["Album_Index"] == $aId ) {
                                    /* Unset duplicate if set, because the user is editing that item */
                                    if( isset( $duplicate ) ) { unset( $duplicate ); }
                                }
                            }
                        }
                    }
                }

                /* If duplicate isnt set after the loops are done, there is no duplicate name, so i return FALSE */
                if( !isset( $duplicate ) ) {
                    return FALSE;
                /* In all other cases, i throw a duplicate error for user feedback */
                } else {
                    throw new Exception(  App::get( "errors" )->getError( "dupl" ) );
                }
            }
        /* Handle any exception messages during this process */
        } catch( Exception $e ) { return [ "error" => [ "fetchResponse" => $e->getMessage() ] ]; }
    }
}

?>