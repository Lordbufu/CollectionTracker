<?php

/*  Search tags to remove/edit specific content:
        - COMPATIBILITY \ REVIEW :
            Left in because im unsure if the issue is resolved correctly, now i can uncomment and make it work, while i debug and trace down the source.
        - W.I.P.:
            Needs user feedback, and a review based on the feedback.
 */

/* Debug copy and paste line
    //die( var_dump( print_r(  ) ) ) );
 */

/*  Validate one liners:
        Admin -> $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );
        User -> $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get("user")->checkUser( $_SESSION["user"]["id"] ) : App::get("user")->checkUser( -1 );
 */

/* Debug info, for testing the isbn manual search functions.
    // Optional isbn 2
    9020667505
    9789020642506
    De Kameleon in het goud

    // Optional isbn 3 ( 200+ items found )
    0123456789
 */

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
        App::get( "database" )->createTable( "albums" );
        App::get( "database" )->createTable( "collecties" );
        return App::redirect( "" );
    }
    
    /*  register():
            The POST route for account registration, that uses the user class to process the request.
                $temp   (Array) : The user input that needs to be stored
                $store  (Array) : The outcome of the user class trying to store the user data
            
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
                $pw          (String)       - The password input from the user
                $cred        (String)       - The user credentials (e-mail or user name)
                $userCheck   (Bool/Array)   - The user check validation result
                $userIdFetch (Bool/Array)   - The user Index fetch result
            
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
                App::get( "session" )->setVariable( "header", $userIdFetch );
                return App::redirect( "#login-pop-in" );
            }

            /* Evaluate the user rights. */
            $userRightsCheck = App::get( "user" )->evalUser();

            /* Get name and set welcome message, or store the error if no name was found */
            $uName = App::get( "user" )->getUserName();

            if( !is_array( $uName ) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ 'welcome' => "Welcome " . App::get( "user" )->getUserName() ]
                ] );
            } else {
                App::get( "session" )->setVariable( "header", $uName );
            }

            /* Store admin rights in session, and redirect to the correct page */
            if( $userRightsCheck === TRUE ) {
                App::get( "session" )->setVariable( "user", [ "admin" => FALSE ] );

                return App::redirect( "gebruik" );
            } elseif( $userRightsCheck === FALSE ) {
                App::get( "session" )->setVariable( "user", [ "admin" => TRUE ] );

                return App::redirect( "beheer" );
            /* If the user rights evaluation failed, i store the error and redirect to the pop-in. */
            } else {
                App::get( "session" )->setVariable( "header", $userRightsCheck );
                return App::redirect( "#login-pop-in" );
            }
        /* If the user was not validated, i store the error, and redirect back to the pop-in. */
        } else {
            App::get( "session" )->setVariable( "header", $userCheck );
            return App::redirect( "#login-pop-in" );            
        }
    }

    /*  logout():
            The POST '/logout' route, cleaning and ending the user session, before redirecting to home.

                Return Value:
                    - Redirect -route-> '/'
     */
    public function logout() {
        App::get( "session" )->endSession();
        return App::redirect( "" );
    }

    /* Adminpage functions */
    /*  beheer():
            The POST route for '/beheer', this is similar to the GET route in the 'PageController'.
            But here is also deal with loading the Series view, and thus loading all related albums.
                $userCheck  (Bool/Array)    - The user check based on the stored session data
                $checkName  (Bool/Array)    - Evaluate if the name is duplicate or not
                $serInd     (String/Array)  - The item duplicate check, based on the item naam and potentially index
                $impTags    (Array)         - Important session tags, if set the session page-data can not be cleared/cleaned
                $tempAlbums (Array)         - Temp store for all albums associated to a serie
                $serName    (Array)         - Serie name for the table title
            
            Return Type:
                On Validation fail          - Redirect  -route-> '/'
                On Name check fail          - Redirect  -route-> '/beheer'
                On Name check pass          - Redirect  -route-> '/beheer#seriem-pop-in'
                On pop-in close             - View      -route-> '/beheer.view.php'
                On Album add trigger        - Redirect  -route-> '/beheer#albumt-pop-in'
                Failsave unexpected cases   - View      -route-> '/beheer.view.php' 
                
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

            /* Check if name is duplicate and has the expected POST data, then display the correct message and redirect to the correct route */
            if( isset( $_POST["newSerName"] ) ) {
                $checkName = App::get( "series" )->SerChDup( $_POST["newSerName"] );

                if( is_array( $checkName) ) {
                    App::get( "session" )->setVariable( "header", $checkName );
                    return App::redirect( "beheer" );
                } else {
                    App::get( "session" )->setVariable( "page-data", [ "new-serie" => $_POST["newSerName"] ] );
                    return App::redirect( "beheer#seriem-pop-in" );
                }
            }

            /* Add session tag, for the album-toevoegen pop-in */
            if( isset($_POST["album-toev"] ) ) {
                $serInd = App::get( "series" )->getSerAtt( "Serie_Index", [ "Serie_Naam" => $_POST["album-toev"] ] );
                App::get( "session" )->setVariable( "page-data", [ "add-album" => $serInd ] );
                return App::redirect( "beheer#albumt-pop-in" );
            }

            /* Make sure important session tags stay set untill specifically unset */
            $impTags = [ "add-album", "new-serie", "edit-serie", "huidige-serie", "Album_Cover", "album-dupl", "album-cover", "isbn-scan", "isbn-search" ];

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
            if( empty( $_SESSION["page-data"]["series"] ) ) {
                App::get( "session" )->setVariable( "page-data", App::get( "series" )->getSeries() );
            }

            /* Store the albums and name for a serie, if the admin is viewing a serie */
            if( !empty( $_POST["serie-index"] ) ) {
                $tempAlbums = App::get( "albums" )->getAlbums( [ "Album_Serie" => $_POST["serie-index"] ] );
                $serName = App::get( "series" )->getSerAtt( "Serie_Naam", [ "Serie_Index" => $_POST["serie-index"] ] );

                /* Set the ablums in the session, or store the correct error for user feedback */
                if( isset( $tempAlbums ) && !isset( $tempAlbums["error"] ) ) {
                    unset( $_SESSION["page-data"]["albums"] );
                    App::get( "session" )->setVariable( "page-data", $tempAlbums );
                } else {
                    App::get( "session" )->setVariable( "header", $tempAlbums );
                }

                /* Set the serie naam in the session, or store the correct error for user feedback */
                if( isset( $serName ) && !isset( $serName["error"] ) ) {
                    App::get( "session" )->setVariable( "page-data", [ "huidige-serie" => $serName ] );
                } else {
                    App::get( "session" )->setVariable( "header", $serName );
                }
                
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
            App::get( "session" )->setVariable( "header", $userCheck );
            return App::redirect( "" );  
        }
    }

    /*  serieM():
            The POST route for '/serieM', for checking series names and creating series.
            Where the latter is related to the pop-in form, and the former to the name input from the controller.
                $userCheck  (Bool/Array)  - The user check based on the stored session data
                $checkName  (Bool/Array)  - Evaluate if the name is duplicate or not
                $sqlData    (Array)       - The POST data prepared for the SQL database
                $store      (Bool/Array)  - The result of the database operation

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
            /* Check if serie-naam is duplicate, and set the correct session data, if not prep name for $sqlData */
            if( isset( $_POST["serie-naam"] ) ) {
                $checkName = App::get( "series" )->serChDup( $_POST["serie-naam"] );

                if( is_array( $checkName ) ) {
                    App::get( "session" )->setVariable( "page-data", [ "serie-dupl" => $_POST ] );
                    App::get( "session" )->setVariable( "header", $checkName );
                    return App::redirect( "beheer#seriem-pop-in" );
                } else {
                    $sqlData = [ "Serie_Naam" => htmlspecialchars( $_POST["serie-naam"] ) ];
                }
            }

            /* Check and store the other POST data, and then attempt to store it in the DB */
            $sqlData["Serie_Maker"] = isset( $_POST["makers"] ) ? htmlspecialchars( $_POST["makers"] ) : "";
            $sqlData["Serie_Opmerk"] = isset( $_POST["opmerking"] ) ? htmlspecialchars( $_POST["opmerking"] ) : "";
            $store = App::get( "series" )->setSerie( $sqlData );

            /* Evaluate the DB store request, and redirect/store feedback information accordingly. */
            if( !is_array( $store ) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "fetchResponse" => "Het toevoegen van: " . $_POST["serie-naam"] . " is gelukt !" ]
                ] );

                unset( $_SESSION["page-data"]["series"] );

                return App::redirect( "beheer" );
            } else {
                App::get( "session" )->setVariable( "header", $store );
                return App::redirect( "beheer" );
            }

        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", $userCheck );
            return App::redirect( "" );  
        }
    }

    /*  serieBew():
            This function deals with editing serie data on the admin page, and stores the changes made.
                $userCheck (Bool/Array)   - The user check based on the stored session data
                $checkName (Bool/Array)   - Evaluate if the name is duplicate or not
                $serieData (Array)        - The POST data prepared for the SQL Database
                $store     (Bool/Array)   - The result of the database operation

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

            /* Check if name is duplicate, and set the correct session data, if not prep name for $serieData */
            if( isset( $_POST["naam"] ) ) {
                $checkName = App::get( "series" )->serChDup( $_POST["naam"], $_POST["index"] );

                if( is_array( $checkName ) ) {
                    App::get( "session" )->setVariable( "page-data", [ "edit-serie" => $_POST["index"] ] );
                    App::get( "session" )->setVariable( "header", $checkName );
                    return App::redirect( "beheer#serieb-pop-in" );
                } else {
                    $serieData["Serie_Naam"] = htmlspecialchars( $_POST["naam"] );
                }
            }

            /* Check and store the other POST data, and then attempt to store it in the DB */
            $serieData["Serie_Maker"] = isset( $_POST["makers"] ) ? htmlspecialchars( $_POST["makers"] ) : "";
            $serieData["Serie_Opmerk"] = isset( $_POST["opmerking"] ) ? htmlspecialchars( $_POST["opmerking"] ) : "";
            $store = App::get( "series" )->setSerie( $serieData, [ "Serie_Index" => $_POST["index"] ] );

            /* Evaluate the DB store request, and redirect/store feedback information accordingly. */
            if( !is_array( $store ) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "fetchResponse" => "Het aanpassen van: " . $_POST["naam"] . " is gelukt !"]
                ] );

                unset( $_SESSION["page-data"]["series"] );

                return App::redirect( "beheer" );
            } else {
                App::get( "session" )->setVariable( "header", $store );
                App::get( "session" )->setVariable( "page-data", [ "edit-serie" => $_POST["index"] ] );

                return App::redirect( "beheer#serieb-pop-in" );
            }

        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", $userCheck );
            return App::redirect( "" );
        }
    }

    /*  serieVerw():
            This removes a serie and all its albums from the database, and gives back user feedback based on that.
                $userCheck (Bool/Array)   - The user check based on the stored session data
                $remove_# (Bool/Array)    - The result of the database operations

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
            $remove_1 = App::get( "albums" )->delAlbum( [ "Album_Serie" => $_POST["serie-index"] ] );
            $remove_2 = App::get( "collection" )->delSerie( [ "Serie_Index" => $_POST["serie-index"], "Serie_Naam" => $_POST["serie-naam"] ] );

            /* Evaluate the DB operation, and return the correct feedback, reset the correct session data and redirect back to the page. */
            if( !is_array( $remove_1 ) && !is_array( $remove_2 ) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "fetchResponse" => "Het verwijderen van: " . $_POST["serie-naam"] . " en alle albums is geslaagd!" ]
                ] );

                unset( $_SESSION["page-data"]["series"] );

                return App::redirect( "beheer" );

            /* Send errors to JS for user feedback, and redirect to the admin page */
            } else {

                if( isset( $remove_1["error"] ) ) {
                    App::get( "session" )->setVariable( "header", [ "error" =>
                        [ "error1" => $remove_1["error"]["fetchResponse"] ]
                    ] );
                }

                if( isset( $remove_2["error"] ) ) {
                    App::get( "session" )->setVariable( "header", [ "error" =>
                        [ "error2" => $remove_2["error"]["fetchResponse"] ]
                    ] );
                }

                return App::redirect( "beheer" );
            }
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", $userCheck );
            return App::redirect( "" );
        }
    }

    /*  albumT():
            This function checks the album name, and either stores that user input, or returns it for the user to correct it.
                $userCheck (Bool/Array)   - The user check based on the stored session data
                $itemCheck (Bool/Array)   - The item duplicate check, based on the item naam and potentially index
                $albumData (Array)        - The POST data prepared for the SQL Database
                $store     (Bool/Array)   - The result of the database operation
            
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

            /* Trigger a duplicate entry check on the album name. */
            if( isset( $_POST["album-naam"] ) ) {
                $itemCheck = App::get( "albums" )->albChDup( $_POST["album-naam"], [ "Album_Serie" => $_POST["serie-index"] ] );
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

                App::get( "session" )->setVariable( "header", $itemCheck );
                App::get( "session" )->setVariable( "page-data", [ "album-dupl" => $returnData ] );
                return App::redirect( "beheer#albumt-pop-in" );

            /* Otherwhise i can store the name for processing it. */
            } else {
                $albumData["Album_Naam"] = htmlspecialchars( $_POST["album-naam"] );
            }

            /* Prep the rest of the album data for SQL */
            $albumData["Album_Serie"] = $_POST["serie-index"];
            $albumData["Album_ISBN"] = ( !empty( $_POST["album-isbn"] ) || $_POST["album-isbn"] !== "" ) ? $_POST["album-isbn"] : 0;
            $albumData["Album_Nummer"] = ( !empty( $_POST["album-nummer"] ) ) ? $_POST["album-nummer"] : 0;
            if( !empty( $_POST["album-datum"] ) ) { $albumData["Album_UitgDatum"] = $_POST["album-datum"]; }
            if( isset( $_POST["album-schrijver"] ) ) { $albumData["Album_Schrijver"] = $_POST["album-schrijver"]; }
            if( isset( $_POST["album-opm"] ) ) { $albumData["Album_Opm"] = $_POST["album-opm"]; }

            /* Album cover loop, for base64 conversion, or re-adding of the one stored in the session */
            if( $_FILES["album-cover"]["error"] === 0 ) {
                $fileName = basename( $_FILES["album-cover"]["name"] );
                $fileType = pathinfo( $fileName, PATHINFO_EXTENSION );
                $image = $_FILES["album-cover"]["tmp_name"];
                $imgContent = file_get_contents($image);
                $dbImage = "data:image/" . $fileType . ";charset=utf8;base64," . base64_encode($imgContent);
                $albumData["Album_Cover"] = $dbImage;

            /* If a cover was passed on via the duplicate entry check, use that blob and remove the session data. */
            } elseif( isset( $_SESSION["page-data"]["album-dupl"]["Album_Cover"] ) ) {
                $albumData["Album_Cover"] = $_SESSION["page-data"]["album-dupl"]["Album_Cover"];
                unset( $_SESSION["page-data"]["album-dupl"] );

            /* If a cover was found using the search feature, store that blob, and remove the session data. */
            } elseif( isset( $_SESSION["page-data"]["isbn-search"]["Album_Cover"] ) ) {
                $albumData["Album_Cover"] = $_SESSION["page-data"]["isbn-search"]["Album_Cover"];
                unset( $_SESSION["page-data"]["isbn-search"] );
            }

            /* Attempt to store the album in the SQL DB */
            $store = App::get( "albums" )->setAlbum( $albumData );

            /* Evaluate the DB store request, and redirect/store feedback information accordingly. */
            if( isset( $store ) && !isset( $store["error"] ) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "fetchResponse" => "Het toevoegen van: " . $_POST["album-naam"] . " is gelukt !" ]
                ] );

                /* Unset specific session page-data or states, to ensure the expected page behavior */
                if( isset( $_SESSION["page-data"]["huidige-serie"] ) ) { unset( $_SESSION["page-data"]["albums"] ); }
                if( isset( $_SESSION["page-data"]["isbn-scan"] ) ) { unset( $_SESSION["page-data"]["isbn-scan"] ); }
                if( isset( $_SESSION["page-data"]["searched"] ) ) { unset( $_SESSION["page-data"]["searched"] ); }

                return App::redirect( "beheer" );
            } else {
                App::get( "session" )->setVariable( "header", $store );
                return App::redirect( "beheer" );
            }

        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", $userCheck );
            return App::redirect( "" );
        }
    }

    /*  albumV():
            The remove album function, with a trigger to repopulate the session data after removal.
                $userCheck (Bool/Array)   - The user check based on the stored session data
                $itemCheck (Bool/Array)   - The item duplicate check, based on the item naam and potentially index
                $store     (Bool/Array)   - The result of the database operation

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
                $itemCheck = App::get( "albums" )->getAlbAtt( "Album_Naam", [ "Album_Index" => $_POST["album-index"] ] );
                $store = App::get( "albums" )->delAlbum( [ "Album_Index" => $_POST["album-index"] ] );
            } else {
                return App::redirect( "beheer" );
            }

            /* Evaluate the itemCheck and DB action. */
            if( !is_array( $itemCheck ) && $store ) {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "fetchResponse" => "Het verwijderen van: " . $itemCheck . " is geslaagd!" ]
                ] );

                unset( $_SESSION["page-data"]["albums"] );

                return App::redirect( "beheer" );
            /* Store the correct error, and redirect accordingly */
            } else {
                if( is_array( $itemCheck ) ) {
                    App::get( "session" )->setVariable( "header", [ "error" => [ "error1" => $itemCheck["error"]["fetchResponse"] ] ] );
                }

                if( is_array( $store ) ) {
                    App::get( "session" )->setVariable( "header", [ "error" => [ "error2" => $store["error"]["fetchResponse"] ] ] );
                }

                return App::redirect( "beheer" );
            }
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", $userCheck );
            return App::redirect( "" );
        }
    }

    // Pending:
        // Waiting for Testing results and or feedback/errors.
    /*  albumBew():
            This function deal with all album-bewerken actions, but does currently cause unwanted page refreshes.
                $userCheck (Bool/Array)   - The user check based on the stored session data
                $itemCheck (Bool/Array)   - The item duplicate check, based on the item naam and potentially index
                $albumData (Array)        - The POST data prepared for the SQL Database
                $store     (Bool/Array)   - The result of the database operation
            
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
            $itemCheck = App::get( "albums" )->albChDup( $_POST["album-naam"], [ "Album_Serie" => $_POST["serie-index"] ], $_POST["album-index"] );

            /* Evaluate itemCheck, and store the error including the album index tag in the session, and redirect to the pop-in. */
            if( is_array( $itemCheck ) ) {
                App::get( "session" )->setVariable( "page-data", [ "album-edit" => $_POST["album-index"] ] );
                App::get( "session" )->setVariable( "header", $itemCheck );
                return App::redirect( "beheer#albumb-pop-in" );
            /* Otherwhise just store the name for the SQL DB. */
            } else {
                $albumData["Album_Naam"] = htmlspecialchars( $_POST["album-naam"] );
            }

            /* Store the remaining data for SQL */
            if( isset( $_POST["album-nummer"] ) ) { $albumData["Album_Nummer"] = $_POST["album-nummer"]; }
            if( !empty( $_POST["album-datum"] ) ) { $albumData["Album_UitgDatum"] = $_POST["album-datum"]; }

            $albumData["Album_Isbn"] = isset( $_POST["album-isbn"] ) ? $_POST["album-isbn"] : 0;
            if( isset( $_POST["album-schrijver"] ) ) { $albumData["Album_Schrijver"] = $_POST["album-schrijver"]; }
            if( isset( $_POST["album-opm"] ) ) { $albumData["Album_Opm"] = $_POST["album-opm"]; }

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

            // COMPATIBILITY: leaving this here incase the issue comes back later.
            //      for some reasons, empty fields were causing problems with the SQL querry.
            // foreach( $albumData as $key => $value ) {
            //     if( empty( $value ) ) {
            //         unset( $albumData[$key] );
            //     }
            // }

            /* Attempt to store the album data in the SQL DB. */
            $store = App::get( "albums" )->setAlbum( $albumData, [
                "Album_Index" => $_POST["album-index"],
                "Album_Serie" => $_POST["serie-index"]
            ] );

            /* Evaluate the DB action, and store the correct response (unset data if required), and redirect back to the admin page. */
            if( isset( $store ) && !isset( $store["error"] ) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" => 
                        [ "fetchResponse" => "Het aanpassen van: " . $_POST["album-naam"] . " is gelukt !" ]
                ] );

                if( isset( $_SESSION["page-data"]["huidige-serie"] ) ) { unset( $_SESSION["page-data"]["albums"] ); }
                if( isset( $_SESSION["page-data"]["album-edit"] ) ) { unset( $_SESSION["page-data"]["album-edit"] ); }

                return App::redirect( "beheer" );
            } else {
                App::get( "session" )->setVariable( "header", $store );

                return App::redirect( "beheer" );
            }
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", $userCheck );
            return App::redirect( "" );
        }
    }

    /*  adminReset():
            This function can reset user passwords, since that is missing from the main page login-pop-in.
                $userCheck (Bool/Array) - The user check based on the stored session data
                $store     (Bool/Array) - The result of the database operation

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
                App::get( "session" )->setVariable( "header", $store );
                return App::redirect( "beheer#ww-reset-pop-in" );
            } else {
                App::get( "session" )->setVariable( "header", [ "feedB" =>
                    [ "fetchResponse" => "Het wachtwoord van: " . $_POST["email"] . " is aangepast !" ]
                ] );

                return App::redirect( "beheer" );
            }
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", $userCheck );
            return App::redirect( "" );
        }
    }

    /* User-Page functions */
    /*  gebruik():
            The POST route for '/gebruik', this is similar to the GET route in the 'PageController'.
                $userCheck (Bool/Array) - The user check based on the stored session data
                $tempSerie (Array)      - To evaluate the database get request
                $tempCol (Array)        - To evaluate the database get request
                $albId (Int/Array)      - Request the id required for getting the series its ablums.
                $tempAbums (Array)      - To evaluate the database get request

            Return Value:
                On sucess   - View      -route-> '../gebruik.view.php'
                On fail     - Redirect  -route-> '../'
     */
    public function gebruik() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"] ) : App::get( "user" )->checkUser( -1 );

        /* Validate the userCheck result, and execute the correct logic. */
        if( !is_array( $userCheck ) ) {
            /* Always unset and reload all series data, for serie selection related functions */
            $tempSerie = App::get( "series" )->getSeries();

            if( isset( $tempSerie ) && !isset( $tempSerie["error"] ) ) {
                unset( $_SESSION["page-data"]["collections"] );
                App::get( "session" )->setVariable( "page-data", $tempSerie );
            } else {
                App::get( "session" )->setVariable( "header", $tempSerie );
            }

            /* If a collection is being viewed, get all albums for that serie, and the user there collection data, before setting the correct flag in the session */
            if( !empty( $_POST["serie_naam"] ) ) {
                /* Unset and reload the user its collection data first */
                $tempColl = App::get( "collecties" )->getCol( [ "Gebr_Index" => $_SESSION["user"]["id"] ] );

                /* If no errors are set, unset the old collection data, and load the newly requested data */
                if( isset( $tempColl ) && !isset( $tempColl["error"] ) ) {
                    unset( $_SESSION["page-data"]["collections"] );
                    App::get( "session" )->setVariable( "page-data", $tempColl );
                /* Store error in the session header tag, for user feedback */
                } else {
                    App::get( "session" )->setVariable( "header", $tempColl );
                }

                /* Then unset and reload all albums from the selected serie */
                $albId = [ "Album_Serie" => App::get( "series" )->getSerAtt( "Serie_Index", [ "Serie_Naam" => $_POST["serie_naam"] ] ) ];

                /* Make sure the id isnt a error, befor requesting the album datas */
                if( !isset( $albId["error"] ) ) {
                    $tempAlbums = App::get( "albums" )->getAlbums( $albId );
                /* store this error as error1 for JS */
                } elseif( isset( $albId["error"] ) ) {
                    App::get( "session" )->setVariable( "header", [ "error" => [ "error1" => $albId["error"]["fetchResponse"] ] ] );
                }

                /* If the album request had an error, store that as error2 for JS */
                if( isset( $tempAlbums["error"] ) ) {
                    App::get( "session" )->setVariable( "header", [ "error" => [ "error1" => $tempAlbums["error"]["fetchResponse"] ] ] );
                /* if the request was done, unset album session data, and store the new data  */
                } elseif( isset( $tempAlbums ) && !isset( $tempAlbums["error"] ) ) {
                    unset( $_SESSION["page-data"]["albums"] );
                    App::get( "session" )->setVariable( "page-data", $tempAlbums );
                }
                
                /* And finally make sure the correct tag is set for the table header */
                App::get( "session" )->setVariable( "page-data", [ "huidige-serie" => $_POST["serie_naam"] ] )  ;
            }

            return App::view("gebruik");
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", $userCheck );
            return App::redirect("");
        }
    }

    /*  albSta():
            The POST route for '/albSta', where we create collection data, based on what album(s) got toggled on/off.
                $userCheck (Bool/Array) - The user check based on the stored session data
                $colData (Array)        - Id's required for setting and removing collection data
                $store (Bool/Array      - The result of the database operation
            
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
            /* Set collection data, and let the collecties class process the request */
            if( isset( $_POST["albumIndex"] ) ) {
                $colData = [
                    "Gebr_Index" => $_SESSION["user"]["id"],
                    "Alb_Index" => $_POST["albumIndex"]
                ];
                
                $store = App::get( "collecties" )->changeCol( $colData );
            }

            /* Check if the process had errors, and set as user feedback */
            if( isset( $store["error"] ) ) {
                App::get( "session" )->setVariable( "header", $store );
            /* If data was added/remove to/from a collection, set the user feedback in the session */
            } elseif( isset( $store["fetchResponse"] ) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" => $store ] );
            }

            /* Always return to the main user page */
            return App::redirect( "gebruik" );
        /* Return the error to JS, and redirect to the landingpage. */
        } else {
            App::get( "session" )->setVariable( "header", $userCheck );
            return App::redirect( "" );
        }
    }

    /*	scan():
            This function simply set the correct session tag, and redirects to the pop-in to load the correct template.
                $userCheck (Bool/Array) - The user check based on the stored session data
                $serInd (Int/Array)     - The serie index of the serie the album should be added to
            
            Return Value:
                On auth failure -redirect-route-> "/"
                On error        -redirect-route-> "/beheer"
                On success      -redirect-route-> "/beheer#albumS-pop-in"
     */
	public function scan() {
		/* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );

		/* Validate the userCheck result, and execute the correct logic. */
		if( !is_array( $userCheck ) ) {
            if( isset( $_POST["album-toev"] ) ) {
                $serInd = App::get( "series" )->getSerAtt( "Serie_Index", [ "Serie_Naam" => $_POST["album-toev"] ] );

                /* Check for errors, and store the error in the session for JS */
                if(isset($serInd["error"])) {
                    App::get( "session" )->setVariable( "header", $serInd );
                /* Else prepare the correct page-data, and redirect to the scan pop-in */
                } else {
                    App::get( "session" )->setVariable( "page-data", [ "serie-index" => $serInd ] );
                    App::get( "session" )->setVariable( "page-data", [ "isbn-scan" => True ] );
                    return App::redirect( "beheer#albumS-pop-in" );
                }
                
                return App::redirect( "beheer" );
            }
        /* Return the error to JS, and redirect to the landingpage. */
		} else {
			App::get( "session" )->setVariable( "header", $userCheck );
			return App::redirect( "" );
		}
    }

    /*  isbn():
            This function attempt to get as much item data as possible, from the Google API, so forms can be pre-filled.
            This works for both the ISBN search function, as the bar-code scanner, though only give ISBN/EAN book information in return.
            When called from a album-edit pop-in, it will attempt to add the old data back, if nothing was found in the API
                $userCheck (Bool/Array) - The user check based on the stored session data
                $result (Array)         - The parsed data from the Google Books API
            
            Return Value:
                On auth failure -redirect-route-> "/"
                On error        -redirect-route-> "/beheer"
                Multy Items     -redirect-route-> "/beheer#isbn-preview"
                On success      -redirect-route-> "/beheer#albumt-pop-in" or "/beheer#albumb-pop-in"
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

                /* If there is a data array returned, and the first index is called titles, i store that data in the session, and return to a user choice pop-in. */
                if( isset( $result[0] ) ) {
                    if( $result[0] === "Titles" ) {
                        $_SESSION["page-data"]["show-titles"] = $result;

                        /* Set tags for the album edit variation of this */
                        if( isset( $_POST["album-index"] ) ) { $_SESSION["page-data"]["add-album"] = $_POST["album-index"]; }
                        if( isset( $_POST["album-nummer"] ) ) { $_SESSION["page-data"]["temp-alb-nr"] = $_POST["album-nummer"]; }

                        /* Store a tag to see what pop-in was used. */
                        if( isset( $_POST["serie-index"] ) && isset( $_POST["album-index"] ) ) {
                            $_SESSION["page-data"]["shown-titles"]["bewerken"] = true;
                        } else {
                            $_SESSION["page-data"]["shown-titles"]["toevoegen"] = true;
                        }

                        return App::redirect( "beheer#isbn-preview" );
                    }
                }

                /* Data that isnt provided via the Google API, and should be there by default in the POST. */
                if( isset( $_POST["album-index"] ) ) { $result["Album_Index"] = $_POST["album-index"]; }
                if( isset( $_POST["serie-index"] ) ) { $result["Album_Serie"] = $_POST["serie-index"]; }

                /* Data that can only be user input, only applies for editing albums. */
                if( isset( $_POST["album-nummer"] ) ) { $result["Album_Nummer"] = $_POST["album-nummer"]; }

                /* Data that need to be taken from the Google API search if avaible, otherwhise take the user input, only applies for editing albums. */
                if( !isset( $result["Album_Naam"] ) && !empty( $_POST["album-naam"] ) ) { $result["Album_Naam"] = $_POST["album-naam"]; }
                if( !isset( $result["Album_UitgDatum"] ) && !empty( $_POST["album-datum"] ) ) { $result["Album_UitgDatum"] = $_POST["album-datum"]; }
                if( !isset( $result["Album_Opm"] ) && !empty( $_POST["album-opm"] ) ) { $result["Album_Opm"] = $_POST["album-opm"]; }
                if( !isset( $result["Album_Schrijver"] ) && !empty( $_POST["album-schrijver"] ) ) { $result["Album_Schrijver"] = $_POST["album-schrijver"]; }

            /* Get the user choices if more then 1 item was detected, and store that as a result for processing. */
            } elseif( isset( $_POST["isbn-choice"] ) && isset( $_POST["title-choice"] ) ) {
                $result = App::get( "isbn" )->get_data( $_POST["isbn-choice"], null, $_POST["title-choice"] );
                $result["Album_Serie"] = $_POST["serie-index"];
            }

            /* Evaluate the result, and prepare the correct feedback and page-data, on error we redirect back to the admin page. */
            if( !empty( $result ) ) {
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

                if( isset( $_SESSION["page-data"]["add-album"] ) ) {
                    $result["Album_Index"] = $_SESSION["page-data"]["add-album"];
                    unset( $_SESSION["page-data"]["add-album"] );
                }

                if( isset( $_SESSION["page-data"]["temp-alb-nr"] ) ) {
                    $result["Album_Nummer"] = $_SESSION["page-data"]["temp-alb-nr"];
                    unset( $_SESSION["page-data"]["temp-alb-nr"] );
                }

                if( isset( $result["Album_Cover"] ) ) {
                    $_SESSION["page-data"]["Album_Cover"] = $result["Album_Cover"];
                }

                /* If the scan tag is set, we prep the data for the related pop-ins */
                if( isset( $_SESSION["page-data"]["isbn-scan"] ) ) {
                    App::get( "session" )->setVariable( "page-data", [ "isbn-scan" => $result ] );
                    App::get( "session" )->setVariable( "header", [ "broSto" => [ "isbnScan" => TRUE ] ] );
                /* If that tag wasnt set, its going the be manual isbn search.  */
                } else {
                    App::get( "session" )->setVariable( "page-data", [ "isbn-search" => $result ] );
                    App::get( "session" )->setVariable( "header", [ "broSto" => [ "isbnSearch" => TRUE ] ] );
                    App::get( "session" )->setVariable( "page-data", [ "searched" => TRUE ] );
                }

                /* Check what pop-in got us here, using POST data, and return (redirect) to the correct pop-in */
                if( isset( $_SESSION["page-data"]["shown-titles"]["bewerken"] ) ) {
                    unset( $_SESSION["page-data"]["shown-titles"] );
                    return App::redirect( "beheer#albumb-pop-in" );
                } elseif( isset( $_SESSION["page-data"]["shown-titles"]["toevoegen"] ) ) {
                    unset( $_SESSION["page-data"]["shown-titles"] );
                    return App::redirect( "beheer#albumt-pop-in" );
                }
            }
        /* When authentication fails, store the error, and return to the landingpage. */
		} else {
			App::get( "session" )->setVariable( "header", $userCheck );
			return App::redirect( "" );
		}
    }

    /*  userScan():
            This function set the correct tags, and gets the correct data, for the scanner form data.
                $userCheck (Bool/Array) - The user check based on the stored session data

            Return Value:
                On auth failure -redirect-route-> "/"
     */
    public function userScan() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get("user")->checkUser( $_SESSION["user"]["id"] ) : App::get("user")->checkUser( -1 );
        /* If user is verified, convert the serie name in the session, to a serie index */
		if( !is_array( $userCheck ) ) {
            App::get( "session" )->setVariable( "page-data",
                [ "serie-index" => App::get( "series" )->getSerAtt( "Serie_Index", [ "Serie_Naam" => $_SESSION["page-data"]["huidige-serie"] ] ) ]
            );
            /* Set the scan tag to true, and redirect to the scan pop-in */
            App::get( "session" )->setVariable( "page-data", [ "isbn-scan" => True ] );
            return App::redirect( "gebruik#albumS-pop-in" );
        /* If user is not verified, store a error for the header, and redirect to landingpage */
		} else {
			App::get( "session" )->setVariable( "header", $userCheck );
			return App::redirect( "" );
		}
    }

    /*  userIsbn():
            This function checks the isbn provided by userScan(), against album data from the selected serie.
            If a match is found, it will attempt to add or remove the item from the collection, and returns feedback to the user about this process.
                $userCheck  (Bool/Array)    - The outcome of the user evalulation
                $albId      (Int/Array)     - The outcome of requesting a album index
                $colIds     (Array)         - The collection of identifiers that i need to make/delete a collection item
                $store      (Array)         - The outcome of trying to change a item its collection status

            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Success                  - Redirect -route-> '/gebruik'
     */
    public function userIsbn() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        $userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"] ) : App::get( "user" )->checkUser( -1 );

        /* Validate the userCheck result, and execute the correct logic. */
		if( !is_array( $userCheck ) ) {
            /* If there is a serie-index and a album-isbn, try to get extra data from the google API */
            if( isset( $_POST["serie-index"] ) && isset( $_POST["album-isbn"] ) ) {
                $albId = App::get( "albums" )->getAlbAtt( "Album_Index", [ "Album_ISBN" => $_POST["album-isbn"] ], [ "Album_Serie" => $_POST["serie-index"] ] );

                /* Check if a album index was found, and change its collection status */
                if( isset( $albId ) && !is_array( $albId ) ) {
                    $colIds = [ "Gebr_Index" => $_SESSION["user"]["id"], "Alb_Index" => $albId ];
                    $store = App::get( "collecties" )->changeCol( $colIds );
                /* If no index was found, the album is not part of the selected serie */
                } else {
                    App::get( "session" )->setVariable( "header", [ "error" =>
                        [ "fetchResponse" => "Gescande albums, is niet gevonden in de huidige serie, controleer of u de juiste serie bekijkt!" ] ] );
                }
            }

            if( isset( $store["error"] ) ) {
                App::get( "session" )->setVariable( "header", $store );
            } elseif( isset( $store["fetchResponse"] ) ) {
                App::get( "session" )->setVariable( "header", [ "feedB" => $store ] );
            }

            /* Remove any collection data, so the changes are re-loaded */
            if( isset( $_SESSION["page-data"]["colllections"] ) ) { unset( $_SESSION["page-data"]["colllections"] ); }
            /* Unset any leftover session states, to prevent broken page logic */
            if( isset( $_SESSION["page-data"]["serie-index"] ) ) { unset( $_SESSION["page-data"]["serie-index"] ); }
            if( isset( $_SESSION["page-data"]["isbn-scan"] ) ) { unset( $_SESSION["page-data"]["isbn-scan"] ); }

            /* Redirect to the user page, to reflect the changes. */
            return App::redirect( "gebruik" );
        /* If user is not verified, store a error for the header, and redirect to landingpage */
		} else {
			App::get( "session" )->setVariable( "header", $userCheck );
			return App::redirect( "" );
		}
    }

    // W.I.P. (working atm, but its alos just a draft/example)
    /*  loadDetails():
            This function requests album data, based on what element was clicked, and return a string so JS knows if said data was stored.
            This is all JS fetch based, so the page-reloads etc are handled there.
            
            Return Value: String
     */
    function loadDetails() {
        /* Check if formdata was recieved properly */
        if( isset( $_POST["album-index"] ) ) {
            $_SESSION["page-data"]["mobile-details"] = App::get( "database" )->selectAllWhere( "albums", [ "Album_Index" => $_POST["album-index"] ] )[0];
            echo "display";
        } else {
            echo "request failed!";
        }
    }
}
?>