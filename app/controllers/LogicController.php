<?php

//  TODO: Add a JS event to the password input (register user), it only checks the confirm input atm.
//  TODO: Change return view to redirects, when there is a error set for the header/JS (example line: 150).
//  TODO: Go over all code, and adjust for database error returned from the collection class, since querry execution now returns the DB error as a string.

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
            I validate the process, based on what associated array item is set, after requesting to store the user.

                $temp:  The user input that needs to be stored.
                $eval:  The outcome of attempting to store the user input in the database.
            
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
            App::get('session')->setVariable('header', ['feedB' => ["userCreated" => "Gebruiker aangemaakt, u kunt nu inloggen!"]]);

            return App::redirect('#login-pop-in');
        } else {
            App::get('session')->setVariable('header', $store);

            return App::redirect('#account-maken-pop-in');
        }
    }

    /*  login():
            The POST route for the login process, where the user class is used to validate the user.
            And where the SESSION data is set, linking a user to a session, so we can verify them later on.

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
        // Non-database errors that can occure during this process.
		$authFailed = ["fetchResponse" => "Access denied, Account authentication failed !"];
        $dupError = ["fetchResponse" => "Deze serie naam bestaat al, gebruik een andere naam gebruiken !"];

        // Check session user data, and validate the user that is stored.
        if(isset($_SESSION['user']['id']) && App::get('user')->checkUSer($_SESSION['user']['id'], 'rights')) {
            // If a pop-in is closed, and a serie is selected, we return to the admin page.
            if(isset($_POST['close-pop-in']) && isset($_SESSION['page-data']['huidige-serie'])) {
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
                    'add-album' => App::get('collection')->getSerInd($_POST['album-toev'])
                ]);

                return App::redirect('beheer#albumt-pop-in');
            }

            // Session data checks, to prevent unexpected behavior in page logic.
            if(!App::get('session')->checkVariable('page-data', [ 'add-album', 'new-serie', 'edit-serie' ] )) {
                unset($_SESSION['page-data']);
            }

            // If there is no series data in the session, populate the session data.
            if(empty($_SESSION['page-data']['series'])) {
                App::get('session')->setVariable('page-data', App::get('collection')->getSeries());
            }

            // If there was a serie-index in the post, store the albums and serie-naam for that series in the session.
            if(!empty($_POST['serie-index'])) {
                App::get('session')->setVariable('page-data', App::get('collection')->getAlbums($_POST['serie-index']));
                App::get('session')->setVariable('page-data', ['huidige-serie' => App::get('collection')->getSerName($_POST['serie-index']) ] );
            }

            // If the edit series button was clicked, we set the serie index in the session an return to the edit pop-in.
            if(isset($_POST['serie-edit-index'])) {
                App::get('session')->setVariable('page-data', ['edit-serie' => $_POST['serie-edit-index']]);

                return App::redirect('beheer#serieb-pop-in');
            }

            return App::view('beheer');
        } else {
            App::get('session')->setVariable('header', ['error' => $authFailed]);

            return App::redirect('');
        }
    }

    //  TODO: Figure out what todo with the SQL error that is returned on failed DB actions.
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
        // Non-database errors that can occure during this process.
        $authFailed = ["fetchResponse" => "Access denied, Account authentication failed !"];
        $dupError = ["fetchResponse" => "Deze serie naam bestaat al, gebruik een andere naam gebruiken !"];

        // Check session user data, and validate the user that is stored.
        if( isset($_SESSION['user']['id']) && App::get('user')->checkUSer($_SESSION['user']['id'], 'rights') ) {
            // Check if serie name was set, and check if serie name is duplicated, because the user can change it.
            if(isset($_POST['serie-naam'])) {
                if(App::get('collection')->cheSerName($_POST['serie-naam'])) {
                    // Store the POST data for JS to fill out the form again.
                    App::get('session')->setVariable('header', ["broSto" => [
                        "serieNaam" => $_POST['serie-naam'],
                        "makers" => $_POST['makers'],
                        "opmerking" => $_POST['opmerking']
                    ]]);
                    
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
            if(!$store) {
                // return user feedback that the serie was added.
                App::get('session')->setVariable('header', ['feedB' => [
                    'fetchResponse' => 'Het toevoegen van: ' . $_POST['serie-naam'] . ' is gelukt !']
                ]);

                return App::redirect('beheer');
            } else {
                // return error from the collection class.
                App::get('session')->setVariable('header', ['error' => [
                    'fetchResponse' => "Er was een database error, neem contact op met de administrator als dit blijft gebeuren!"
                ]]);
                
                return App::redirect('beheer');
            }
        // Notify user that authentication failed, and redirect to the landingpage
        } else {
            App::get('session')->setVariable('header', ['error' => $authFailed]);
            return App::redirect('');  
        }
    }

    // Refactor: In Progress
    //  TODO: If edit found a duplicate name, you are put back to /beheer, better if that would be the pop-in + post data.
    // '/serieBew' function, edit\update serie data.
    /*  serieBew():
            This function deals with editing serie data on the admin page, and stores the changes made.

            $serieData (Assoc Array) :
     */
    public function serieBew() {
        // Non-database errors that can occure during this process.
        $authFailed = ["fetchResponse" => "Access denied, Account authentication failed !"];
        $dupError = ["fetchResponse" => "Deze serie naam bestaat al, gebruik een andere naam gebruiken !"];

        // // Check session user data, and validate the user that is stored.
        if( isset($_SESSION['user']['id']) && App::get('user')->checkUSer($_SESSION['user']['id'], 'rights') ) {
            // Since the user can change the serie-naam, we need to check if its duplicate or not.
            if(isset($_POST['naam'])) {
                if(App::get('collection')->cheSerName($_POST['naam'], $_POST['index'])) {
                    // Store the error for user feedback.
                    App::get('session')->setVariable('header', ['error' => $dupError]);

                    // redirect to the pop-in.
                    return App::redirect('beheer');
                // If not duplicate store in serieData array.
                } else { $serieData['Serie_Naam'] = $_POST['naam']; }
            }

            if(isset($_POST['makers'])) { $serieData['Serie_Make'] = $_POST['makers']; }
            if(isset($_POST['opmerking'])) { $serieData['Serie_Opmerk'] = $_POST['opmerking']; }

            $store = App::get('collection')->setSerie($serieData, $_POST['index']);

            if(!is_string($store)) {
                // return error from the collection class.
                App::get('session')->setVariable('header', ['error' => [
                    'fetchResponse' => "Er was een database error, neem contact op met de administrator als dit blijft gebeuren!"
                ]]);
                
                return App::redirect('beheer');
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

    // Refactor: Pending
    // 'serieVerw' function, remove serie and its albums from database.
    public function serieVerw() {
        // Store the identifiers required for removing the entire serie.
        $serieId = [ 'Serie_Index' => $_POST['serie-index'], 'Serie_Naam' => $_POST['serie-naam'] ];

        // Remove the albums first, and then the serie to ensure there are not issues.
        App::get('processing')->remove_Object('albums', ['Album_Serie' => $_POST['serie-index']]);
        App::get('processing')->remove_Object('series', $serieId);

        // Provide feeback to user, using JS.
        echo json_encode("Verwijderen van {$_POST['serie-naam']}, en alle albums is gelukt");
    }

    // TODO: Figure out what todo with the SQL error that is returned on failed DB actions.
    // Refactor: Paused
    /* albumT():
            This function checks and then albums to the database.
     */
    public function albumT() {
        // Non-database errors that can occure during this process.
		$authFailed = ["fetchResponse" => "Access denied, Account authentication failed !"];
        $dupError = ["fetchResponse" => "Deze album naam bestaat al, gebruik een andere naam gebruiken !"];
        $unexError = ["fetchResponse" => "Er is een onverwachte fout opgetreden, neem contact op met de admin als dit blijft gebeuren !"];

        // Check session user data, and validate the user that is stored.
        if( isset($_SESSION['user']['id']) && App::get('user')->checkUSer($_SESSION['user']['id'], 'rights') ) {
            // Make sure the album name is not duplicate, and the correct feedback is returned and redirect if true.
            if(isset($_POST['album-naam'])) {
                if(App::get('collection')->cheAlbName($_POST['serie-index'], $_POST['album-naam'])) {
                    App::get('session')->setVariable('header', ['error' => $dupError]);
                    return App::redirect('beheer');
                } else {
                    $albumData['Album_Naam'] = htmlspecialchars($_POST['album-naam']);
                }
            }

            // Populate the rest of the required data
            $albumData['Album_Opm'] = 'W.I.P.';
            $albumData['Album_Serie'] = $_POST['serie-index'];

            // Check if optional data is there, and set the correct data where needed.
            $albumData['Album_ISBN'] = (!empty( $_POST['album-isbn']) || $_POST['album-isbn'] !== "") ? $_POST['album-isbn'] : 0;
            if(isset($_POST['album_nummer'])) { $albumData['Album_Nummer'] = $_POST['album_nummer']; }
            if(!empty($_POST['album_datum'])) { $albumData['Album_UitgDatum'] = $_POST['album_datum']; }

            // The album-cover requires some converting to base64_encoded blob data.
            if($_FILES['album-cover']['error'] === 0) {
                // Get all required file info to store it's content
                $fileName = basename($_FILES["album-cover"]["name"]);
                $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
                $image = $_FILES['album-cover']['tmp_name'];

                // Get file content, and store it in a string that can be inject straight into a HTML <img> tag.
                $imgContent = file_get_contents($image);
                $dbImage = 'data:image/'.$fileType.';charset=utf8;base64,'.base64_encode($imgContent);
                
                // Add the blob/string to the SQL data.
                $albumData['Album_Cover'] = $dbImage;
            }

            $store = App::get('collection')->setAlbum($albumData);

            if(is_string($store)) {
                // Add user feedback to header data.
                App::get('session')->setVariable('header', ['error' => $unexError]);

                // Redirect to admin page.
                return App::redirect('beheer');
            } else {
                // Add user feedback to header data.
                App::get('session')->setVariable('header', [ 'feedB' =>
                    [ 'fetchResponse' => 'Het toevoegen van: ' . $_POST['album-naam'] . ' is gelukt !']
                ]);

                // Redirect to admin page.
                return App::redirect('beheer');
            }

        // Notify user that authentication failed, and redirect to the landingpage
        } else {
            App::get('session')->setVariable('header', ['error' => $authFailed]);
            return App::redirect('');  
        }
    }

    // Refactor: Pending
    // '/albumV' function, remove album from database.
    public function albumV() {
        // Because all user confirms are done client side, i simple remove the object.
        App::get('processing')->remove_Object('albums', ['Album_Index' => $_POST['album-index']], ['Album_Naam' => $_POST['album-naam']]);

        // And do some user feedback via JS.
        echo json_encode("Verwijderen van {$_POST['album-naam']}, is gelukt.");
    }

    // Refactor: Pending
    // 'albumBew' function, edit\update album data.
    public function albumBew() {
        // Prepare album and error check data.
        $albumData = [];
        $erroCheck;

        // Get all current album data from the database.
        $tempAlbum = App::get('database')->selectAllWhere('albums', ['Album_Index' => $_POST['album-index']])[0];

        // Prepare mandatory album data for SQL
        $albumData['Album_Serie'] = $tempAlbum['Album_Serie'];
        $albumData['Album_Naam'] = $_POST['album-naam'];
        $albumData['Album_ISBN'] = $_POST['album-isbn'];

        // Check non-mandatory data for SQL.
        if(isset($_POST['album-nummer'])) { $albumData['Album_Nummer'] = $_POST['album-nummer']; }
        if(isset($_POST['album-datum'])) { $albumData['Album_UitgDatum'] = $_POST['album-datum']; }

        // Prepare the cover in a base64_encoded string (blob).
        if(!empty($_FILES['album-cover']['name'])) {
            $fileName = basename($_FILES["album-cover"]["name"]);
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

            $image = $_FILES['album-cover']['tmp_name'];
            $imgContent = file_get_contents($image);
            $dbImage = 'data:image/'.$fileType.';charset=utf8;base64,'.base64_encode($imgContent);

            $albumData['Album_Cover'] = $dbImage;
        }

        // Attempt to store album in database.
        $infoAlbum = App::get('processing')->update_Object('albums', ['Album_Index' => $_POST['album-index']], $albumData);

        // If there are errors, pass them to JS for user feedback.
        if(isset($infoAlbum) && $infoAlbum != 0) {
            echo json_encode($infoAlbum);
        // If there are no errors, give feedback to user via JS.
        } else {
            echo json_encode("Het album: " . $_POST['album-naam'] . " is bijgewerkt.");
        }
    }

    // Refactor: Pending
    // 'adminReset' function, user password reset so only the admin can reset passwords.
    public function adminReset() {
        // Attempt to update the user table with the new password
        $reset = App::get('processing')->update_Object('gebruikers', ['Gebr_Email' => $_POST['email']], ['Gebr_WachtW' => password_hash($_POST['wachtwoord1'], PASSWORD_BCRYPT)]);

        // Check if there where errors trying to update the information.
        if(isset($reset)) {
            echo json_encode('De wachtwoord reset is niet gelukt');
        } else {
            echo json_encode('De wachtwoord reset is geslaagd');
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

        if(isset($_SESSION['user']['id'])) {
            if(App::get('user')->checkUSer($_SESSION['user']['id'])) {
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
        } else {
            echo json_encode($authFailed);
            return;
        }
    }
}
?>