<?php

/*  TODO List:
        - Re-think/-design the load process, so the 0 rows returned update querry exception isnt a issue.
 */

/*  Search Tags:
        - Old code:
            Stuff that was fixed, and can likely be removed after further testing.
 */

namespace App\Core;

use Exception;

/*  Collecties class explained:
        This class handles all collection related actions, so we can keep the controllers somewhat nice and clean.
        Errors are dealt with via try/catch/throw statements, so any feedback to users, has to be done in the controller.

        Below a few short examples of how to use this:
            # get/load collections: The getCol() & loadCol() function.
                To get/load all user collections:
                    App::get("collecties")->getCol( [ "{user-index-column-name}" => "{user-index-value}" ] )

            # change collection data: The getAlbAtt(), loadCol() and changeCol() function.
                This function uses the getAlbAtt() function from the Albums class, to get and return the album/items name for userfeedback.
                To add/remove to/from a collection:
                    App::get("collecties")->changeCol( {processed-collection-form-data} );
 */

class Collecties {
    protected $collections;

    /*  loadCol($id):
            Loads the collection for the requested user id, or re-loads if already set.
                $id (Assoc Array)   - The requested user id, that needs it colletion data loaded.
            
            Return Value: None.
     */
    protected function loadCol( $id ) {
        /* Ensure the class global $collections is empty before loading */
        if( !empty( $this->collections ) || isset( $this->collections ) ) {
            unset( $this->collections );
        }

        /* If no collections are set, load them for the requested user id */
        if( count( $id ) <= 2 ) {
            $this->collections = App::get( "database" )->selectAllWhere( "collecties", $id );
        /* If the id has more then 2 identifier pairs, i throw a id error */
        } elseif( count( $id ) >= 3 ) {
            throw new Exception( App::get( "errors" )->getError( "idToBig" ) );
        /* This is likely never reached, but just in case i give a default load error here. */
        } else {
            throw new Exception( App::get( "errors" )->getError( "load" ) );
        }

        return TRUE;    // Temp fix for a failed thought process.

        // Old code that doesnt work with the 0 rows changed DB error
        /* If the class global is a string, i need to throw a DB error */
        // if( is_string( $this->collections ) ) {
        //     throw new Exception( App::get( "errors" )->getError( "dbFail" ) );
        // } else {
        //     return TRUE;
        // }
    }

    /*  evalCol($id):
            Basically a duplication check, so i know if need to add or remove to/from a collection, based on album index values.
                $id (Assoc Array)   - The index of the album that needs to be checked.

            Return Value: Boolean
     */
    protected function evalCol( $id ) {
        // Temp solution for the 0 rows changed error.
        if( is_string( $this->collections ) ) {
            return FALSE;
        }

        /* Loop over the first set of keys and values */
        foreach( $this->collections as $oKey => $oValue ) {
            /* Loop over the second set of keys and values */
            foreach( $oValue as $iKey => $iValue) {
                /* Compare the album index values, and return TRUE is the match */
                if( $iKey == "Alb_Index" && $iValue == $id["Alb_Index"] ) {
                    return TRUE;
                }
            }
        }

        /* Return false, since no identical data was found */
        return FALSE;
    }

    /*  getCol($id):
            This function simple loads, and returns, all collection data for the requested user id.
            For now it also returns a Exception, when no items are loaded, this might change later when i convert it into live code.
                $id (Assoc Array)   - The user id, of the user requesting collection data.
            
            Return Value:
                On failure: String.
                On success: Multi-dimensional, Associative Array ¿.
     */
    public function getCol( $id ) {
        try {
            /* Check if search key is specific enough, not really needed but a fun experiment */
            if( isset( $id["Alb_Staat"] ) || isset( $id["Alb_Aantal"] ) || isset( $id["Alb_Opmerk"] ) || isset( $id["Alb_DatumVerkr"] ) ) {
                throw new Exception( App::get( "errors" )->getError( "idNotVal" ) );
            }
            
            /* If loading was completed, return all loaded items */
            if( $this->loadCol( $id ) ) {
                return $this->collections;
            /* Else return a no items error */
            } else {
                throw new Exception( App::get( "errors" )->getError( "noItems" ) );
            }
        /* Handle any exception messages during this process, this includes any database exceptions */
        } catch( Exception $e ) {
            return $e->getMessage();
        }
    }

    /*  changeCol($data):
            This function either stores or removes data, based on the evalCol() function.
                $data (Assoc Array)         - The data required to add/remove to/from a collection.
                $albumNaam (string)         - The name of the album/item that is being added/removed.
                $eval (Boolean)             - The evaluation of the duplication check from evalCol().
                $store (empty array/String) - The result of the database operation of storing/removing said data.
                $feedback (Assoc Array)     - The string that is shown to the user, to reflect if something was added or removed.
            
            Return Value:
                On Failure: String.
                On success: Assoc Array.
     */
    public function changeCol( $data ) {
        try {
            /* Get album name now, so we can use it for the user feedback message */
            if( isset( $data["Alb_Index"] ) ) {
                $albumNaam = App::get( "albums" )->getAlbAtt( "Album_Naam", [ "Album_Index" => $data["Alb_Index"] ] );
            /* Throw exception if there wasnt a album id stored */
            } else {
                throw new Exception( App::get( "errors" )->getError( "noProc" ) );
            }

            /* Check if a user index was set, and load its collection data */
            if( isset( $data["Gebr_Index"] ) && $this->loadCol( [ "Gebr_Index" => $data["Gebr_Index"] ] ) ) {
                /* Evaluate the $data its album index, against all loaded collection items */
                $eval = $this->evalCol( [ "Alb_Index" => $data["Alb_Index"] ] );
            /* If non was set, throw a specific exception */
            } else {
                throw new Exception( App::get( "errors" )->getError( "noUserId" ) );
            }

            /* If the album is part of this user collection, i remove it and prepare a user feedback message */
            if( $eval ) {
                $store = App::get( "database" )->remove( "collecties", [ "Gebr_Index" => $data["Gebr_Index"], "Alb_Index" => $data["Alb_Index"] ] );
                $feedback = [ "fetchResponse" => "The album: {$albumNaam}, was removed from your collection." ];
            /* If the album is not part of this user collection, i add it and prepare a user feedback message */
            } else {
                $store = App::get( "database" )->insert( "collecties", $data );
                $feedback = [ "fetchResponse" => "The album: {$albumNaam}, was added to your collection." ];
            }

            /* Return the feedback if the data was stored/removed */
            if( empty( $store ) ) {
                return $feedback;
            /* If the PDO had errors, return a less detailed error for the user */
            } else {
                throw new Exception( App::get( "errors" )->getError( "noProc" ) );
            }
        /* Handle any exception messages during this process, this includes any database exceptions */
        } catch( Exception $e ) {
            return $e->getMessage();
        }
    }
}

?>