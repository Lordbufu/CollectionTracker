<?php

//  TODO: Add a JS event to the password input (register user), it only checks the confirm input atm.

namespace App\Controllers;

use App\Core\{App, User};

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

            Return Value: (redirect)    -> '../'
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
                On sucess: (redirect)   -> '../#login-pop-in'
                On failed: (redirect)   -> '../#account-maken-pop-in'
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
                If validated as Admin   -> '../beheer'
                If validated as User    -> '../gebruik'
                If validation failed    -> '../#login-pop-in'
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

            Return Value: redirect to '../'
     */
    public function logout() {
        App::get('session')->endSession();
        App::redirect('');
    }

    /* Admin-Page functions */
    /*  beheer():
            The POST route for '/beheer', this is similar to the GET route in the 'PageController'.
            But here is also deal with loading the Series view, and thus loading all related albums.

                $authFailed (Assoc Array)   - Error message for when the user din't validate, using the session data.
            
            Return Type:
                On sucess (view)    -> '../beheer.view.php' 
                On fail (redirect)  -> '../'
     */
    public function beheer() {
		$authFailed = ["fetchResponse" => "Access denied, Account authentication failed !"];

        if(isset($_SESSION['user']['id'])) {
            if(App::get('user')->checkUSer($_SESSION['user']['id'],  'rights')) {
                unset($_SESSION['page-data']);

                if(empty($_SESSION['page-data']['series'])) {
                    App::get('session')->setVariable('page-data', App::get('collection')->getSeries());
                }

                if(!empty($_POST['serie-index'])) {
                    App::get('session')->setVariable('page-data', App::get('collection')->getAlbums($_POST['serie-index']));
                    App::get('session')->setVariable('page-data', ['huidige-serie' => App::get('collection')->getSerName($_POST['serie-index'])]);
                    App::get('session')->setVariable('header', [
                        'broSto' => [
                            'huidigeIndex' => $_POST['serie-index'],
                            'serieWeerg' => TRUE
                        ]
                    ]);
                }

                return App::view('beheer');

            } else {
                App::get('session')->setVariable('error', $authFailed);

                return App::redirect('');
            }
        } else {
            App::get('session')->setVariable('error', $authFailed);

            return App::redirect('');
        }
    }

    // '/serieM' function, both the controlle and pop-in submits.
    public function serieM() {
        // Keep track of potential errors.
        $naamError = false;

        // Before we open the pop-in, we check for double names.
        if(isset($_POST['naam-check'])) {
            $localSeries = App::get('database')->selectAll('series');

            foreach($localSeries as $key => $value) {
                if($value['Serie_Naam'] === $_POST['naam-check']) {
                    $naamError = true;
                }
            }

            // And we give feedback to the user if there is an error.
            if($naamError) {
                echo json_encode("Deze serie naam bestaat al, gebruik een andere naam gebruiken !");
            } else {
                echo json_encode("Serie-Maken");
            }

            return;
        // If the submit was from the pop-in, we start with formating the data for SQL
        } else {
            $sqlData = [ 'Serie_Naam' => $_POST['serie-naam'] ];

            // Ensure 'makers' has either a value or empty string
            if(isset($_POST['makers'])) {
                $sqlData['Serie_Maker'] = $_POST['makers'];
            } else {
                $sqlData['Serie_Maker'] = '';
            }

            // Ensure 'opmerking' has either a value or empty string
            if(isset($_POST['opmerking'])) {
                $sqlData['Serie_Opmerk'] = $_POST['opmerking'];
            } else {
                $sqlData['Serie_Opmerk'] = '';
            }
    
            // Attempt to store the data via my own Processing class.
            $newSerie = App::get('processing')->set_Object('series', $sqlData);
    
            // Check if there where errors or not, and ensure the right feedback is returned to JS.
            if(isset($newSerie)) {
                echo json_encode($newSerie);
            } else {
                echo json_encode("Het toevoegen van: " . $_POST['serie-naam'] . " is gelukt !");
            }

            return;
        }
    }

    // '/albumT' function, add album to database.
    public function albumT() {
        // Format the expected SQL data
        $albumData = [
            'Album_Naam' => $_POST['album-naam'],
            'Album_Opm' => 'W.I.P.'
        ];

        if(!empty($_POST['album-isbn']) || $_POST['album-isbn'] !== "") {
            $albumData['Album_ISBN'] = $_POST['album-isbn'];
        } else {
            $albumData['Album_ISBN'] = 0;
        }
        
        // Check if certain data is present before storing, as they are not required,
        if(isset($_POST['serie-index'])) {
            $albumData['Album_Serie'] = $_POST['serie-index'];
        }

        if(isset($_POST['album-nummer'])) {
            $albumData['Album_Nummer'] = $_POST['album-nummer'];
        }

        if(!empty($_POST['album-datum'])) {
            $albumData['Album_UitgDatum'] = $_POST['album-datum'];
        }

        // If we dont have a serie-index but only a serie-naam, we make sure the correct index is stored.
        if(isset($_POST['serie-naam']) && !isset($data['serie-index'])) {
            $tempSerie = App::get('database')->selectAllWhere('series', ['Serie_Naam' => $_POST['serie-naam']])[0];
            $albumData['Album_Serie'] = $tempSerie['Serie_Index'];
        }
        
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

        // Attempt to store the ablum in the database, and store any errors
        $newAlbum = App::get('processing')->set_Object('albums', $albumData);

        // IF there are errors, format the error for JS
        if(isset($newAlbum)) {
            $returnData = [];

            // Make sure that each error is stored properly, if present.
            foreach($newAlbum as $key => $value) {
                if($key === 'Album_Naam' && isset($value)) {
                    $returnData['aNaamFailed'] = $newAlbum['Album_Naam'];
                }

                if($key === 'Album_ISBN' && isset($value)) {
                    $returnData['aIsbnFailed'] = $newAlbum['Album_ISBN'];
                }
            }

            // return said error to JS for user feedback.
            echo json_encode($returnData);
        // If there where no errors, give user-feedback that the album was stored.
        } else {
            echo json_encode("Toevoegen van het Album: " . $_POST['album-naam'] . " is gelukt.");
        }
    }

    // '/albumV' function, remove album from database.
    public function albumV() {
        // Because all user confirms are done client side, i simple remove the object.
        App::get('processing')->remove_Object('albums', ['Album_Index' => $_POST['album-index']], ['Album_Naam' => $_POST['album-naam']]);

        // And do some user feedback via JS.
        echo json_encode("Verwijderen van {$_POST['album-naam']}, is gelukt.");
    }

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

    // '/serieBew' function, edit\update serie data.
    public function serieBew() {
        // Prepare data for SQL
        $serieData = [
            'Serie_Naam' => $_POST['naam'],
            'Serie_Maker' => $_POST['makers'],
            'Serie_Opmerk' => $_POST['opmerking']
        ];

        // Attempt to update database.
        $checkSerie = App::get('processing')->update_Object('series', ['Serie_Index' => $_POST['index']], $serieData);

        // Check for errors, and provide feedback for user via JS.
        if(isset($checkSerie)) { 
            echo json_encode($checkSerie);
        } else {
            echo json_encode('Het bijwerken van de Serie is gelukt !');
        }
    }

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
    // Finished and cleaned up.
    /*  gebruik():
            The POST route for '/gebruik', this is similar to the GET route in the 'PageController'.
            In this case though, we also need to load albums and collections, to display when a serie is selected.

                $authFailed (Assoc Array)   - Error message for when the user din't validate, using the session data.

            Return Value:
                On sucess: (view)       -> '../gebruik.view.php'
                On fail: (redirect)     -> '../'
     */
    public function gebruik() {
        $authFailed = ["fetchResponse" => "Access denied, Account authentication failed !"];

        if(isset($_SESSION['user']['id'])) {
            if(App::get('user')->checkUSer($_SESSION['user']['id'])) {
                unset($_SESSION['page-data']);

                App::get('session')->setVariable('page-data', App::get('collection')->getSeries());

                if(!empty($_POST['serie_naam'])) {
                    App::get('session')->setVariable('page-data', App::get('collection')->getAlbums(
                        App::get('collection')->getSerInd($_POST['serie_naam'])
                    ));

                    App::get('session')->setVariable('page-data', App::get('collection')->getColl($_SESSION['user']['id']));

                    App::get('session')->setVariable('header', ['broSto' => ['huidigeSerie' => $_POST['serie_naam']]]);
                }

                return App::view('gebruik');
            } else {
                App::get('session')->setVariable('header', ['error' => $authFailed]);

                return App::redirect('');
            }
        } else {
            App::get('session')->setVariable('header', ['error' => $authFailed]);

            return App::redirect('');
		}
    }

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
                $checkCol = null;

                if(isset($_POST['aanwezig'])) {
                    if($_POST['aanwezig'] === '0') {
                        $tempData = [
                            'Gebr_Index' => $_SESSION['user']['id'],
                            'Alb_Index' => App::get('collection')->getAlbId($_POST['album-naam'])
                        ];

                        $checkCol = App::get('collection')->setColl($tempData);
                    } else {
                        $tempData = [
                            'Gebr_Index' => $_SESSION['user']['id'],
                            'Alb_Index' => App::get('collection')->getAlbId($_POST['album_naam'])
                        ];

                        $checkCol = App::get('collection')->remColl($tempData);
                    }
                }

                if($checkCol) {
                    if($_POST['aanwezig'] === 'true') {
                        echo json_encode($collComp);
                        return;
                    } else {
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