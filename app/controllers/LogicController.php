<?php

//  TODO: Add a JS event to the password input (register user), it only checks the confirm input atm.
//  TODO: Change return view to redirects, when there is a error set for the header/JS (example line: 150).
//  TODO: Go over all code, and adjust for database error returned from the collection class, since querry execution now returns the DB error as a string (should be noted as comment).

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
    /* Landingpage functions */
    /*  dbCreation():
            The function linked to the landingpage route, if no database tables where set this triggers.
            And it creates all tables and the default admin account, before redirecting back to the landingpage.

            Return Value    - Redirect -route-> '/'
     */
    public function dbCreation() {
        App::get('database')->createTable('gebruikers');
        App::get('database')->createAdmin();
        App::get('database')->createTable('series');
        App::get('database')->createTable('serie_meta');
        App::get('database')->createTable('albums');
        App::get('database')->createTable('collecties');

        App::redirect('');
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
            'Gebr_Naam' => htmlspecialchars($_POST['gebr-naam']),
            'Gebr_Email' => htmlspecialchars($_POST['email']),
            'Gebr_WachtW' => password_hash($_POST['wachtwoord'], PASSWORD_BCRYPT),
            'Gebr_Rechten' => 'gebruiker'
        ];

        $store = App::get('user')->setUser($temp);

        if($store === TRUE) {
            App::get('session')->setVariable('header', [ 'feedB' =>
            [ 'userCreated' => 'Gebruiker aangemaakt, u kunt nu inloggen!' ]
        ] );

            return App::redirect('#login-pop-in');
        } else {
            App::get('session')->setVariable('header', $store);

            return App::redirect('#account-maken-pop-in');
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
        $pw = $_POST['wachtwoord'];
        $cred = htmlspecialchars($_POST['accountCred']);

        if(App::get('user')->validateUser($cred, $pw) == 1) {
            App::get('session')->setVariable('user', [ 'id' => App::get('user')->getUserId()]);

            if(App::get('user')->evalUser() === 1) {
                App::get('session')->setVariable('user', [ 'admin' => FALSE ]);
                App::get('session')->setVariable('header', ['feedB' => ['welcome' => "Welcome " . App::get('user')->getUserName()]]);

                return App::redirect('gebruik');

            } elseif(App::get('user')->evalUser() === 0) {
                App::get('session')->setVariable('user', [ 'admin' => TRUE ]);
                App::get('session')->setVariable('header', ['feedB' => ['welcome' => "Welcome " . App::get('user')->getUserName()]]);

                return App::redirect('beheer');

            } else {
                App::get('session')->setVariable('header', [ 'error' => App::get('user')->evalUser()]);

                return App::redirect('#login-pop-in');
            }
        } else {
            App::get('session')->setVariable('header', [ 'error' => App::get('user')->validateUser($cred, $pw)]);

            return App::redirect('#login-pop-in');
        }
    }

    /*  logout():
            The POST '/logout' route, cleaning and ending the user session, before redirecting to home.

            Return Value    - Redirect -route-> '/'
     */
    public function logout() {
        App::get('session')->endSession();
        App::redirect('');
    }

    /* Adminpage functions */
    //  TODO: Finish\clean-up comments.
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
        // Errors that can occure during this process.
		$authFailed = [ 'fetchResponse' => 'Access denied, Account authentication failed !' ];
        $dupError = [ 'fetchResponse' => 'Deze serie naam bestaat al, gebruik een andere naam gebruiken !' ];

        // Check session user data, and validate the user that is stored.
        if( isset( $_SESSION['user']['id'] ) && App::get('user')->checkUSer( $_SESSION['user']['id'], 'rights') ) {
            // If a pop-in is closed in the series view, we tell JS to process the pop-in close actions.
            if( isset( $_POST['close-pop-in'] ) && isset( $_SESSION['page-data']['huidige-serie'] ) ) {
                return App::view('beheer');
            }

            // Check for duplicate serie names for the serie-maken controller.
            if(isset($_POST['newSerName'])) {
                if(App::get('collection')->cheSerName($_POST['newSerName'])) {
                    App::get('session')->setVariable('header', ['error' => $dupError]);
                    return App::redirect('beheer'); // redirect to clear post and thus the repeat of the error on page refresh.
                } else {
                    App::get('session')->setVariable('page-data', ['new-serie' => $_POST['newSerName']]);
                    return App::redirect('beheer#seriem-pop-in');
                }
            }

            // If the album-toev controller is used, get the serie-index into the session, and redirect to the pop-in.
            if(isset($_POST['album-toev'])) {
                App::get('session')->setVariable('page-data', [
                    'add-album' => App::get('collection')->getSerInd( $_POST['album-toev'] )
                ] );

                return App::redirect('beheer#albumt-pop-in');
            }

            // Session data checks, to prevent unexpected behavior in page logic.
            if(!App::get('session')->checkVariable('page-data', [ 'add-album', 'new-serie', 'edit-serie', 'huidige-serie' ] )) {
                unset($_SESSION['page-data']);
            }

            // If the admin want to return to the default view from the album view
            if(isset($_POST['return'])) {
                // Unset the album-view session data, to replace the templates.
                unset($_SESSION['page-data']['huidige-serie']);
                // Unset series session data, to trigger a refresh.
                unset($_SESSION['page-data']['series']);

                // Redirect to clear the browser POST data.
                return App::redirect('beheer');
            }

            // If there is no series data in the session, populate the session data.
            if(empty($_SESSION['page-data']['series'])) {
                App::get('session')->setVariable( 'page-data', App::get('collection')->getSeries() );
            }

            // If there was a serie-index in the post, store the albums and serie-naam for that series in the session.
            if(!empty($_POST['serie-index'])) {
                App::get('session')->setVariable( 'page-data', App::get('collection')->getAlbums($_POST['serie-index']) );
                App::get('session')->setVariable( 'page-data', ['huidige-serie' => App::get('collection')->getSerName($_POST['serie-index']) ] );
            }

            // If the edit series button was clicked, we set the serie index in the session an return to the edit pop-in.
            if(isset($_POST['serie-edit-index'])) {
                App::get('session')->setVariable('page-data', [ 'edit-serie' => $_POST['serie-edit-index'] ] );
                //die(var_dump(print_r($_SESSION)));

                return App::redirect('beheer#serieb-pop-in');
            }



            return App::view('beheer');
        } else {
            App::get('session')->setVariable('header', [ 'error' => $authFailed ] );

            return App::redirect('');
        }
    }

    //  TODO: Figure out what todo with the SQL error that is returned on failed DB actions.
    //  TODO: Finish\clean-up comments.
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
        // Errors that can occure during this process.
        $authFailed = [ 'fetchResponse' => 'Access denied, Account authentication failed !' ];
        $dupError = [ 'fetchResponse' => 'Deze serie naam bestaat al, gebruik een andere naam gebruiken !' ];
        $dbError = [ 'fetchResponse' => 'Er was een database error, neem contact op met de administrator als dit blijft gebeuren!' ];

        // Check session user data, and validate the user that is stored.
        if( isset($_SESSION['user']['id']) && App::get('user')->checkUSer($_SESSION['user']['id'], 'rights') ) {
            // Check if serie name was set, and check if serie name is duplicated, because the user can change it.
            if(isset($_POST['serie-naam'])) {
                if(App::get('collection')->cheSerName($_POST['serie-naam'])) {
                    // Store the POST data for JS to fill out the form again.
                    App::get('session')->setVariable('header', [ 'broSto' => $_POST ] );
                    
                    // Store the error for user feedback.
                    App::get('session')->setVariable('header', ['error' => $dupError]);
                    
                    // redirect to the pop-in.
                    return App::redirect('beheer#seriem-pop-in');

                // Store and filter input for special chars.
                } else { $sqlData = ['Serie_Naam' => htmlspecialchars($_POST['serie-naam'])]; }
            }

            // Ensure 'makers' and 'opmerking' have either a value or empty string, and are filtered.
            $sqlData['Serie_Maker'] = isset($_POST['makers']) ? htmlspecialchars($_POST['makers']) : '';
            $sqlData['Serie_Opmerk'] = isset($_POST['opmerking']) ? htmlspecialchars($_POST['opmerking']) : '';

            // Attempt to store the data
            $store = App::get('collection')->setSerie($sqlData);

            // Check if there where errors or not, and ensure the right feedback is returned to JS.
            if(!is_string($store)) {
                // return user feedback that the serie was added.
                App::get('session')->setVariable('header', ['feedB' => [
                    'fetchResponse' => 'Het toevoegen van: ' . $_POST['serie-naam'] . ' is gelukt !' ]
                ]);

                return App::redirect('beheer');
            } else {
                // return error from the collection class.
                App::get('session')->setVariable('header', [ 'error' => [ 'fetchResponse' => $dbError ] ] );
                
                return App::redirect('beheer');
            }
        // Notify user that authentication failed, and redirect to the landingpage
        } else {
            App::get('session')->setVariable('header', ['error' => $authFailed]);
            return App::redirect('');  
        }
    }

    //  TODO: Figure out what todo with the SQL error that is returned on failed DB actions.
    //  TODO: Finish\clean-up comments.
    /*  serieBew():
            This function deals with editing serie data on the admin page, and stores the changes made.

            $serieData (Assoc Array) :
     */
    public function serieBew() {
        // Errors that can occure during this process.
        $authFailed = [ 'fetchResponse' => 'Access denied, Account authentication failed !' ];
        $dupError = [ 'fetchResponse' => 'Deze serie naam bestaat al, gebruik een andere naam gebruiken !' ];
        $dbError = [ 'fetchResponse' => 'Er was een database error, neem contact op met de administrator als dit blijft gebeuren!' ];

        // Check session user data, and validate the user that is stored.
        if( isset($_SESSION['user']['id']) && App::get('user')->checkUSer($_SESSION['user']['id'], 'rights') ) {
            // Since the user can change the serie-naam, we need to check if its duplicate or not.
            if(isset($_POST['naam'])) {
                if(App::get('collection')->cheSerName($_POST['naam'], $_POST['index'])) {
                    // Store the index of the series, so the correct on is displayed in the pop-in.
                    App::get('session')->setVariable('page-data', [ 'edit-serie' => $_POST['index'] ]);
                    // Store the error for user feedback.
                    App::get('session')->setVariable('header', [ 'error' => $dupError ]);

                    // Redirect to the pop-in.
                    return App::redirect('beheer#serieb-pop-in');

                // If not duplicate store in serieData array.
                } else { $serieData['Serie_Naam'] = $_POST['naam']; }
            }

            // Set data to a empty string if not in the POST data.
            $serieData['Serie_Maker'] = isset($_POST['makers']) ? $_POST['makers'] : '';
            $serieData['Serie_Opmerk'] = isset($_POST['opmerking']) ? $_POST['opmerking'] : '';

            $store = App::get('collection')->setSerie($serieData, $_POST['index']);

            if(is_string($store)) {
                // return error from the collection class.
                App::get('session')->setVariable('header', [ 'error' => $dbError ]);

                // Store the index of the series, so the correct on is displayed in the pop-in.
                App::get('session')->setVariable('page-data', [ 'edit-serie' => $_POST['index'] ]);
                
                return App::redirect('beheer#serieb-pop-in');
            } else {
                // return user feedback that the serie was added.
                App::get('session')->setVariable('header', ['feedB' => [
                    'fetchResponse' => 'Het aanpassen van: ' . $_POST['naam'] . ' is gelukt !']
                ]);

                return App::redirect('beheer');
            }

        // Notify user that authentication failed, and redirect to the landingpage
        } else {
            App::get('session')->setVariable('header', ['error' => $authFailed]);

            return App::redirect('');  
        }
    }

    //  TODO: Figure out what todo with the SQL error that is returned on failed DB actions.
    //  TODO: Finish\clean-up comments.
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
        // Non-database errors that can occure during this process.
        $authFailed = [ 'fetchResponse' => 'Access denied, Account authentication failed !' ];
        $dbError = [ 'fetchResponse' => 'Er was een database error, neem contact op met de administrator als dit blijft gebeuren!' ];

        // Check session user data, and validate the user that is stored.
        if( isset($_SESSION['user']['id']) && App::get('user')->checkUSer($_SESSION['user']['id'], 'rights') ) {

            // Remove the albums from serie form the database.
            $remove_1 = App::get('collection')->remSerie('albums', [ 'Album_Serie' => $_POST['serie-index'] ]);

            // Remove the serie itself from the database.
            $remove_2 = App::get('collection')->remSerie('series', [
                'Serie_Index' => $_POST['serie-index'],
                'Serie_Naam' => $_POST['serie-naam']
            ]);

            if(is_string($remove_1) || is_string($remove_2)) {
                // Store user feedback if there was a error.
                App::get('session')->setVariable('header', [ 'error' => $dbError ] );

                // Redirect to the admin page.
                return App::redirect('beheer');
            } else {
                // Store user feedback if there where no errors.
                App::get('session')->setVariable('header', [ 'feedB' => [
                    'fetchResponse' => 'Het verwijderen van: ' . $_POST['serie-naam'] . ' en alle albums is geslaagd!'
                ]]);

                // Redirect to the admin page.
                return App::redirect('beheer');
            }
        // Notify user that authentication failed, and redirect to the landingpage
        } else {
            App::get('session')->setVariable('header', [ 'error' => $authFailed ] );

            return App::redirect('');  
        }
    }

    //  TODO: Figure out what todo with the SQL error that is returned on failed DB actions.
    //  TODO: Finish\clean-up comments.
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
        // Errors that can occure during this process.
		$authFailed = [ 'fetchResponse' => 'Access denied, Account authentication failed !' ];
        $dupError = [ 'fetchResponse' => 'Dit album naam bestaat al, gebruik een andere naam !' ];
        $dbError = [ 'fetchResponse' => 'Er was een database error, neem contact op met de administrator als dit blijft gebeuren!' ];

        // Check session user data, and validate the user that is stored.
        if( isset( $_SESSION['user']['id'] ) && App::get('user')->checkUSer( $_SESSION['user']['id'], 'rights' ) ) {
            // If duplicate, return all user input and feedback, so the user can correct the issue.
            if(App::get('collection')->cheAlbName( $_POST['serie-index'], $_POST['album-naam'] ) ) {
                App::get('session')->setVariable( 'header', [ 'broSto' => $_POST ] );
                App::get('session')->setVariable( 'page-data', [ 'add-album' => $_POST['serie-index'] ] );
                App::get('session')->setVariable( 'header', [ 'error' => $dupError ] );

                if( $_FILES['album-cover']['error'] === 0 ) {
                    $fileName = basename( $_FILES['album-cover']['name'] );
                    $fileType = pathinfo( $fileName, PATHINFO_EXTENSION );
                    $image = $_FILES['album-cover']['tmp_name'];

                    $imgContent = file_get_contents($image);
                    $dbImage = 'data:image/' . $fileType . ';charset=utf8;base64,' . base64_encode($imgContent);

                    App::get('session')->setVariable( 'page-data', [ 'album-cover' => $dbImage ] );
                }

                return App::redirect('beheer#albumt-pop-in');
            // If not duplicate, store the filtered user input.
            } else { $albumData['Album_Naam'] = htmlspecialchars( $_POST['album-naam'] ); }

            // Populate the rest of the required data
            $albumData['Album_Opm'] = 'W.I.P.';
            $albumData['Album_Serie'] = $_POST['serie-index'];

            // Check if optional data is there, and set the correct data where needed.
            $albumData['Album_ISBN'] = ( !empty( $_POST['album-isbn'] ) || $_POST['album-isbn'] !== '' ) ? $_POST['album-isbn'] : 0;
            $albumData['Album_Nummer'] = ( isset($_POST['album-nummer']) && !is_string($_POST['album-nummer']) ) ? $_POST['album-nummer'] : 0;
            if( !empty( $_POST['album-datum'] ) ) { $albumData['Album_UitgDatum'] = $_POST['album-datum']; }

            // The album-cover requires some converting to base64_encoded blob data before we can store it.
            if( $_FILES['album-cover']['error'] === 0 ) {
                $fileName = basename( $_FILES['album-cover']['name'] );
                $fileType = pathinfo( $fileName, PATHINFO_EXTENSION );
                $image = $_FILES['album-cover']['tmp_name'];
                $imgContent = file_get_contents($image);
                $dbImage = 'data:image/' . $fileType . ';charset=utf8;base64,' . base64_encode($imgContent);
                $albumData['Album_Cover'] = $dbImage;
            // If there is no file, but there is string stored in the session, we use\store and unset that instead.
            } elseif( isset( $_SESSION['page-data']['album-cover'] ) ) {
                $albumData['Album_Cover'] = $_SESSION['page-data']['album-cover'];
                unset($_SESSION['page-data']['album-cover']);
            }

            $store = App::get('collection')->setAlbum($albumData);

            // If there is an error, store a generic error message and redirect to the main admin page.
            if(is_string($store)) {
                App::get('session')->setVariable('header', [ 'error' => $dbError ] );

                return App::redirect('beheer');
            // If stored give the correct user feedback, and redirect back to the main admin page.
            } else {
                App::get('session')->setVariable('header', [ 'feedB' => [
                    'fetchResponse' => 'Het toevoegen van: ' . $_POST['album-naam'] . ' is gelukt !'
                ] ] );

                // If the album-view is active
                if(isset($_SESSION['page-data']['huidige-serie'])) {
                    // Clear albums session data, so it can be repopulated after the redirect.
                    unset( $_SESSION['page-data']['albums'] );
                }

                return App::redirect('beheer');
            }
        // Notify user that authentication failed, and redirect to the landing page.
        } else {
            App::get('session')->setVariable('header', [ 'error' => $authFailed ] );

            return App::redirect('');  
        }
    }

    //  TODO: Figure out what todo with the SQL error that is returned on failed DB actions.
    //  TODO: Finish\clean-up comments.
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
        // Errors that can occure during this process.
		$authFailed = [ 'fetchResponse' => 'Access denied, Account authentication failed !' ];
        $dbError = [ 'fetchResponse' => 'Er was een database error, neem contact op met de administrator als dit blijft gebeuren!' ];

        // Check session user data, and validate the user that is stored.
        if( isset( $_SESSION['user']['id'] ) && App::get('user')->checkUSer( $_SESSION['user']['id'], 'rights' ) ) {
            if( isset( $_POST['album-index'] ) ) {
                // Store the album name as its gone after a delete querry xD
                $albName = App::get('collection')->getAlbumName( $_POST['album-index'], $_POST['serie-index'] );
                // The remSerie function still has to be renamed, as it removes both albums and series, and can likely also remove user collections.
                $store = App::get('collection')->remSerie( 'albums', [ 'Album_Index' => $_POST['album-index'] ] );

                // Check if there is a error string returned.
                if(is_string($store)) {
                    // Store user feedback if there was a error.
                    App::get('session')->setVariable( 'header', [ 'error' => $dbError ]);

                    // Redirect to the admin page.
                    return App::redirect('beheer');
                } else {
                    // Store user feedback if there was a error.
                    App::get('session')->setVariable( 'header', [ 'feedB' => [
                        'fetchResponse' => 'Het verwijderen van: ' . $albName . ' is geslaagd!'
                    ]]);

                    // Clear albums session data, so it can be repopulated
                    unset( $_SESSION['page-data']['albums'] );

                    // Redirect to the admin page.
                    return App::redirect('beheer');
                }
            } else { return App::redirect('beheer'); }
        // Notify user that authentication failed, and redirect to the landing page.
        } else {
            App::get('session')->setVariable('header', [ 'error' => $authFailed ] );

            return App::redirect('');  
        }
    }

    //  TODO: Figure out what todo with the SQL error that is returned on failed DB actions.
    //  TODO: Finish\clean-up comments.
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
        // Errors that can occure during this process.
		$authFailed = [ 'fetchResponse' => 'Access denied, Account authentication failed !' ];
        $dbError = [ 'fetchResponse' => 'Er was een database error, neem contact op met de administrator als dit blijft gebeuren!' ];
        $dupError = [ 'fetchResponse' => 'Dit album naam bestaat al, gebruik een andere naam !' ];

        // Check session user data, and validate the user that is stored.
        if( isset( $_SESSION['user']['id'] ) && App::get('user')->checkUSer( $_SESSION['user']['id'], 'rights' ) ) {
            // If a albumEdit is requested, i need to set that data in the session, so the correct data can be displayed.
            if( isset( $_POST['albumEdit'] ) ) {
                App::get('session')->setVariable( 'page-data', [ 'album-edit' => $_POST['albumEdit'] ] );

                return App::redirect("beheer#albumb-pop-in");
            }

            // If the album-naam is duplicate, i set a new album-edit and a feedback message in the session, and return to the pop-in.
            if(App::get('collection')->cheAlbName( $_POST['serie-index'], $_POST['album-naam'] ) ) {
                App::get('session')->setVariable( 'page-data', [ 'album-edit' => $_POST['album-index'] ] );
                App::get('session')->setVariable( 'header', [ 'error' => $dupError ] );

                return App::redirect('beheer#albumb-pop-in');
            // Else i filter and store the name for processing.
            } else { $albumData['Album_Naam'] = htmlspecialchars( $_POST['album-naam'] ); }

            // Set the remaining POST data in the albumData.
            $albumData['Album_Index'] = $_POST['album-index'];
            if( isset($_POST['album-nummer']) ) { $albumData['Album_Nummer'] = $_POST['album-nummer']; }
            if( !empty( $_POST['album-datum'] ) ) { $albumData['Album_UitgDatum'] = $_POST['album-datum']; }
            $albumData['Album_Isbn'] = isset($_POST['album-isbn']) ? $_POST['album-isbn'] : 0;
            $albumData['Album_Opm'] = isset($_POST['album-opm']) ? $_POST['album-opm'] : 'W.I.P';

            // Check if there was a cover, and convert it to a base64 string for the database.
            if( $_FILES['album-cover']['error'] === 0 ) {
                $fileName = basename( $_FILES['album-cover']['name'] );
                $fileType = pathinfo( $fileName, PATHINFO_EXTENSION );
                $image = $_FILES['album-cover']['tmp_name'];
                $imgContent = file_get_contents($image);
                $dbImage = 'data:image/' . $fileType . ';charset=utf8;base64,' . base64_encode($imgContent);
                $albumData['Album_Cover'] = $dbImage;
            }

            $store = App::get('collection')->setAlbum($albumData, [
                'Album_Index' => $_POST['album-index'],
                'Album_Serie' => $_POST['serie-index']
            ]);

            // If there is an error, store a generic error message and redirect to the main admin page.
            if(is_string($store)) {
                App::get('session')->setVariable('header', [ 'error' => $dbError ] );

                return App::redirect('beheer');
            } else {
                App::get('session')->setVariable('header', [ 'feedB' => [ 'fetchResponse' => 'Het aanpassen van: ' . $_POST['album-naam'] . ' is gelukt !' ] ] );

                // If the album-view is active, clear albums session data, so it can be repopulated after the redirect.
                if(isset($_SESSION['page-data']['huidige-serie'])) { unset( $_SESSION['page-data']['albums'] ); }

                return App::redirect('beheer');
            }
        // Notify user that authentication failed, and redirect to the landing page.
        } else {
            App::get('session')->setVariable( 'header', [ 'error' => $authFailed ] );

            return App::redirect('');  
        }
    }

    //  TODO: Figure out what todo with the SQL error that is returned on failed DB actions.
    //  TODO: Finish\clean-up comments.
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
        // Errors that can occure during this process.
		$authFailed = [ 'fetchResponse' => 'Access denied, Account authentication failed !' ];
        $dbError = [ 'fetchResponse' => 'Er was een database error, neem contact op met de administrator als dit blijft gebeuren!' ];

        // Check session user data, and validate the user that is stored.
        if( isset( $_SESSION['user']['id'] ) && App::get('user')->checkUSer( $_SESSION['user']['id'], 'rights' ) ) {
            // Attempt to update the user in the database.
            $store = App::get('user')->updateUser('gebruikers',
                [ 'Gebr_WachtW' => password_hash($_POST['wachtwoord1'], PASSWORD_BCRYPT) ],
                [ 'Gebr_Email' => $_POST['email'] ] );

            // If updated, store user feedback in session, and redirect to the admin page.
            if($store) {
                App::get('session')->setVariable('header', [ 'feedB' => ['fetchResponse' => 'Het wachtwoord van: ' . $_POST['email'] . ' is aangepast !' ] ] );

                return App::redirect('beheer');
            // If the update failed, store user feedback in session, and redirect to the pop-in
            } else {
                App::get('session')->setVariable('header', [ 'error' => $dbError ] );

                return App::redirect('beheer#ww-reset-pop-in');
            }
        // Notify user that authentication failed, and redirect to the landing page.
        } else {
            App::get('session')->setVariable( 'header', [ 'error' => $authFailed ] );

            return App::redirect('');  
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
        $authFailed = ["fetchResponse" => "Access denied, Account authentication failed !"];

        if(isset($_SESSION['user']['id']) && App::get('user')->checkUSer($_SESSION['user']['id'])) {
            unset($_SESSION['page-data']);

            App::get('session')->setVariable('page-data', App::get('collection')->getSeries());

            if(!empty($_POST['serie_naam'])) {
                App::get('session')->setVariable('page-data', App::get('collection')->getAlbums(App::get('collection')->getSerInd($_POST['serie_naam'])));
                App::get('session')->setVariable('page-data', App::get('collection')->getColl($_SESSION['user']['id']));
                App::get('session')->setVariable('page-data', ['huidige-serie' => $_POST['serie_naam']]);
            }

            return App::view('gebruik');
        } else {
            App::get('session')->setVariable('header', ['error' => $authFailed]);

            return App::redirect('');
		}
    }

    //  TODO: Review if i can remove the JS fetch with regular session data and page regular PhP routing.
    // Refactor: Potentially Still Pending
    /*  albSta():
            The POST route for '/albSta', where we create collection data, based on what album(s) got toggled on/off.

                $authFailed (Assoc Array)   - Error message for when the user din't validate, using the session data.
                $collErr (Assoc Array)      - Error for when a album was already added, can be triggered by page-refreshes or browser navigation.
                $collComp (Assoc Array)     - Album added feedback message.
                $colRemo (Assoc Array)      - Album removed feedback message.
            
            Return Value: JSON encoded data, for the JS fetch request.
     */
    public function albSta() {
        $authFailed = "Access denied, Account authentication failed !";
        $collErr = "Dit Album is al aanwezig in de huidige Collectie!!";
        $collComp = "Toevoegen van het album aan de collectie is gelukt";
        $collRemo = "Verwijderen van het album uit de collectie is gelukt";

        if(isset($_SESSION['user']['id']) && App::get('user')->checkUSer($_SESSION['user']['id'])) {
            $checkCol;

            if(isset($_POST['aanwezig']) && $_POST['aanwezig'] === 'true') {
                $tempData = [
                    'Gebr_Index' => $_SESSION['user']['id'],
                    'Alb_Index' => App::get('collection')->getAlbId($_POST['album_naam'])
                ];

                $checkCol = App::get('collection')->setColl($tempData);
            } elseif(isset($_POST['aanwezig']) && $_POST['aanwezig'] === 'false') {
                $tempData = [
                    'Gebr_Index' => $_SESSION['user']['id'],
                    'Alb_Index' => App::get('collection')->getAlbId($_POST['album_naam'])
                ];

                $checkCol = App::get('collection')->remColl($tempData);
            }

            if($checkCol) {
                if($_POST['aanwezig'] === 'true') {
                    echo json_encode($collComp);
                    return;
                } elseif($_POST['aanwezig'] === 'false') {
                    echo json_encode($collRemo);
                    return;
                }
            } else {
                echo json_encode($collErr);
                return;
            }
        } else {
            echo json_encode($authFailed);
            return;
        }
    }
}
?>