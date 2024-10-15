<?php

namespace App\Controllers;

use App\Core\App;

/* LogicController Class: For all complex logic required to add/update/remove items to/from the database. */
class LogicController {
    /* Landingpage functions */
    /*  dbCreation():
            The function linked to the landingpage route, if no database tables where set this triggers.
            And it creates all tables and the default admin account, before redirecting back to the landingpage.

            Return Value    - Redirect -route-> '/'
     */
    public function dbCreation() {
        App::get( "database" )->createTable( "gebruikers" );
        App::get( "database" )->createAdmin();
        App::get( "database" )->createTable( "series" );
        App::get( "database" )->createTable( "serie_meta" );
        App::get( "database" )->createTable( "albums" );
        App::get( "database" )->createTable( "collecties" );
        return App::redirect( "" );
    }
    
    /*  register():
            The POST route for account registration, that uses the user class to process the request.
                $temp   : The user input that needs to be stored.
                $store  : The outcome of the user class trying to store the user data.
            
            Return Value:
                On sucess   - Redirect -route-> '/#login-pop-in'
                On failed:  - Redirect -route-> '/#account-maken-pop-in'
     */
	public function register() {
        $temp = [
            "Gebr_Naam" => htmlspecialchars( $_POST["gebr-naam"] ),
            "Gebr_Email" => htmlspecialchars( $_POST["email"] ),
            "Gebr_WachtW" => password_hash( $_POST["wachtwoord"], PASSWORD_BCRYPT ),
            "Gebr_Rechten" => "gebruiker"
        ];

        $store = App::get( "user" )->setUser( $temp );

        if( !isset( $store["error"] ) ) {
            App::get( "session" )->setVariable( "header", [ "feedB" => $store ] );
            return App::redirect( "#login-pop-in" );
        } else {
            App::get( "session" )->setVariable( "header", $store );
            return App::redirect( "#account-maken-pop-in" );
        }
    }

    /*  login():
            The POST route for the login process, where the user class is used to validate the user.
            And where the SESSION data is set, linking a user to a session, so we can verify the user later on.
                $pw (string)                    - The password input from the user.
                $cred (string)                  - The user credentials (e-mail or user name).
                $userCheck (Bool/Assoc Array)   - The user check validation result.
                $userIdFetch (Bool/Assoc Array) - The user Index fetch result
            
            Return Value (redirect):
                If validated as Admin   - Redirect -route-> '/beheer'
                If validated as User    - Redirect -route-> '/gebruik'
                If validation failed    - Redirect -route-> '/#login-pop-in'
     */
    public function login() {
        /* Process the POST information, and validate the user that is trying to login. */
        $pw = $_POST["wachtwoord"];
        $cred = htmlspecialchars( $_POST["accountCred"] );
        $userCheck = App::get( "user" )->validateUser( $cred, $pw );

        /* Check the user evaluation, and execute the correct logic. */
        if( !is_array( $userCheck ) ) {
            /* Get the user id from the user that is trying to login */
            $userIdFetch = App::get( "user" )->getUserId();

            /* If the user ID fetch worked, i store said id in the session. */
            if( !is_array( $userIdFetch ) ) {
                App::get( "session" )->setVariable( "user", [ "id" => $userIdFetch ] );
            /* If the user ID fetch failed, i store the error in the session, and redirect to the pop-in. */
            } else {
                App::get( "session" )->setVariable( "header", [ "error" => $userIdFetch ] );
                return App::redirect( "#login-pop-in" );
            }

            /* Evaluate the user rights. */
            $userRightsCheck = App::get( "user" )->evalUser();

            /* Redirect the user according to the user rights evaluation. */
            if( $userRightsCheck === TRUE ) {
                App::get( "session" )->setVariable( "user", [ "admin" => FALSE ] );

                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ 'welcome' => "Welcome " . App::get( "user" )->getUserName() ]
                ] );

                return App::redirect( "gebruik" );
            } elseif( $userRightsCheck === FALSE ) {
                App::get( "session" )->setVariable( "user", [ "admin" => TRUE ] );

                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "welcome" => "Welcome " . App::get( "user" )->getUserName() ]
                ] );

                return App::redirect( "beheer" );
            /* If the user rights evaluation failed, i store the error and redirect to the pop-in. */
            } else {
                App::get( "session" )->setVariable( "header", [ "error" => $userRightsCheck ] );
                return App::redirect( "#login-pop-in" );
            }
        /* If the user was not validated, i store the error, and redirect back to the pop-in. */
        } else {
            App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
            return App::redirect( "#login-pop-in" );            
        }
    }

    /*  logout():
            The POST '/logout' route, cleaning and ending the user session, before redirecting to home.

            Return Value    - Redirect -route-> '/'
     */
    public function logout() {
        App::get( "session" )->endSession();
        return App::redirect( "" );
    }

    /* Adminpage functions */
    /*  beheer():
            The POST route for '/beheer', this is similar to the GET route in the 'PageController'.
            But here is also deal with loading the Series view, and thus loading all related albums.
                $userCheck (Bool/Assoc Array)   - The user check based on the stored session data.
                $checkSerie (Bool/Assoc Array)  - The item duplicate check, based on the item naam and potentially index.
            
            Return Type:
                On Validation fail          - Redirect  -route-> '/'
                On Name check fail          - Redirect  -route-> '/beheer'
                On Name check pass          - Redirect  -route-> '/beheer#seriem-pop-in'
                On pop-in close             - View      -route-> '/beheer.view.php'
                On Album add trigger        - Redirect  -route-> '/beheer#albumt-pop-in'
                In all other cases          - View      -route-> '/beheer.view.php' 
                
     */
    public function beheer() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );

        /* Validate the userCheck result, and execute the correct logic */
        if( !is_array( $userCheck ) ) {
            /* Skip all other logic if a pop-in is closed */
            if( isset( $_POST["close-pop-in"] ) && isset( $_SESSION["page-data"]["huidige-serie"] ) ) {
                return App::view( "beheer" );
            }

            /* Check for duplicate serie names, when opening the serie-maken pop-in */
            if( isset( $_POST["newSerName"] ) ) {
                $checkSerie = App::get( "collection" )->checkItemName( "serie", $_POST["newSerName"] );
            }

            /* Evaluate checkSerie with the expected POST data, to display the correct message and redirect to the proper route. */
            if( isset( $_POST["newSerName"] ) && is_array( $checkSerie ) ) {
                App::get( "session" )->setVariable( "header", [ "error" => $checkSerie ] );
                return App::redirect( "beheer" );
            } elseif( isset( $_POST["newSerName"] ) && !is_array( $checkSerie ) ) {
                App::get( "session" )->setVariable( "page-data", [ "new-serie" => $_POST["newSerName"] ] );
                return App::redirect( "beheer#seriem-pop-in" );
            }

            /* Add session tag, for the album-toevoegen pop-in */
            if( isset($_POST["album-toev"] ) ) {
                App::get( "session" )->setVariable( "page-data", [ "add-album" => App::get( "collection" )->getSerInd( $_POST["album-toev"] ) ] );
                return App::redirect( "beheer#albumt-pop-in" );
            }

            /* Make sure important session tags stay set untill specifically unset */
            $impTags = [ "add-album", "new-serie", "edit-serie", "huidige-serie", "album-dupl", "album-cover", "isbn-scan", "isbn-search" ];

            if( !App::get( "session" )->checkVariable( "page-data", $impTags ) ) {
                unset($_SESSION["page-data"]);
            }

            /* Clear series session data when returning to the admin serie-view from album-view */
            if( isset( $_POST["return"] ) ) {
                unset( $_SESSION["page-data"]["huidige-serie"] );
                unset( $_SESSION["page-data"]["series"] );
                return App::redirect( "beheer" );
            }

            /* Populate the session series data is there is non */
            if( empty($_SESSION["page-data"]["series"] ) ) {
                App::get( "session" )->setVariable( "page-data", App::get( "collection" )->getSeries() );
            }

            /* Store the albums and name for a serie, if the admin is viewing a serie */
            if( !empty( $_POST["serie-index"] ) ) {
                App::get( "session" )->setVariable( "page-data", App::get( "collection" )->getAlbums( $_POST["serie-index"] ) );
                App::get( "session" )->setVariable( "page-data", [ "huidige-serie" => App::get( "collection" )->getItemName( "serie", $_POST["serie-index"] ) ] );
                return App::redirect( "beheer" );
            }

            /* Store serie index, if the admin is editing a serie */
            if( isset( $_POST["serie-edit-index"] ) ) {
                App::get( "session" )->setVariable( "page-data", [ "edit-serie" => $_POST["serie-edit-index"] ] );
                return App::redirect( "beheer#serieb-pop-in" );
            }

            /* Fail-save for unexpected behavior */
            return App::view( "beheer" );
            
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
            return App::redirect( "" );  
        }
    }

    /*  serieM():
            The POST route for '/serieM', for checking series names and creating series.
            Where the latter is related to the pop-in form, and the former to the name input from the controller.
                $userCheck  (Bool/Assoc Array)  - The user check based on the stored session data.
                $itemCheck  (Bool/Assoc Array)  - The item duplicate check, based on the item naam and potentially index.
                $sqlData    (Assoc Array)       - The POST data prepared for the SQL database.
                $store      (Bool/Assoc Array)  - The result of the database operation.

            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed name check        - Redirect -route-> '/beheer#seriem-pop-in'
                On Failed Database action   - Redirect -route-> '/beheer'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function serieM() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );

        /* Validate the userCheck result, and execute the correct logic */
        if( !is_array( $userCheck ) ) {
            /* Check if serie-naam was set in the post, and check the name */
            if(isset( $_POST["serie-naam"] )) {
                $itemCheck = App::get( "collection" )->checkItemName( "serie", $_POST["serie-naam"] );
            }

            /* If an array was returned, i need to set the duplicate data tag, and return the error. */
            if( is_array( $itemCheck ) ) {
                App::get( "session" )->setVariable( "page-data", [ "serie-dupl" => $_POST ] );
                App::get( "session" )->setVariable( "header", [ "error" => $itemCheck ] );
                return App::redirect( "beheer#seriem-pop-in" );
            /* Otherwhise i can store the name for processing it. */
            } else {
                $sqlData = [ "Serie_Naam" => htmlspecialchars( $_POST["serie-naam"] ) ];
            }

            /* Check and store the other POST data, and then attempt to store it in the DB */
            $sqlData["Serie_Maker"] = isset( $_POST["makers"] ) ? htmlspecialchars( $_POST["makers"] ) : "";
            $sqlData["Serie_Opmerk"] = isset( $_POST["opmerking"] ) ? htmlspecialchars( $_POST["opmerking"] ) : "";
            $store = App::get( "collection" )->setSerie( $sqlData );

            /* Evaluate the DB store request, and redirect/store feedback information accordingly. */
            if( !is_string( $store ) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "fetchResponse" => "Het toevoegen van: " . $_POST["serie-naam"] . " is gelukt !" ]
                ] );

                unset( $_SESSION["page-data"]["series"] );

                return App::redirect( "beheer" );
            } else {
                App::get( "session" )->setVariable( "header", [ "error" => $store ] );
                return App::redirect( "beheer" );
            }

        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
            return App::redirect( "" );  
        }
    }

    /*  serieBew():
            This function deals with editing serie data on the admin page, and stores the changes made.
                $userCheck (Bool/Assoc Array)   - The user check based on the stored session data.
                $itemCheck (Bool/Assoc Array)   - The item duplicate check, based on the item naam and potentially index.
                $serieData (Assoc Array)        - The POST data prepared for the SQL Database.
                $store     (Bool/Assoc Array)   - The result of the database operation.

            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed name check        - Redirect -route-> '/beheer#serieb-pop-in'
                On Failed Database action   - Redirect -route-> '/beheer#serieb-pop-in'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function serieBew() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );

        /* Validate the userCheck result, and execute the correct logic */
        if( !is_array( $userCheck ) ) {
            /* Check if serie-naam was set in the post, and check the name */
            if( isset( $_POST["naam"] ) ) {
                $itemCheck = App::get( "collection" )->checkItemName( "serie",  $_POST["naam"], $_POST["index"] );
            }

            /* If an array was returned, i need to set the duplicate data tag, and return the error. */
            if( is_array( $itemCheck ) ) {
                App::get( "session" )->setVariable( "page-data", [ "edit-serie" => $_POST["index"] ] );
                App::get( "session" )->setVariable( "header", [ "error" => $itemCheck ] );
                return App::redirect( "beheer#serieb-pop-in" );
            /* Otherwhise i can store the name for processing it. */
            } else {
                $serieData["Serie_Naam"] = htmlspecialchars( $_POST["naam"] );
            }

            /* Check and store the other POST data, and then attempt to store it in the DB */
            $serieData["Serie_Maker"] = isset( $_POST["makers"] ) ? htmlspecialchars( $_POST["makers"] ) : "";
            $serieData["Serie_Opmerk"] = isset( $_POST["opmerking"] ) ? htmlspecialchars( $_POST["opmerking"] ) : "";
            $store = App::get( "collection" )->setSerie( $serieData, $_POST["index"] );

            /* Evaluate the DB store request, and redirect/store feedback information accordingly. */
            if( !is_array( $store ) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "fetchResponse" => "Het aanpassen van: " . $_POST["naam"] . " is gelukt !"]
                ] );

                unset( $_SESSION["page-data"]["series"] );

                return App::redirect( "beheer" );
            } else {
                App::get( "session" )->setVariable( "header", [ "error" => $store ] );
                App::get( "session" )->setVariable( "page-data", [ "edit-serie" => $_POST["index"] ] );

                return App::redirect( "beheer#serieb-pop-in" );
            }

        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
            return App::redirect( "" );
        }
    }

    /*  serieVerw():
            This removes a serie and all its albums from the database, and gives back user feedback based on that.
                $userCheck (Bool/Assoc Array)   - The user check based on the stored session data.
                $serieData (Assoc Array)        - The POST data prepared for the SQL Database.
                $remove_# (Bool/Assoc Array)    - The result of the database operations.

            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed Database action   - Redirect -route-> '/beheer'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function serieVerw() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );

        /* Validate the userCheck result, and execute the correct logic */
        if( !is_array( $userCheck ) ) {
            $remove_1 = App::get( "collection" )->remItem( "albums", [ "Album_Serie" => $_POST["serie-index"] ] );
            $remove_2 = App::get( "collection" )->remItem( "series", [ "Serie_Index" => $_POST["serie-index"], "Serie_Naam" => $_POST["serie-naam"] ] );

            /* Evaluate the DB operation, and return the correct feedback, reset the correct session data and redirect back to the page. */
            if( !is_array( $remove_1 ) || !is_array($remove_2) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "fetchResponse" => "Het verwijderen van: " . $_POST["serie-naam"] . " en alle albums is geslaagd!" ]
                ] );

                unset( $_SESSION["page-data"]["series"] );

                return App::redirect( "beheer" );

            /* Store either of returned errors if set, since there identical if both are set anyway. */
            } else {
                if( is_array( $remove_1 ) ) {
                    App::get( "session" )->setVariable( "header", [ "error" => $remove_1 ] );
                } else if( is_array($remove_2) ) {
                    App::get( "session" )->setVariable( "header", [ "error" => $remove_2 ] );
                }

                return App::redirect( "beheer" );
            }
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
            return App::redirect( "" );
        }
    }

    /*  albumT():
            This function checks the album name, and either stores that user input, or returns it for the user to correct it.
                $userCheck (Bool/Assoc Array)   - The user check based on the stored session data.
                $itemCheck (Bool/Assoc Array)   - The item duplicate check, based on the item naam and potentially index.
                $albumData (Assoc Array)        - The POST data prepared for the SQL Database.
                $store     (Bool/Assoc Array)   - The result of the database operation.
            
            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed name check        - Redirect -route-> '/beheer#albumt-pop-in'
                On Failed Database action   - Redirect -route-> '/beheer'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function albumT() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );

        /* Validate the userCheck result, and execute the correct logic. */
        if( !is_array( $userCheck ) ) {
            /* Check if the POST data was set for the item name check. */
            if( isset( $_POST["album-naam"] ) ) {
                $itemCheck = App::get( "collection" )->checkItemName( "album", $_POST["album-naam"], $_POST["serie-index"] );
            }

            /* If an array was returned, the correct session data has to be prepared and stored, and i redirect to the pop-in. */
            if( is_array( $itemCheck ) ) {
                $returnData = $_POST;

                /* Album cover loop, for base64 conversion */
                if( $_FILES["album-cover"]["error"] === 0 ) {
                    $fileName = basename( $_FILES["album-cover"]["name"] );
                    $fileType = pathinfo( $fileName, PATHINFO_EXTENSION );
                    $image = $_FILES["album-cover"]["tmp_name"];
                    $imgContent = file_get_contents($image);
                    $dbImage = "data:image/" . $fileType . ";charset=utf8;base64," . base64_encode($imgContent);
                    $returnData["album-cover"] = $dbImage;
                }

                App::get( "session" )->setVariable( "header", [ "error" => $itemCheck ] );
                App::get( "session" )->setVariable( "page-data", [ "album-dupl" => $returnData ] );
                return App::redirect( "beheer#albumt-pop-in" );
            /* Otherwhise i can store the name for processing it. */
            } else {
                $albumData["Album_Naam"] = htmlspecialchars( $_POST["album-naam"] );
            }

            /* Prep the rest of the album data for SQL */
            $albumData["Album_Opm"] = "W.I.P.";
            $albumData["Album_Serie"] = $_POST["serie-index"];
            $albumData["Album_ISBN"] = ( !empty( $_POST["album-isbn"] ) || $_POST["album-isbn"] !== "" ) ? $_POST["album-isbn"] : 0;
            $albumData["Album_Nummer"] = ( !empty( $_POST["album-nummer"] ) ) ? $_POST["album-nummer"] : 0;
            if( !empty( $_POST["album-datum"] ) ) { $albumData["Album_UitgDatum"] = $_POST["album-datum"]; }

            /* Album cover loop, for base64 conversion, or re-adding of the one stored in the session */
            if( $_FILES["album-cover"]["error"] === 0 ) {
                $fileName = basename( $_FILES["album-cover"]["name"] );
                $fileType = pathinfo( $fileName, PATHINFO_EXTENSION );
                $image = $_FILES["album-cover"]["tmp_name"];
                $imgContent = file_get_contents($image);
                $dbImage = "data:image/" . $fileType . ";charset=utf8;base64," . base64_encode($imgContent);
                $albumData["Album_Cover"] = $dbImage;
            } elseif( isset( $_SESSION["page-data"]["album-dupl"]["Album_Cover"] ) ) {
                $albumData["Album_Cover"] = $_SESSION["page-data"]["album-dupl"]["Album_Cover"];
            }

            /* Attempt to store the album in the SQL DB */
            $store = App::get( "collection" )->setAlbum( $albumData );

            /* Evaluate the DB store request, and redirect/store feedback information accordingly. */
            if( !is_array( $store ) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "fetchResponse" => "Het toevoegen van: " . $_POST["album-naam"] . " is gelukt !" ]
                ] );

                /* Unset specific session page-data or states, to ensure the expected page behavior */
                if( isset( $_SESSION["page-data"]["huidige-serie"] ) ) { unset( $_SESSION["page-data"]["albums"] ); }
                if( isset( $_SESSION["page-data"]["album-dupl"] ) ) { unset( $_SESSION["page-data"]["album-dupl"] ); }
                if( isset( $_SESSION["page-data"]["add-album"] ) ) { unset( $_SESSION["page-data"]["add-album"] ); }
                if( isset( $_SESSION["page-data"]["isbn-scan"] ) ) { unset( $_SESSION["page-data"]["isbn-scan"] ); }
                if( isset( $_SESSION["page-data"]["isbn-search"] ) ) { unset( $_SESSION["page-data"]["isbn-search"] ); }
                if( isset( $_SESSION["page-data"]["searched"] ) ) { unset( $_SESSION["page-data"]["searched"] ); }

                return App::redirect( "beheer" );
            } else {
                App::get( "session" )->setVariable( "header", [ "error" => $store ] );
                return App::redirect( "beheer" );
            }
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
            return App::redirect( "" );
        }
    }

    /*  albumV():
            The remove album function, with a trigger to repopulate the session data after removal.
                $userCheck (Bool/Assoc Array)   - The user check based on the stored session data.
                $itemCheck (Bool/Assoc Array)   - The item duplicate check, based on the item naam and potentially index.
                $albumData (Assoc Array)        - The POST data prepared for the SQL Database.
                $store     (Bool/Assoc Array)   - The result of the database operation.

            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed name check        - Redirect -route-> '/beheer'
                On Failed Database action   - Redirect -route-> '/beheer'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function albumV() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );

        /* Validate the userCheck result, and execute the correct logic. */
        if( !is_array( $userCheck ) ) {

            if( isset( $_POST["album-index"] ) ) {
                $itemCheck = App::get( "collection" )->getItemName( "album", $_POST["serie-index"], $_POST["album-index"] );
                $store = App::get( "collection" )->remItem( "albums", [ "Album_Index" => $_POST["album-index"] ] );
            } else {
                return App::redirect( "beheer" );
            }

            //  TODO: Figure out where $ablName should come from, seems odd that its is currently nothing.
            /* Evaluate the itemCheck and DB action. */
            if( !is_array( $itemCheck ) && !is_array( $store ) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "fetchResponse" => "Het verwijderen van: " . $itemCheck . " is geslaagd!" ]
                ] );

                unset( $_SESSION["page-data"]["albums"] );

                return App::redirect( "beheer" );
            /* Store the correct error, and redirect accordingly */
            } else {
                if( is_array( $itemCheck ) ) {
                    App::get( "session" )->setVariable( "header", [ "error" => $itemCheck ]);
                } elseif( is_array( $store ) ) {
                    App::get( "session" )->setVariable( "header", [ "error" => $store ]);
                }

                return App::redirect( "beheer" );
            }
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
            return App::redirect( "" );
        }
    }

    /*  albumBew():
            This function deal with all album-bewerken actions, but does currently cause unwanted page refreshes.
                $userCheck (Bool/Assoc Array)   - The user check based on the stored session data.
                $itemCheck (Bool/Assoc Array)   - The item duplicate check, based on the item naam and potentially index.
                $albumData (Assoc Array)        - The POST data prepared for the SQL Database.
                $store     (Bool/Assoc Array)   - The result of the database operation.
            
            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed Database action   - Redirect -route-> '/beheer'
                On Album Edit Request       - Redirect -route-> '/beheer#albumb-pop-in'
                On Duplicate Name Check     - Redirect -route-> '/beheer#albumb-pop-in'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function albumBew() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );

        /* Validate the userCheck result, and execute the correct logic. */
        if( !is_array( $userCheck ) ) {

            /* If the edit album button edit was pressed, just store the tag in the session and redirect to the pop-in. */
            if( isset( $_POST["albumEdit"] ) ) {
                App::get( "session" )->setVariable( "page-data", [ "album-edit" => $_POST["albumEdit"] ] );
                return App::redirect( "beheer#albumb-pop-in" );
            }

            /* Check item name for duplicate entries. */
            $itemCheck = App::get( "collection" )->checkItemName( "album", $_POST["album-naam"], $_POST["serie-index"], $_POST["album-index"] );

            /* Evaluate itemCheck, and store the error including the album index tag in the session, and redirect to the pop-in. */
            if( is_array( $itemCheck ) ) {
                App::get( "session" )->setVariable( "page-data", [ "album-edit" => $_POST["album-index"] ] );
                App::get( "session" )->setVariable( "header", [ "error" => $itemCheck ] );
                return App::redirect( "beheer#albumb-pop-in" );
            /* Otherwhise just store the name for the SQL DB. */
            } else {
                $albumData["Album_Naam"] = htmlspecialchars( $_POST["album-naam"] );
            }

            /* Store the remaining data for SQL */
            if( isset( $_POST["album-nummer"] ) ) { $albumData["Album_Nummer"] = $_POST["album-nummer"]; }
            if( !empty( $_POST["album-datum"] ) ) { $albumData["Album_UitgDatum"] = $_POST["album-datum"]; }

            $albumData["Album_Isbn"] = isset( $_POST["album-isbn"] ) ? $_POST["album-isbn"] : 0;
            $albumData["Album_Opm"] = isset( $_POST["album-opm"] ) ? $_POST["album-opm"] : 'W.I.P';

            /* Album cover loop, for base64 conversion */
            if( $_FILES["album-cover"]["error"] === 0 ) {
                $fileName = basename( $_FILES["album-cover"]["name"] );
                $fileType = pathinfo( $fileName, PATHINFO_EXTENSION );
                $image = $_FILES["album-cover"]["tmp_name"];
                $imgContent = file_get_contents($image);
                $dbImage = "data:image/" . $fileType . ";charset=utf8;base64," . base64_encode($imgContent);
                $albumData["Album_Cover"] = $dbImage;
            } else if( isset( $_SESSION["page-data"]["Album_Cover"] ) ) {
                $albumData["Album_Cover"] = $_SESSION["page-data"]["Album_Cover"];
                unset( $_SESSION["page-data"]["Album_Cover"] ); // unset after using it.
            }

            /* Attempt to store the album data in the SQL DB. */
            $store = App::get( "collection" )->setAlbum( $albumData, [
                "Album_Index" => $_POST["album-index"],
                "Album_Serie" => $_POST["serie-index"]
            ] );

            /* Evaluate the DB action, and store the correct response (unset data if required), and redirect back to the admin page. */
            if( is_array( $store ) ) {
                App::get( "session" )->setVariable( "header", [ "error" => $store ] );

                return App::redirect( "beheer" );
            } else {
                App::get( "session" )->setVariable( "header", [ "feedB" => 
                        [ "fetchResponse" => "Het aanpassen van: " . $_POST["album-naam"] . " is gelukt !" ]
                ] );

                if( isset( $_SESSION["page-data"]["huidige-serie"] ) ) { unset( $_SESSION["page-data"]["albums"] ); }
                if( isset( $_SESSION["page-data"]["album-edit"] ) ) { unset( $_SESSION["page-data"]["album-edit"] ); }

                return App::redirect( "beheer" );
            }
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
            return App::redirect( "" );
        }
    }

    /*  adminReset():
            This function can reset user passwords, since that is missing from the main page login-pop-in.
                $userCheck (Bool/Assoc Array)   - The user check based on the stored session data.
                $store     (Bool/Assoc Array)   - The result of the database operation.

            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed name check        - Redirect -route-> '/beheer'
                On Failed Database action   - Redirect -route-> '/beheer#ww-reset-pop-in'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function adminReset() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );

        /* Validate the userCheck result, and execute the correct logic. */
        if( !is_array( $userCheck ) ) {
            $store = App::get( "user" )->updateUser( "gebruikers",
                [ "Gebr_WachtW" => password_hash( $_POST["wachtwoord1"], PASSWORD_BCRYPT ) ],
                [ "Gebr_Email" => $_POST["email"] ]
            );

            /* Evaluate the DB action, and store the correct response, and redirect back to the correct page. */
            if( is_array( $store ) ) {
                App::get( "session" )->setVariable( "header", [ "error" => $store ] );
                return App::redirect( "beheer#ww-reset-pop-in" );
            } else {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "fetchResponse" => "Het wachtwoord van: " . $_POST["email"] . " is aangepast !" ]
                ] );

                return App::redirect( "beheer" );
            }
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
            return App::redirect( "" );
        }
    }

    /* User-Page functions */
    /*  gebruik():
            The POST route for '/gebruik', this is similar to the GET route in the 'PageController'.
                $userCheck (Bool/Assoc Array)   - The user check based on the stored session data.

            Return Value:
                On sucess   - View      -route-> '../gebruik.view.php'
                On fail     - Redirect  -route-> '../'
     */
    public function gebruik() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"] ) : App::get( "user" )->checkUser( -1 );

        /* Validate the userCheck result, and execute the correct logic. */
        if( !is_array( $userCheck ) ) {
            /* Always unset the collection data in the session, and repopulate the series and collections. */
            unset( $_SESSION["page-data"]["collections"] );
            App::get( "session" )->setVariable( "page-data", App::get("collection")->getSeries() );
            App::get( "session" )->setVariable( "page-data", App::get("collection")->getColl( "collecties", [ "Gebr_Index" => $_SESSION["user"]["id"] ] ) );

            /* If a collection is being viewed, get all albums for that serie, and the user there collection data, before setting the correct flag in the session */
            if( !empty( $_POST["serie_naam"] ) ) {
                App::get( "session" )->setVariable( "page-data", App::get( "collection" )->getAlbums( $_POST["serie_naam"] ) );
                App::get( "session" )->setVariable( "page-data", App::get( "collection" )->getColl( "collecties", [ "Gebr_Index" => $_SESSION["user"]["id"] ] ) );
                App::get( "session" )->setVariable( "page-data", [ "huidige-serie" => $_POST["serie_naam"] ] )  ;
            }

            return App::view("gebruik");
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
            return App::redirect("");
        }
    }

    /*  albSta():
            The POST route for '/albSta', where we create collection data, based on what album(s) got toggled on/off.
                $userCheck (Bool/Assoc Array)   - The user check based on the stored session data.
                $ids        (Assoc Array)       - Id's required for setting and removing collection data.
                $store     (Bool/Assoc Array)   - The result of the database operation.
            
            Return Value:
                On Added    - View      -route-> '../gebruik.view.php'
                On Removed  - View      -route-> '../gebruik.view.php'
                On fail     - Redirect  -route-> '../'
     */
    public function albSta() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"] ) : App::get( "user" )->checkUser( -1 );

        /* Validate the userCheck result, and execute the correct logic. */
        if( !is_array( $userCheck ) ) {
            /* If a albumIndex is in the POST, set ids for SQL first. */
            if( isset( $_POST["albumIndex"] ) ) {
                $ids = [ "Gebr_Index" =>  $_SESSION["user"]["id"], "Alb_Index" => $_POST["albumIndex"] ];
            }

            /* Then evaluate the checkState, and execute the correct DB action */
            if( isset( $_POST["checkState"] ) && $_POST["checkState"] === "false" ) {
                $store = App::get( "collection" )->setColl( "collecties", $ids );
            } else if ( isset( $_POST["checkState"] ) && $_POST["checkState"] === "true" ) {
                $store = App::get( "collection" )->remItem( "collecties", $ids );
            }

            /* Evaluate the DB action, and give the coorect feedback, clear the correct page-data, and redirect to the user page. */
            if( !is_array( $store ) ) {
                /* If no errors, evaluated the checkState and execute the correct logic. */
                if( $_POST["checkState"] === "false" ) {
                    App::get( "session" )->setVariable( "header", [ "feedB" =>
                        [ "fetchResponse" => "Het album: " . $_POST["albumNaam"] . ", is toegvoegd aan uw collectie!" ]
                    ] );

                    unset( $_SESSION["page-data"]["colllections"] );

                    return App::redirect( "gebruik" );
                } elseif( $_POST["checkState"] === "true" ) {
                    App::get( "session" )->setVariable( "header", [ "feedB" =>
                            [ "fetchResponse" => "Het album: " . $_POST["albumNaam"] . ", is verwijdert van uw collectie!" ]
                    ] );

                    unset( $_SESSION["page-data"]["colllections"] );

                    return App::redirect( "gebruik" );
                }
            /* If there was an error stored, we store that in the session, and redirect to the user page. */
            } else {
                App::get( "session" )->setVariable( "header", [ "error" => $store ] );
                return App::redirect( "gebruik" );
            }
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
            return App::redirect( "" );
        }
    }

    //  TODO: Wait for user feedback, to see if anything need changing.
    //  TODO: Add isbn-search submit, regardless of any required form fields.

    /*	scan(): This function simply set the correct session tag, and redirects to the pop-in to load the correct template. */
	public function scan() {
		/* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );

		/* Validate the userCheck result, and execute the correct logic. */
		if( !is_array( $userCheck ) ) {
            if( isset( $_POST["album-toev"] ) ) {
                App::get( "session" )->setVariable( "page-data", [ "serie-index" => App::get( "collection" )->getSerInd( $_POST["album-toev"] ) ] );
                App::get( "session" )->setVariable( "page-data", [ "isbn-scan" => True ] );
                return App::redirect( "beheer#albumS-pop-in" );
            }
		} else {
			App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
			return App::redirect( "" );
		}
    }

    /*  isbn():
            This function attempt to get as much item data as possible, from the Google API, so forms can be pre-filled.
            This works for both the ISBN search function, as the bar-code scanner, though only give ISBN/EAN book information in return.
     */
    public function isbn() {
		/* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );

        /* Evaluate the user authentication. */
        if( !is_array( $userCheck ) ) {
            $result = "";

            /* Check if the isbn was set, and start the parsing/processing logic. */
            if( isset( $_POST["album-isbn"] ) ) {
                /* Attempt to get data from the Google API. */
                $result = App::get( "isbn" )->get_data( $_POST["album-isbn"], $_POST["serie-index"] );

                // If there is a data array returned,
                if( isset( $result[0] ) ) {
                    // and the first index is called titles,
                    if( $result[0] === "Titles" ) {
                        // i store that data in the session,
                        $_SESSION["page-data"]["show-titles"] = $result;

                        // and return to a user choice pop-in.
                        return App::redirect( "beheer#isbn-preview" );
                    }
                }

                /* Items that arent provided via the Google API, and should be there by default in the POST. */
                if( isset( $_POST["album-index"] ) ) { $result["Album_Index"] = $_POST["album-index"]; }
                if( isset( $_POST["serie-index"] ) ) { $result["Album_Serie"] = $_POST["serie-index"]; }

                /* Items that can only be user input, only applies for editing albums. */
                if( isset( $_POST["album-nummer"] ) ) { $result["Album_Nummer"] = $_POST["album-nummer"]; }

                /* Items that need to be taken from the Google API search if avaible, otherwhise take the user input, only applies for editing albums. */
                if( !isset( $result["Album_Naam"] ) && !empty( $_POST["album-naam"] ) ) { $result["Album_Naam"] = $_POST["album-naam"]; }
                if( !isset( $result["Album_UitgDatum"] ) && !empty( $_POST["album-datum"] ) ) { $result["Album_UitgDatum"] = $_POST["album-datum"]; }
                if( !isset( $result["Album_Opm"] ) && !empty( $_POST["album-opm"] ) ) { $result["Album_Opm"] = $_POST["album-opm"]; }
            // Loop for when the user had to make a choice, between several items that share the same isbn.
            } elseif( isset( $_POST["isbn-choice"] ) && isset( $_POST["title-choice"] ) ) {
                // Get that specific choice and store it, for processing.
                $result = App::get( "isbn" )->get_data( $_POST["isbn-choice"], null, $_POST["title-choice"] );
            }

            /* Evaluate the result, and prepare the correct feedback and page-data, on error we redirect back to the admin page. */
            if( isset( $result ) ) {
                if( !isset( $result["error"] ) ) {
                    App::get( "session" )->setVariable( "header", [ "feedB" =>
                        [ "fetchResponse" => "Controleer of de ingevulde gegevens kloppen en compleet zijn !" ]
                    ] );
                } else {
                    App::get( "session" )->setVariable( "header", [ "feedB" =>
                            [ "fetchResponse" => $result["error"] ]
                    ] );

                    return App::redirect( "beheer" );
                }

                /* If the scan tag is set, we prep the data for the related pop-ins */
                if( isset( $_SESSION["page-data"]["isbn-scan"] ) ) {
                    App::get( "session" )->setVariable( "page-data", [ "isbn-scan" => $result ] );
                    App::get( "session" )->setVariable( "page-data", [ "barcode" => TRUE ] );
                    App::get( "session" )->setVariable( "header", [ "broSto" => [ "isbnScan" => TRUE ] ] );
                /* If that tag wasnt set, its going the be manual isbn search.  */
                } else {
                    App::get( "session" )->setVariable( "page-data", [ "isbn-search" => $result ] );
                    App::get( "session" )->setVariable( "header", [ "broSto" => [ "isbnSearch" => TRUE ] ] );
                    App::get( "session" )->setVariable( "page-data", [ "searched" => TRUE ] );
                }

                // This is a bit dodgy, but the only goodway atm to detect where the input came from.
                // Came from the album-bewerken pop-in.
                if( isset( $_POST["serie-index"] ) && isset( $_POST["album-index"] ) ) {
                    return App::redirect( "beheer#albumb-pop-in" );
                // Came from the album-toevoegen pop-in.
                } elseif( $_POST["serie-index"] ) {
                    return App::redirect( "beheer#albumt-pop-in" );
                }
            }
        /* When authentication fails, store the error, and return to the landingpage. */
		} else {
			App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
			return App::redirect( "" );
		}
    }

    //  TODO: Wait for user feedback, to see if anything need changing.
    /* userScan(): */
    public function userScan() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get("user")->checkUser( $_SESSION["user"]["id"] ) : App::get("user")->checkUser( -1 );

		if( !is_array( $userCheck ) ) {
            if( isset( $_SESSION["page-data"]["huidige-serie"] ) ) {
                App::get( "session" )->setVariable( "page-data",
                    [ "serie-index" => App::get("collection")->getSerInd( $_SESSION["page-data"]["huidige-serie"] ) ]
                );

                App::get( "session" )->setVariable( "page-data", [ "isbn-scan" => True ] );
                return App::redirect( "gebruik#albumS-pop-in" );
            }
		} else {
			App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );

			return App::redirect( "" );
		}
    }

    //  TODO: Wait for user feedback, to see if anything need changing.
    //  TODO: See if i can make this a bit faster, the amount of loops etc make this a bit slow on the processing end of things.
    /* userIsbn(): */
    public function userIsbn() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"] ) : App::get( "user" )->checkUser( -1 );

        /* Validate the userCheck result, and execute the correct logic. */
		if( !is_array( $userCheck ) ) {
            /* Attempt to get data from the Google API, if isbn was set. */
            if( isset( $_POST["album-isbn"] ) ) {
                $result = App::get( "isbn" )->get_data( $_POST["album-isbn"] );
            }

            /* Confirmation\processing loop goes here */
            if( isset( $_POST["serie-index"] ) ) {
                $eColl = App::get( "collection" )->evalColl ($result, $_POST["serie-index"], $_SESSION["user"]["id"] );
                $ids = [ "Gebr_Index" => $_SESSION["user"]["id"], "Alb_Index" => App::get( "collection" )->getAlbId( $result["album-naam"] ) ];

                /* */
                if( isset( $eColl["addToColl"] ) ) {
                    $store = App::get( "collection" )->setColl( "collecties", $ids );

                    if( !is_array( $store ) ) {

                        App::get( "session" )->setVariable( "header", [ "feedB" =>
                            [ "fetchResponse" => "Het album: " . $result["album-naam"] . ", is toegvoegd aan uw collectie!" ]
                        ] );

                    } else {
                        App::get( "session" )->setVariable( "header", [ "error" => $store ] );
                    }
                }

                /* */
                if( isset( $eColl["remFromColl"] ) ) {
                    $store = App::get( "collection" )->remItem( "collecties", $ids );
                    if( !is_array( $store ) ) {
                        App::get( "session" )->setVariable( "header", [ "feedB" =>
                            [ "fetchResponse" => "Het album: " . $result["album-naam"] . ", is verwijdert van uw collectie!" ]
                        ] );
                    } else {
                        App::get( "session" )->setVariable( "header", [ "error" => $store ] );
                    }
                }

                /* */
                if( isset( $eColl["inSerie"] ) && !$eColl["inSerie"] ) {
                    App::get( "session" )->setVariable( "header", [ "feedB" =>
                        [ "fetchResponse" => "Het ablum: " . $result["album-naam"] . ", zit niet in deze serie!" ]
                    ] );
                }

                /* Unset all session states, to prevent broken page logic. */
                unset( $_SESSION["page-data"]["colllections"] );
                unset( $_SESSION["page-data"]["serie-index"] );
                unset( $_SESSION["page-data"]["isbn-scan"] );

                /* Redirect to the user page, to reflect the changes. */
                return App::redirect( "gebruik" );
            }
		} else {
			App::get( "session" )->setVariable( "header", [ "error" => $userCheck ] );
			return App::redirect( "" );
		}
    }

    /* Debug copy and paste line
        //die( var_dump( print( "<pre>" ) . print_r(  ) . print( "</pre>" ) ) );
     */

    /*  Validate one liners:
            Admin -> $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );
            User -> $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get("user")->checkUser( $_SESSION["user"]["id"] ) : App::get("user")->checkUser( -1 );
     */

    /* Debug info, for testing the isbn manual search functions.
        9780340626580
        Suske & Wiske
        Lambiorix
        alb nr 14
     */

    /* Debug info, for testing the isbn manual search functions.
        9789020666526
        De Kameleon in het goud
     */
}
?>