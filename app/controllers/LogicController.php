<?php
//  TODO: Finish\clean-up comments.
//  TODO: Add a JS event to the password input (register user), it only checks the confirm input atm.
namespace App\Controllers;

use App\Core\App;

/* LogicController Class:
        In this class i need to deal with a mix of request, all main request are done via HTML form submits.
        But in certain cases i also opted to use JS fetch, to remove a page reload from the user experience.

        $data (Multi-Dimensional Array)      - Intended for data required for displaying a webpage/data.
            'header' (Associative Array)     - Data to be injected into the page header, like JS data for the browser storage, redirects etc.
            'series'  (Associative Array)    - Data used by PhP to display series related information in HTML tables.
            'albums'  (Associative Array)    - Data used by PhP to display albums related information in HTML tables.
            'collecties' (Associative Array) - Data used by PhP to display if the user has a ablum in its collection.
 */
class LogicController {
    /* Global returning error messages for user feedback */
    protected $authFailed = [ "fetchResponse" => "Access denied, Account authentication failed !" ];
    protected $dupError = [ "fetchResponse" => "Deze naam bestaat al, gebruik een andere naam gebruiken !" ];
    protected $dbError = [ "fetchResponse" => "Er was een database error, neem contact op met de administrator als dit blijft gebeuren!" ];
    protected $userAdd = [ "userCreated" => "Gebruiker aangemaakt, u kunt nu inloggen!" ];

    /* Landingpage functions */
    /*  dbCreation():
            The function linked to the landingpage route, if no database tables where set this triggers.
            And it creates all tables and the default admin account, before redirecting back to the landingpage.

            Return Value    - Redirect -route-> '/'
     */
    public function dbCreation() {
        App::get("database")->createTable("gebruikers");
        App::get("database")->createAdmin();
        App::get("database")->createTable("series");
        App::get("database")->createTable("serie_meta");
        App::get("database")->createTable("albums");
        App::get("database")->createTable("collecties");

        return App::redirect("");
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

        $store = App::get("user")->setUser($temp);

        if($store === TRUE) {
            App::get("session")->setVariable("header", [ "feedB" => $this->userAdd ] );

            return App::redirect( "#login-pop-in" );
        } else {
            App::get("session")->setVariable( "header", $store );

            return App::redirect( "#account-maken-pop-in" );
        }
    }

    /*  login():
            The POST route for the login process, where the user class is used to validate the user.
            And where the SESSION data is set, linking a user to a session, so we can verify the user later on.
                $pw (string)    - The password input from the user.
                $cred (string)  - The user credentials (e-mail or user name).
            
            Return Value (redirect):
                If validated as Admin   - Redirect -route-> '/beheer'
                If validated as User    - Redirect -route-> '/gebruik'
                If validation failed    - Redirect -route-> '/#login-pop-in'
     */
    public function login() {
        $pw = $_POST["wachtwoord"];
        $cred = htmlspecialchars( $_POST["accountCred"] );

        if(App::get("user")->validateUser( $cred, $pw ) == 1) {
            App::get("session")->setVariable( "user", [ "id" => App::get("user")->getUserId() ] );

            if(App::get("user")->evalUser() === 1) {
                App::get("session")->setVariable( "user", [ "admin" => FALSE ] );
                App::get("session")->setVariable( "header", [ "feedB" => [ 'welcome' => "Welcome " . App::get("user")->getUserName() ] ] );

                return App::redirect("gebruik");

            } elseif(App::get("user")->evalUser() === 0) {
                App::get("session")->setVariable( "user", [ "admin" => TRUE ] );
                App::get("session")->setVariable( "header", [ "feedB" => [ "welcome" => "Welcome " . App::get("user")->getUserName() ] ] );

                return App::redirect("beheer");

            } else {
                App::get("session")->setVariable( "header", [ "error" => App::get("user")->evalUser() ] );

                return App::redirect("#login-pop-in");
            }
        } else {
            App::get("session")->setVariable("header", [ "error" => App::get("user")->validateUser($cred, $pw) ] );

            return App::redirect("#login-pop-in");
        }
    }

    /*  logout():
            The POST '/logout' route, cleaning and ending the user session, before redirecting to home.

            Return Value    - Redirect -route-> '/'
     */
    public function logout() {
        App::get("session")->endSession();
        App::redirect("");
    }

    /* Adminpage functions */
    /*  beheer():
            The POST route for '/beheer', this is similar to the GET route in the 'PageController'.
            But here is also deal with loading the Series view, and thus loading all related albums.

                $authFailed (Assoc Array)   - Error message for when the user din't validate, using the session data.
                $dupError (String)          - Error message for when a serie name is already stored in the database.
            
            Return Type:
                On Validation fail          - Redirect  -route-> '/'
                On Name check fail          - Redirect  -route-> '/beheer'
                On Name check pass          - Redirect  -route-> '/beheer#seriem-pop-in'
                On pop-in close             - View      -route-> '/beheer.view.php'
                On Album add trigger        - Redirect  -route-> '/beheer#albumt-pop-in'
                In all other cases          - View      -route-> '/beheer.view.php' 
                
     */
    public function beheer() {
        /* Validate user in the session, and execute the correct logic */
        if( isset( $_SESSION["user"]["id"] ) && App::get("user")->checkUSer( $_SESSION["user"]["id"], "rights") ) {
            /* Skip all other logic if a pop-in is closed */
            if( isset( $_POST["close-pop-in"] ) && isset( $_SESSION["page-data"]["huidige-serie"] ) ) { return App::view("beheer"); }

            /* Check for duplicate serie names, when opening the serie-maken pop-in */
            if( isset( $_POST["newSerName"] ) ) {
                if( App::get("collection")->cheSerName( $_POST["newSerName"] ) ) {
                    App::get("session")->setVariable( "header", [ "error" => $this->dupError ] );

                    return App::redirect("beheer");
                } else {
                    App::get("session")->setVariable( "page-data", [ "new-serie" => $_POST["newSerName"] ] );
                    return App::redirect("beheer#seriem-pop-in");
                }
            }

            /* Add session tag, for the album-toevoegen pop-in */
            if( isset($_POST["album-toev"] ) ) {
                App::get("session")->setVariable( "page-data", [ "add-album" => App::get("collection")->getSerInd( $_POST["album-toev"] ) ] );

                return App::redirect("beheer#albumt-pop-in");
            }

            /* Make sure important session tags stay set untill specifically unset */
            if( !App::get("session")->checkVariable( "page-data", [ "add-album", "new-serie", "edit-serie", "huidige-serie", "album-dupl", "album-cover" ] ) ) {
                unset($_SESSION["page-data"]);
            }

            /* Clear series session data when returning to the admin serie-view */
            if( isset( $_POST["return"] ) ) {
                unset( $_SESSION["page-data"]["huidige-serie"] );
                unset( $_SESSION["page-data"]["series"] );

                return App::redirect("beheer");
            }

            /* Populate the session series data is there is non */
            if( empty($_SESSION["page-data"]["series"] ) ) { App::get("session")->setVariable( "page-data", App::get("collection")->getSeries() ); }

            /* Store the albums and name for a serie, if the admin is viewing a serie */
            if( !empty( $_POST["serie-index"] ) ) {
                App::get("session")->setVariable( "page-data", App::get("collection")->getAlbums( $_POST["serie-index"] ) );
                App::get("session")->setVariable( "page-data", [ "huidige-serie" => App::get("collection")->getItemName( "serie", $_POST["serie-index"] ) ] );

                return App::redirect("beheer");
            }

            /* Store serie index, if the admin is editing a serie */
            if( isset( $_POST["serie-edit-index"] ) ) {
                App::get("session")->setVariable( "page-data", [ "edit-serie" => $_POST["serie-edit-index"] ] );

                return App::redirect("beheer#serieb-pop-in");
            }

            /* Fail-save for unexpected behavior */
            return App::view("beheer");
        } else {
            App::get("session")->setVariable( "header", [ "error" => $this->authFailed ] );

            return App::redirect("");
        }
    }

    //  TODO: Review the JS depended codeing, the serie-naam loop.
    // Refactor: Paused
    /*  serieM():
            The POST route for '/serieM', for checking series names and creating series.
            Where the latter is related to the pop-in form, and the former to the name input from the controller.

                $authFailed (Assoc Array)   - Error message for when the user din't validate, using the session data.
                $dupError (String)          - Error message for when a serie name is already stored in the database.

            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed name check        - Redirect -route-> '/beheer#seriem-pop-in'
                On Failed Database action   - Redirect -route-> '/beheer'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function serieM() {
        if( isset( $_SESSION["user"]["id"] ) && App::get("user")->checkUSer( $_SESSION["user"]["id"], "rights" ) ) {
            if( isset( $_POST["serie-naam"] ) ) {
                if( App::get("collection")->cheSerName( $_POST["serie-naam"] ) ) {
                    //die(var_dump(print_r($_POST)));       //debugline
                    App::get("session")->setVariable( "header", [ "broSto" => $_POST ] );           // Needs to be stored in session, instead of the browser storage.
                    App::get("session")->setVariable( "header", [ "error" => $this->dupError ] );
                    
                    return App::redirect("beheer#seriem-pop-in");

                } else { $sqlData = [ "Serie_Naam" => htmlspecialchars( $_POST["serie-naam"] ) ]; }
            }

            $sqlData["Serie_Maker"] = isset( $_POST["makers"] ) ? htmlspecialchars( $_POST["makers"] ) : "";
            $sqlData["Serie_Opmerk"] = isset( $_POST["opmerking"] ) ? htmlspecialchars( $_POST["opmerking"] ) : "";

            $store = App::get("collection")->setSerie( $sqlData );

            if( !is_string($store) ) {
                App::get("session")->setVariable( "header", [ "feedB" => [ "fetchResponse" => "Het toevoegen van: " . $_POST["serie-naam"] . " is gelukt !" ] ] );
                unset( $_SESSION["page-data"]["series"] );
                return App::redirect("beheer");
            } else {
                App::get("session")->setVariable( "header", [ "error" => [ "fetchResponse" => $this->dbError ] ] );
                return App::redirect("beheer");
            }
        } else {
            App::get("session")->setVariable( "header", [ "error" => $this->authFailed ] );
            return App::redirect("");
        }
    }

    /*  serieBew():
            This function deals with editing serie data on the admin page, and stores the changes made.

            $serieData (Assoc Array) :
     */
    public function serieBew() {
        if( isset( $_SESSION["user"]["id"] ) && App::get("user")->checkUSer( $_SESSION["user"]["id"], "rights" ) ) {
            if( isset( $_POST["naam"] ) ) {
                if( App::get("collection")->cheSerName( $_POST["naam"], $_POST["index"] ) ) {
                    App::get("session")->setVariable( "page-data", [ "edit-serie" => $_POST["index"] ] );
                    App::get("session")->setVariable( "header", [ "error" => $this->dupError ] );
                    return App::redirect("beheer#serieb-pop-in");
                } else { $serieData["Serie_Naam"] = $_POST["naam"]; }
            }

            $serieData["Serie_Maker"] = isset( $_POST["makers"] ) ? $_POST["makers"] : "";
            $serieData["Serie_Opmerk"] = isset( $_POST["opmerking"] ) ? $_POST["opmerking"] : "";

            $store = App::get("collection")->setSerie( $serieData, $_POST["index"] );

            if( is_string( $store ) ) {
                App::get("session")->setVariable( "header", [ "error" => $this->dbError ] );
                App::get("session")->setVariable( "page-data", [ "edit-serie" => $_POST["index"] ] );
                return App::redirect("beheer#serieb-pop-in");
            } else {
                App::get("session")->setVariable( "header", [ "feedB" => [ "fetchResponse" => "Het aanpassen van: " . $_POST["naam"] . " is gelukt !"] ] );
                return App::redirect("beheer");
            }
        } else {
            App::get("session")->setVariable( "header", [ "error" => $this->authFailed ] );
            return App::redirect("");  
        }
    }

    /*  serieVerw():
            This removes a serie and all its albums from the database, and gives back user feedback based on that.
                $authFailed (Assoc Array)   - Error message for when the user din't validate, using the session data.
                $dbError (Assoc Array)      - Error message for when there are database errors.
                $remove_# (String or Bool)  - Temp store for the results of the requested remove action.

            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed Database action   - Redirect -route-> '/beheer'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function serieVerw() {
        if( isset( $_SESSION["user"]["id"] ) && App::get("user")->checkUSer( $_SESSION["user"]["id"], "rights" ) ) {
            $remove_1 = App::get("collection")->remItem( "albums", [ "Album_Serie" => $_POST["serie-index"] ] );
            $remove_2 = App::get("collection")->remItem( "series", [ "Serie_Index" => $_POST["serie-index"], "Serie_Naam" => $_POST["serie-naam"] ] );

            if( is_string( $remove_1 ) || is_string( $remove_2 ) ) {
                App::get("session")->setVariable( "header", [ "error" => $this->dbError ] );
                return App::redirect("beheer");
            } else {
                App::get("session")->setVariable( "header", [ "feedB" => [ "fetchResponse" => "Het verwijderen van: " . $_POST["serie-naam"] . " en alle albums is geslaagd!" ] ] );
                unset( $_SESSION["page-data"]["series"] );
                return App::redirect("beheer");
            }
        } else {
            App::get("session")->setVariable( "header", [ "error" => $this->authFailed ] );
            return App::redirect("");
        }
    }

    /*  albumT():
            This function checks the album name, and either stores that user input, or returns it for the user to correct it.
                $authFailed (Assoc Array)   - Error message for when the user din't validate, using the session data.
                $dupError (Assoc Array)     - Error message for when the album name is duplicate within that series.
                $dbError (Assoc Array)      - Error message for when there are database errors.
                $albumData (Assoc Array)    - The data that needs to be stored in the database.
            
            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed Database action   - Redirect -route-> '/beheer'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function albumT() {
        if( isset( $_SESSION["user"]["id"] ) && App::get("user")->checkUSer( $_SESSION["user"]["id"], "rights" ) ) {
            if(App::get("collection")->cheAlbName( $_POST["serie-index"], $_POST["album-naam"] ) ) {
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
                
                App::get("session")->setVariable( "header", [ "error" => $this->dupError ] );
                App::get("session")->setVariable( "header", [ "broSto" => [ "event" => "album-maken" ] ] );
                App::get("session")->setVariable( "page-data", [ "album-dupl" => $returnData ] );
                return App::redirect("beheer#albumt-pop-in");
            } else { $albumData["Album_Naam"] = htmlspecialchars( $_POST["album-naam"] ); }

            $albumData["Album_Opm"] = "W.I.P.";
            $albumData["Album_Serie"] = $_POST["serie-index"];
            $albumData["Album_ISBN"] = ( !empty( $_POST["album-isbn"] ) || $_POST["album-isbn"] !== "" ) ? $_POST["album-isbn"] : 0;
            $albumData["Album_Nummer"] = ( !empty( $_POST["album-nummer"] ) ) ? $_POST["album-nummer"] : 0;
            if( !empty( $_POST["album-datum"] ) ) { $albumData["Album_UitgDatum"] = $_POST["album-datum"]; }

            /* Album cover loop, for base64 conversion, or re-adding of the one store in the session */
            if( $_FILES["album-cover"]["error"] === 0 ) {
                $fileName = basename( $_FILES["album-cover"]["name"] );
                $fileType = pathinfo( $fileName, PATHINFO_EXTENSION );
                $image = $_FILES["album-cover"]["tmp_name"];
                $imgContent = file_get_contents($image);
                $dbImage = "data:image/" . $fileType . ";charset=utf8;base64," . base64_encode($imgContent);
                $albumData["Album_Cover"] = $dbImage;
            } elseif( isset( $_SESSION["page-data"]["album-dupl"]["album-cover"] ) ) { $albumData["Album_Cover"] = $_SESSION["page-data"]["album-dupl"]["album-cover"]; }

            $store = App::get("collection")->setAlbum( $albumData );

            if( is_string( $store ) ) {
                App::get("session")->setVariable( "header", [ "error" => $this->dbError ] );
                return App::redirect("beheer");
            } else {
                App::get("session")->setVariable( "header", [ "feedB" => [ "fetchResponse" => "Het toevoegen van: " . $_POST["album-naam"] . " is gelukt !" ] ] );

                /* Unset specific session page-data or states, to ensure the expected page behavior */
                if( isset( $_SESSION["page-data"]["huidige-serie"] ) ) { unset( $_SESSION["page-data"]["albums"] ); }
                if( isset( $_SESSION["page-data"]["album-dupl"] ) ) { unset( $_SESSION["page-data"]["album-dupl"] ); }
                if( isset( $_SESSION["page-data"]["add-album"] ) ) { unset( $_SESSION["page-data"]["add-album"] ); }

                return App::redirect("beheer");
            }
        } else {
            App::get("session")->setVariable( "header", [ "error" => $this->authFailed ] );
            return App::redirect("");  
        }
    }

    /*  albumV():
            The remove album function, with a trigger to repopulate the session data after removal.
                $authFailed (Assoc Array)   - Error message for when the user din't validate, using the session data.
                $dbError (Assoc Array)      - Error message for when there are database errors.
                $albName (String)           - Temp storage for the album name that is being removed, for user feedback reasons.
                $store (Boolean/String)     - Temp storage for validation if the remove action had an error or not.

            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed Database action   - Redirect -route-> '/beheer'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function albumV() {
        if( isset( $_SESSION["user"]["id"] ) && App::get("user")->checkUSer( $_SESSION["user"]["id"], "rights" ) ) {
            if( isset( $_POST["album-index"] ) ) {
                $albName = App::get("collection")->getItemName( "album", $_POST["serie-index"], $_POST["album-index"] );
                $store = App::get("collection")->remItem( "albums", [ "Album_Index" => $_POST["album-index"] ] );

                if(is_string($store)) {
                    App::get("session")->setVariable( "header", [ "error" => $this->dbError ]);
                    return App::redirect("beheer");
                } else {
                    App::get("session")->setVariable( "header", [ "feedB" => [ "fetchResponse" => "Het verwijderen van: " . $albName . " is geslaagd!" ] ] );
                    unset( $_SESSION["page-data"]["albums"] );
                    return App::redirect("beheer");
                }
            } else { return App::redirect("beheer"); }
        } else {
            App::get("session")->setVariable( "header", [ "error" => $this->authFailed ] );
            return App::redirect("");  
        }
    }

    /*  albumBew():
            This function deal with all album-bewerken actions, but does currently cause unwanted page refreshes.
                $authFailed (Assoc Array)   - Error message for when the user din't validate, using the session data.
                $dbError (Assoc Array)      - Error message for when there are database errors.
                $dupError (Assoc Array)     - Error message for when the album name is duplicate within that series.
                $store (Boolean/String)     - Temp storage for validation if the remove action had an error or not.
            
            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed Database action   - Redirect -route-> '/beheer'
                On Album Edit Request       - Redirect -route-> '/beheer#albumb-pop-in'
                On Duplicate Name Check     - Redirect -route-> '/beheer#albumb-pop-in'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function albumBew() {
        if( isset( $_SESSION["user"]["id"] ) && App::get("user")->checkUSer( $_SESSION["user"]["id"], "rights" ) ) {
            if( isset( $_POST["albumEdit"] ) ) {
                App::get("session")->setVariable( "page-data", [ "album-edit" => $_POST["albumEdit"] ] );
                return App::redirect("beheer#albumb-pop-in");
            }

            if(App::get("collection")->cheAlbName( $_POST["serie-index"], $_POST["album-naam"], TRUE ) ) {
                App::get("session")->setVariable( "page-data", [ "album-edit" => $_POST["album-index"] ] );
                App::get("session")->setVariable( "header", [ "error" => $this->dupError ] );
                return App::redirect("beheer#albumb-pop-in");
            } else { $albumData["Album_Naam"] = htmlspecialchars( $_POST["album-naam"] ); }

            $albumData["Album_Index"] = $_POST["album-index"];
            if( isset( $_POST["album-nummer"] ) ) { $albumData["Album_Nummer"] = $_POST["album-nummer"]; }
            if( !empty( $_POST["album-datum"] ) ) { $albumData["Album_UitgDatum"] = $_POST["album-datum"]; }
            $albumData["Album_Isbn"] = isset( $_POST["album-isbn"] ) ? $_POST["album-isbn"] : 0;
            $albumData["Album_Opm"] = isset( $_POST["album-opm"] ) ? $_POST["album-opm"] : 'W.I.P';

            if( $_FILES["album-cover"]["error"] === 0 ) {
                $fileName = basename( $_FILES["album-cover"]["name"] );
                $fileType = pathinfo( $fileName, PATHINFO_EXTENSION );
                $image = $_FILES["album-cover"]["tmp_name"];
                $imgContent = file_get_contents($image);
                $dbImage = "data:image/" . $fileType . ";charset=utf8;base64," . base64_encode($imgContent);
                $albumData["Album_Cover"] = $dbImage;
            }

            $store = App::get("collection")->setAlbum( $albumData, [ "Album_Index" => $_POST["album-index"], "Album_Serie" => $_POST["serie-index"] ] );

            if( is_string( $store ) ) {
                App::get("session")->setVariable( "header", [ "error" => $this->dbError ] );
                return App::redirect("beheer");
            } else {
                App::get("session")->setVariable( "header", [ "feedB" => [ "fetchResponse" => "Het aanpassen van: " . $_POST["album-naam"] . " is gelukt !" ] ] );
                if( isset( $_SESSION["page-data"]["huidige-serie"] ) ) { unset( $_SESSION["page-data"]["albums"] ); }
                if( isset( $_SESSION["page-data"]["album-edit"] ) ) { unset( $_SESSION["page-data"]["album-edit"] ); }
                return App::redirect("beheer");
            }
        } else {
            App::get("session")->setVariable( "header", [ "error" => $this->authFailed ] );
            return App::redirect("");
        }
    }

    /*  adminReset():
            This function can reset user passwords, since that is missing from the main page login-pop-in.
                $authFailed (Assoc Array)   - Error message for when the user din't validate, using the session data.
                $dbError (Assoc Array)      - Error message for when there are database errors.
                $store (Boolean/String)     - Temp storage for validation if the remove action had an error or not.

            Return Value:
                On Validation fail          - Redirect -route-> '/'
                On Failed Database action   - Redirect -route-> '/beheer#ww-reset-pop-in'
                On Success                  - Redirect -route-> '/beheer'
     */
    public function adminReset() {
        if( isset( $_SESSION["user"]["id"] ) && App::get("user")->checkUSer( $_SESSION["user"]["id"], "rights" ) ) {
            $store = App::get("user")->updateUser( "gebruikers", [ "Gebr_WachtW" => password_hash( $_POST["wachtwoord1"], PASSWORD_BCRYPT ) ], [ "Gebr_Email" => $_POST["email"] ] );

            if($store) {
                App::get("session")->setVariable( "header", [ "feedB" => [ "fetchResponse" => "Het wachtwoord van: " . $_POST["email"] . " is aangepast !" ] ] );
                return App::redirect("beheer");
            } else {
                App::get("session")->setVariable( "header", [ "error" => $this->dbError ] );
                return App::redirect("beheer#ww-reset-pop-in");
            }
        } else {
            App::get("session")->setVariable( "header", [ "error" => $this->authFailed ] );
            return App::redirect("");
        }
    }

    /* User-Page functions */
    /*  gebruik():
            The POST route for '/gebruik', this is similar to the GET route in the 'PageController'.
            In this case though, we also need to load albums and collections, to display when a serie is selected.
                $authFailed (Assoc Array)   - Error message for when the user din't validate, using the session data.

            Return Value:
                On sucess   - View      -route-> '../gebruik.view.php'
                On fail     - Redirect  -route-> '../'
     */
    public function gebruik() {
        if( isset( $_SESSION["user"]["id"] ) && App::get("user")->checkUSer( $_SESSION["user"]["id"] ) ) {
            unset( $_SESSION["page-data"]["collections"] );
            App::get("session")->setVariable( "page-data", App::get("collection")->getSeries() );
            App::get("session")->setVariable( "page-data", App::get("collection")->getColl( "collecties", [ "Gebr_Index" => $_SESSION["user"]["id"] ] ) );
            if( !empty( $_POST["serie_naam"] ) ) {
                App::get("session")->setVariable( "page-data", App::get("collection")->getAlbums( App::get("collection")->getSerInd( $_POST["serie_naam"] ) ) );
                App::get("session")->setVariable( "page-data", App::get("collection")->getColl( "collecties", [ "Gebr_Index" => $_SESSION["user"]["id"] ] ) );
                App::get("session")->setVariable( "page-data", [ "huidige-serie" => $_POST["serie_naam"] ] )  ;
            }
            return App::view("gebruik");
        } else {
            App::get("session")->setVariable( "header", [ "error" => $this->authFailed ] );
            return App::redirect("");
		}
    }

    /*  albSta():
            The POST route for '/albSta', where we create collection data, based on what album(s) got toggled on/off.
                $authFailed (Assoc Array)   - Error message for when the user din't validate, using the session data.
                $collErr (Assoc Array)      - Error for when a album was already added, can be triggered by page-refreshes or browser navigation.
                $collComp (Assoc Array)     - Album added feedback message.
                $colRemo (Assoc Array)      - Album removed feedback message.
            
            Return Value: JSON encoded data, for the JS fetch request.

            Temp Reminder for the expected values/states:
                checkState:
                    false - album is currently no present.
                    true - album is currently present.
                store:
                    is_string = error
                    true && !is_string = album added/remove to/from collection.
                    false && !is_string = album was already added to the collection (odd page refreshes and tampered post data).
     */
    public function albSta() {
        if( isset( $_SESSION["user"]["id"] ) && App::get("user")->checkUSer( $_SESSION["user"]["id"] ) ) {
            if( isset( $_POST["albumIndex"] ) ) { $ids = [ "Gebr_Index" =>  $_SESSION["user"]["id"], "Alb_Index" => $_POST["albumIndex"] ]; }
            
            if( isset( $_POST["checkState"] ) && $_POST["checkState"] === "false" ) {
                $store = App::get("collection")->setColl( "collecties", $ids );
            } else if ( isset( $_POST["checkState"] ) && $_POST["checkState"] === "true" ) {
                $store = App::get("collection")->remItem( "collecties", $ids );
            } else { $store = "No valid POST data found!"; }

            if( !is_string( $store ) ) {                
                if( $_POST["checkState"] === "false" && $store ) {
                    App::get("session")->setVariable( "header", [ "feedB" => [ "fetchResponse" => "Het album: " . $_POST["albumNaam"] . " is toegvoegd aan uw collectie!" ] ] );
                    unset( $_SESSION["page-data"]["colllections"] );
                    return App::redirect("gebruik");
                } else if ( $_POST['checkState'] === 'true' && $store ) {
                    App::get("session")->setVariable( "header", [ "feedB" => [ "fetchResponse" => "Het album: " . $_POST["albumNaam"] . " is verwijdert van uw collectie!" ] ] );
                    unset( $_SESSION["page-data"]["colllections"] );
                    return App::redirect("gebruik");
                } else if ( !$store ) {
                    App::get("session")->setVariable( "header", [ "error" => $this->dbError ] );
                    return App::redirect("gebruik");
                }
            } else {
                App::get("session")->setVariable( "header", [ "error" => $this->dbError ] );
                return App::redirect("gebruik");
            }
        } else {
            App::get("session")->setVariable( "header", [ "error" => $this->authFailed ] );
            return App::redirect("");
        }
    }
}
?>