<?php

namespace App\Core;

use Exception;

/*  Series class explained:
        This class handles all serie related actions, so we can keep the controllers somewhat nice and clean.
        Errors are dealt with via try/catch/throw statements, so any feedback to users, has to be done in the controller.

        Below a few short examples of how to use this:
            #count serie albums: The countAlbums() function.
                To count albums from all series:
                    $this->countAbl

            # get/load series: The getSeries() and loadSeries() function.
                To load all series:
                    App::get("series")->getSeries()
                To load a specific serie (more then 2 id pairs returns a exception):
                    App::get("series")->getSeries( [ {db_col_name} => {value}, {db_col_name} => {value} ] )

            # get serie attribute: The getSerAtt() & loadSeries() function.
                The default syntax:
                    App::get("series")->getSerAtt( {db_col_name}, [ {db_col_name} => {value} ], [ {db_col_name} => {value} ] )
                To get a serie index, from a serie name, ommiting the optional 2nd id:
                    App::get("series")->getSerAtt( "Serie_Index", [ "Serie_Naam" => "Test Serie" ] )

            # set or update a serie: the setSerie() function.
                To set a serie:
                    App::get("series")->setSerie( $serieData )
                To update a serie:
                    App::get("series")->setSerie( $serieData, [ "Serie_Index" => "{index_value}" ] )

            # delete a serie: the delSerie() function.
                To delete a album:
                    App::get("series")->delAlbum( [ "Serie_index" => "{index_value}", "{db_col_name}" => "{value}" ] )
 */
class Series {
    protected $series;

    /*  loadSeries($id):
            This function deals with all loading events, both without a id, as with no more then 2 id sets.
                $id (Array)   - Optional identifier to request DB entries
            
            Return Value: None
     */
    protected function loadSeries( $id=null ) {
        /* Ensure the class global $series is empty before loading */
        if( !empty( $this->series ) || !isset( $this->series ) ) {
            unset( $this->series );
        }

        /* If no id was set, i get all serie data */
        if( empty( $id ) ) {
            $this->series = App::get( "database" )->selectAll( "series" );
        /* If 2 or less pairs are set, i get serie based on those identifiers */
        } elseif( count( $id ) <= 2 ) {
            $this->series = App::get( "database" )->selectAllWhere( "series", $id );
        /* If the id has more then 2 identifier pairs, i throw a id error */
        } elseif( count( $id ) >= 3 ) {
            throw new Exception( App::get( "errors" )->getError( "idToBig" ) );
        /* This is likely never reached, but just in case i give a default load error here. */
        } else {
            throw new Exception( App::get( "errors" )->getError( "load" ) );
        }

        /* If the class global is a string, i need to throw a DB error */
        if( is_string( $this->series ) ) {
            throw new Exception( App::get( "errors" )->getError( "dbFail" ) );
        } else {
            $this->countAlb();
            return TRUE;
        }
    }

    /*  countAlbums():
            We use the database to count the number of albums in a series, and store it in the global.
            So it can be displayed, each time loadSeries() is called, the number is updated.
     */
    protected function countAlb() {
        foreach( $this->series as $key => $value ) {
            $this->series[$key]["Album_Aantal"] = App::get( "database" )->countAlbums( $value["Serie_Index"] );
        }

        return;
    }

    /*  getSeries($id):
            This function is designed to get, all series, or [a] specific serie(s).
                $id (Array)   - Optional identifier to request specific DB entries
            
            Return Value:
                On success: Array
                On failure: String
     */
    public function getSeries( $id=null ) {
        try {
            /* If no series are loaded, and a id was passed, load using the id */
            if( !empty( $id ) ) {
                $this->loadSeries( $id );
            /* If no series are loaded, and no id was passed, load all series*/
            } else {
                $this->loadSeries();
            } 

            /* If not set for some odd reason, throw a exception */
            if( !isset( $this->series ) || empty( $this->series ) ) {
                throw new Exception( App::get( "errors" )->getError( "noItems" ) );
            /* else return all loaded series */
            } else {
                return $this->series;
            }

        /* Catch and return any exceptions */
        } catch( Exception $e ) { return [ "error" => [ "fetchResponse" => $e->getMessage() ] ]; }
    }

    /*  setSerie($data, $id):
            This function can insert and update serie data, based on the optional $id parameter.
            It will also check for duplicate names, using the getSerAtt() function.
                $data (Array)           - The serie data represented in a Associative Array
                $id (Array)             - The id of the serie that needs to be updated
                $nameCheck (String/Int) - Temp variable to check for duplicate names, using the getAlbAtt() function
                $store (String/Bool)    - Temp variable to store the outcome of the database action
            
            Return Value:
                On failure - String
                On success - Boolean
     */
    public function setSerie( $data, $id=null ) {
        try {
            /* If the id is empty, */
            if( empty( $id ) ) {
                /* i need to do a duplicate name check first */
                $nameCheck = $this->getSerAtt( "Serie_Index", [ "Serie_Naam" => $data["Serie_Naam" ] ] );

                /* If the $nameCheck is a int value, we have a duplicate name, and return a error for that */
                if( is_int( $nameCheck ) ) {
                    throw new Exception( App::get( "errors" )->getError( "dupl" ) );
                /* In all other cases, i can simply add the album, any errors in getAlbAtt() are void/irrelevant */
                } else {
                    $store = App::get( "database" )->insert( "series", $data );
                }
            /* If a id was set, i can just update the object using said id */
            } else {
                /* If there are more then 3 id pairs, there needs to be a exception */
                if( count( $id ) >= 3 ) {
                    throw new Exception( App::get( "errors" )->getError( "idToBig" ) );
                /* If all is good, we update the database entry */
                } else {
                    $store = App::get( "database" )->update( "series", $data, $id );
                }
            }

            /* If either the insert or update returned an error, i return a generic error, else i return TRUE */
            return is_string( $store ) ? throw new Exception( App::get( "errors" )->getError( "db" ) ) : TRUE;
        } catch( Exception $e ) { return [ "error" => [ "fetchResponse" => $e->getMessage() ] ]; }
    }

    /*  delSerie($id):
            This function deals with all delete requests, database errors are replaced with a generic error.
                $id (Array)             - The id('s) associated with said item, can support up to 2 id pairs
                $store (Array/String)   - The temp store to evaluate the execution of the query
            
            Return Value:
                On failure   -> Array
                On success   -> Boolean
     */
    public function delSerie( $id ) {
        try {
            /* If there are more then 3 id pairs, there needs to be a exception */
            if( count( $id ) >= 3 ) {
                throw new Exception( App::get( "errors" )->getError( "idToBig" ) );
            /* If all is good, we update the database entry */
            } else {
                $store = App::get( "database" )->remove( "series", $id );
            }

            /* Throw exception if database returned a error string, or TRUE  if not */
            return is_string( $store ) ? throw new Exception( App::get( "errors" )->getError( "db" ) ) : TRUE;
        } catch( Exception $e ) { return [ "error" => [ "fetchResponse" => $e->getMessage() ] ]; }
    }

    /*  getSerAtt( $name, $id ):
            This is basically a extended version, of a getId() function, but then for any attribute from a serie object.
                $name (String)  - The name of the database column we want to request
                $id (Array)     - The identifier we have of the object (Expecting)
                $key (String)   - The $id index as string value, so i can compare it to indexes in the itteration loops
            
            Return Value:
                On failure - Array
                On success - String
     */
    public function getSerAtt( $name, $id ) {
        try {
            /* Get the array key for the identifier 1 */
            $key = implode( array_keys( array_slice( $id, 0, 1 ) ) );

            /* Load all series */
            if( $this->loadSeries() ) {
                /* Loop over the serie data, with a inner/outer loop */
                foreach( $this->series as $oKey => $oValue ) {
                    foreach( $oValue as $iKey => $iValue ) {
                        if( $iKey == $key ) {
                            if( str_replace( " ", "", $id[$key] ) == str_replace( " ", "", $iValue ) ) {
                                return $this->series[$oKey][$name];
                            }
                        }
                    }
                }

                /* Throw exception message, if no match was found */
                throw new Exception( App::get("errors")->getError( "attr" ) );
            } else {
                /* Throw exception message, if no items are loaded */
                throw new Exception( App::get( "errors" )->getError( "noItems" ) );
            }
        /* Handle any exception messages during this process */
        } catch( Exception $e ) { return [ "error" => [ "fetchResponse" => $e->getMessage() ] ]; }
    }

    /* SerChDup($name, $id):
        Fairly straight forward duplicate name check, returning a true of false.
            $name      (String)     - The name that needs to be checked
            $id        (String)     - The index of the serie that is being edited
            $duplicate (Boolean)    - A store to set when comparing inside the loop
        
        Return Value:
            On failure - Array
            On Success - Boolean
     */
    public function SerChDup( $name, $id=null ) {
        try {
            $duplicate;

            /* (Re-)Load series */
            if( $this->loadSeries() ) {
                /* Loop over all series */
                foreach( $this->series as $oKey => $oValue) {
                    /* Loop over each serie */
                    foreach( $oValue as $iKey => $iValue ) {
                        /* Stop when key is Serie_Naam */
                        if( $iKey == "Serie_Naam" ) {
                            /* Compare Serie_Naam value with $name, set duplicate if its a match */
                            if( str_replace( " ", "", $iValue ) == str_replace( " ", "", $name ) ) {
                                $duplicate = TRUE;

                                /* Check if the provided id, is the id of the item that was checked, then if duplicate is set unset it */
                                if( !empty( $id ) && $oValue["Serie_Index"] == $id ) {
                                    if( isset( $duplicate ) ) {
                                        unset( $duplicate );
                                    }
                                }
                            }
                        }
                    }
                }
            }

            /* If duplicate is not set return FALSE, else return error */
            if( !isset( $duplicate ) ) {
                return FALSE;
            } else {
                throw new Exception( App::get( "errors" )->getError( "dupl" ) );
            }
        } catch( Exception $e ) { return [ "error" => [ "fetchResponse" => $e->getMessage() ] ]; }
    }

}

?>