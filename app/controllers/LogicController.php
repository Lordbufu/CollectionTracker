<?php

//  TODO: Add a JS event to the password input (register user), it only checks the confirm input atm.
//  TODO: Review the user of certain Else statements, might not even be userfull (register(), )
//  TODO: Re-write the 'valUsr()' function with the new validation method in 'login()'.

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
    public function dbCreation() {                                                              // '/dbCreation' function, for the database population.
        App::get('database')->createTable('gebruikers');
        App::get('database')->createAdmin();
        App::get('database')->createTable('series');
        App::get('database')->createTable('serie_meta');
        App::get('database')->createTable('albums');
        App::get('database')->createTable('collecties');

        App::redirect('');                                                                      // Redirect back to the landingpage.
    }
    
    // Finished and cleaned up.
	public function register() {                                                                // '/register' function.
        $temp = [                                                                               // Temp store for the filtered user input.
            'Gebr_Naam' => htmlspecialchars($_POST['gebr-naam']),
            'Gebr_Email' => htmlspecialchars($_POST['email']),
            'Gebr_WachtW' => password_hash($_POST['wachtwoord'], PASSWORD_BCRYPT),
            'Gebr_Rechten' => 'gebruiker'
        ];

        $eval = App::get('user')->setUser($temp);                                               // Attempt to set user data in DB,

        if(isset($eval['error'])) {                                                             // if we where not able to store the user,
            App::get('session')->setVariable('header', $eval);                                  // store any errors in the session,
            return App::redirect('#account-maken-pop-in');                                      // redirect back to the register pop-in.
        } elseif(isset($eval['feedB'])) {                                                       // If we where able to store the user,
            App::get('session')->setVariable('header', $eval);                                  // store that message in the session,
            return App::redirect('#login-pop-in');                                              // redirect to the login pop-in.
        } else {
            // Not sure if required, just leaving it here for the moment
            die('A unknown error come looking around the corner');
        }
	}

    // Finished and cleaned up.
    public function login() {                                                                   // '/login' function.
        $pw = $_POST['wachtwoord'];                                                             // store pw input,
        $cred = htmlspecialchars($_POST['accountCred']);                                        // store/filter credentials input,


        if(App::get('user')->validateUser($cred, $pw) == 1) {                                   // validate the user credentials,
            App::get('session')->setVariable('user',                                            // bind user & session,
                [ 'id' => App::get('user')->getUserId() ]
            );

            if(App::get('user')->evalUser() == 1) {                                             // then evaluate the user rights,
                App::get('session')->setVariable('user', [                                      // tell the session that the user is not a Admin,
                    'Admin' => FALSE
                ]);

                App::get('session')->setVariable('header', ['feedB' =>                          // store a welcome message,
                    ['welcome' => "Welcome " . App::get('user')->getUserName() ]
                ]);

                return App::redirect('gebruik');                                                // and redirect to the user page.
            } elseif($user->evalUser() == 0) {                                                  // If the user is a Admin,
                App::get('session')->setVariable('user', [                                      // tell the session that the user is a Admin,
                    'Admin' => TRUE
                ]);

                App::get('session')->setVariable('header', ['feedB' =>                          // store a welcome message,
                    ['welcome' => "Welcome " . App::get('user')->getUserName() ]
                ]);

                return App::redirect('beheer');                                                 // we redirect to the admin page.
            } else {                                                                            // Just incase user rights went missing,
                App::get('session')->setVariable('header',                                      // store the return error in the session,
                    [ 'error' => App::get('user')->evalUser() ]
                );

                return App::redirect('#login-pop-in');                                          // and redirect back to the login pop-in.
            }
        } else {                                                                                // If the validation failed,
            App::get('session')->setVariable('header',                                          // store the return error in the session,
                [ 'error' => App::get('user')->validateUser($cred, $pw) ]
            );

            return App::redirect('#login-pop-in');                                              // and redirect back to the login pop-in.
        }
    }

    // Finished and cleaned up.
    public function logout() {                                                                  // '/logout' function.
        App::get('session')->endSession();                                                      // clean up and end the current session,
        App::redirect('');                                                                      // then redirect to the landingpage.
    }

    /* Admin-Page functions */
    // '/beheer' function, for the admin page.
    public function beheer() {
        // Expected/Required Page-data
        $data = [ 'header' => [], 'series' => [], 'albums' => [] ];

        // If there is no page data, get all serie data first
        if(empty($data['series'])) {
            $localSeries = App::get('database')->selectAll('series');
			$localAlbums = [];
			$count = 0;

            // Loop over all series, store the index and store its ablums.
            foreach($localSeries as $key => $value) {
                $sqlId = ['Album_Serie' => $localSeries[$key]['Serie_Index'] ];
                array_push($localAlbums, App::get('database')->selectAllWhere('albums', $sqlId));

				// Push each serie into the page data
				if(isset($localSeries[$key])) { array_push($data['series'], $localSeries[$key]); }

                // Count the albums in each serie, and store/reset the count after.
			    foreach($localAlbums[$key] as $aKey => $aValue) {
				    if(!empty($localAlbums[$key][$aKey])) {
					    if($localAlbums[$key][$aKey]['Album_Serie'] == $localSeries[$key]['Serie_Index']) {
						    $count++;
					    }
				    }
			    }

			    $data['series'][$key]['Album_Aantal'] = $count;
			    $count = 0;
            }
		}

        // To display the albums of a serie, i fist get all albums data.
        if(!empty($_POST['serie-index']) && !empty($_POST['serie-naam'])) {
            $tempAlbums = App::get('database')->selectAllWhere('albums', [ 'Album_Serie' => $_POST['serie-index'] ]);

            // Push each album into the page data array.
            foreach($tempAlbums as $key => $value) {
                array_push($data['albums'], $value);
            }

            // Prepare JS page data to ensure everything is proccesed\displayed correctly.
            array_push($data['header'], App::get('processing')->createData('local', 'huidigeSerie', $_POST['serie-naam']));
            array_push($data['header'], App::get('processing')->createData('local', 'huidigeIndex', $_POST['serie-index']));
            array_push($data['header'], App::get('processing')->createData('local', 'serieWeerg', true));
        }

        return App::view('beheer', $data);
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
    public function gebruik() {                                                                 // '/gebruik' function, for the user page.
        $authFailed = ["fetchResponse" => "Access denied, Account authentication failed !"];	// Error for when the user is not authenticated.
		$unexError = ["Unexpected error occured, plz contact your admin"];						// If for some reason there was no user id.

        if(isset($_SESSION['user']['id'])) {													// Check if there is a user id in the session,
            if(App::get('user')->checkUSer($_SESSION['user']['id'])) {							// authenticate the user with the user class.
                unset($_SESSION['page-data']);                                                  // Clear page-data so we get new selections.

                if(empty($_SESSION['page-data']['series'])) {                                   // Trigger repopulation if page-data series is clear
                    $_SESSION['page-data']['series'] = App::get('collection')->getSeries();     // set series data in the session.
                }

                if(isset($_POST['serie_naam'])) {                                               // If there was a serie name in the POST,
                    if(empty($_SESSION['page-data']['albums'])) {                               // and albums data is empty, then set the albums.
                        $_SESSION['page-data']['albums'] = App::get('collection')->getAlbums($_POST['serie_naam']);
                    }

                    if(empty($_SESSION['page-data']['collection'])) {                           // If the collection data is empty, set the collection data.
                        $_SESSION['page-data']['collections'] = App::get('collection')->getColl($_SESSION['user']['id']);
                    }

                    App::get('session')->setVariable('header', [                                // Set selected serie name in session for JS.
                        'broSto' => [ 'huidigeSerie' => $_POST['serie_naam'] ] ]
                    );
                }
            } else {																			// If Authentication failed, (most likely my own fail)
                $_SESSION['header']['error'] = $authFailed;										// we store the error in the session.
            }
        } else {																				// If there was no user data at all,
			die($unexError);																	// we die the unexError to end the request process.
		}

        return App::view('gebruik');                                                            // Always return the user view.
    }

    // Finished and cleaned up.
    public function albSta() {                                                                  // '/albSta' function, to update the 'collecties' data, based on the HTML switch.
        $authFailed = "Access denied, Account authentication failed !";                         // Error for when the user is not authenticated.
		$unexError = "Unexpected error occured, plz contact your admin";						// If for some reason there was no user id.

        if(isset($_SESSION['user']['id'])) {													// Check if there is a user id in the session,
            if(App::get('user')->checkUSer($_SESSION['user']['id'])) {                          // authenticate the user with the user class.
                if(isset($_POST['aanwezig'])) {                                                 // check if expected post data was set,
                    if($_POST['aanwezig'] === 'true') {                                         // if the user set a ablum to present in his collection,
                        $collErr = "Dit Album is al aanwezig in de huidige Collectie!!";        // Duplicate entry error.
                        $collComp = "Toevoegen van het album aan de collectie is gelukt";       // Collection data added feedback message.

                        $tempData = [                                                           // Prep the required data for setting collection data,
                            'Gebr_Index' => $_SESSION['user']['id'],
                            'Album_Naam' => $_POST['album_naam']
                        ];

                        $newCol = App::get('collection')->setColl($tempData);                   // then attempt to store the collection data,

                        if($newCol) {                                                           // if the collection was added,
                            echo json_encode($collComp);                                        // return the user feedback to JS json encoded.
                        } else {                                                                // If the collection was not added,
                            echo json_encode($collErr);                                         // return the error to JS json encoded.
                        }
                    } elseif($_POST['aanwezig'] === 'false') {                                  // If the user set a ablum to not present in his collection,
                        $collErr = "Er was geen ablum om te verwijderen!!";                     // Nothing to remove error.
                        $collRemo = "Verwijderen van het album uit de collectie is gelukt";     // Collection data removed feedback message.

                        $tempData = [                                                           // Prep the required data for removing collection data,
                            'Gebr_Index' => $_SESSION['user']['id'],
                            'Album_Naam' => $_POST['album_naam']
                        ];

                        $newCol = App::get('collection')->remColl($tempData);                   // then attempt to remove the data from the database,

                        if($newCol) {                                                           // if the collection was added,
                            echo json_encode($collRemo);                                        // return the user feedback to JS json encoded.
                        } else {                                                                // If the collection was not added (dont think this can ever happen ?),
                            echo json_encode($collErr);                                         // return the error to JS json encoded.
                        }
                    } else {                                                                    // If there was not post aanwezig data,
                        echo json_encode($unexError);                                           // we return the error to JS, json encoded for the fetch request.
                    }
                }
            } else {                                                                            // If Authentication failed, (most likely my own fail)
                echo json_encode($authFailed);                                                  // we return the error to JS, json encoded for the fetch request.
            }
        } else {                                                                                // If there was no user id stored in the session,
            echo json_encode($unexError);                                                       // we return the error to JS, json encoded for the fetch request.
        }

        return;                                                                                 // Always a good habbit to return to caller.
    }
}
?>