<?php

//  TODO: Re-write the 'valUsr()' function with the new validation method in 'login()'.
//  TODO: Add something to clear session data on logout via SessionMan.
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
    // Create DB function, for when the table check was triggered on landing.
    public function dbCreation() {
        // Create database tables and the default admin account.
        App::get('database')->createTable('gebruikers');
        App::get('database')->createAdmin();
        App::get('database')->createTable('series');
        App::get('database')->createTable('serie_meta');
        App::get('database')->createTable('albums');
        App::get('database')->createTable('collecties');

        App::redirect('');
    }

    /* Landingpage functions */
    // '/register' function.
	public function register() {
        $data = [ 'header' => [] ];

        // Format the user data for the database structure.
        $temp = [
            'Gebr_Naam' => $_POST['gebr-naam'],
            'Gebr_Email' => $_POST['email'],
            'Gebr_WachtW' => password_hash($_POST['wachtwoord'], PASSWORD_BCRYPT),
            'Gebr_Rechten' => 'gebruiker'
        ];

        // Store user data in database, and redirect to the login-pop-in.
        $newUser = App::get('processing')->set_Object('gebruikers', $temp);

        if(isset($newUser)) {
            if(isset($newUser['gebrNaam'])) {
                array_push($data['header'], App::get('processing')->createData('local', 'userError1', $newUser['gebrNaam']));
            }

            if(isset($newUser['gebrEmail'])) {
                array_push($data['header'], App::get('processing')->createData('local', 'userError2', $newUser['gebrEmail']));
            }

            array_push($data['header'], App::get('processing')->createRedirect('#account-maken-pop-in'));
        } else {
            array_push($data['header'], App::get('processing')->createData('local', 'userCreated', 'Gebruiker aangemaakt, u kunt nu inloggen!'));
            array_push($data['header'], App::get('processing')->createRedirect('#login-pop-in'));
        }

        return App::view('index', $data);
	}

    // '/login' function.
    public function login() {
        $data = [ 'header' => [] ];

        // Evaluate if the account credentials where set.
        if(isset($_POST['accountCred'])) {
            // Check if the credentials where a e-mail, and use that for a database id.
            if(filter_var($_POST['accountCred'], FILTER_VALIDATE_EMAIL)) {
                $id = [ 'Gebr_Email' => $_POST['accountCred'] ];
            // If it wasnt a valid e-mail, filter the input and use that as database id.
            } else {
                $id = [ 'Gebr_Naam' => htmlspecialchars($_POST['accountCred']) ];
            }
        }

        // Attempt to look up user data
        $gebruiker = App::get('database')->selectAllWhere('gebruikers', $id);

        // Check if there was user data, and verify the password and user rights.
        if(!empty($gebruiker[0])) {
            // evaluate the input passwords vs the stored password,
            if(password_verify($_POST['wachtwoord'], $gebruiker[0]['Gebr_WachtW'])) {
                // Set variable for user validation and data requests,
                App::get('session')->setVariable(['Gebr_Naam' => $gebruiker[0]['Gebr_Naam']]);
                App::get('session')->setVariable(['Gebr_Email' => $gebruiker[0]['Gebr_Email']]);

                // evaluate the user rights, and redirect accordingly
                if($gebruiker[0]['Gebr_Rechten'] === "Admin") {
                    App::redirect('beheer');
                } else {
                    App::redirect('gebruik');
                }

            // Create JS page-data for failed password check (intentional general feedback mssg).
            } else {
                $error = "Uw inlog gegevens zijn niet correct, probeer het nogmaals!!";
                array_push($data['header'], App::get('processing')->createData('local', 'loginFailed', $error));
                array_push($data['header'], App::get('processing')->createRedirect('#login-pop-in'));
            }
        // Create JS page-data for failed account check (intentional general feedback mssg).
        } else {
            $error = "Uw inlog gegevens zijn niet correct, probeer het nogmaals!!";
            array_push($data['header'], App::get('processing')->createData('local', 'loginFailed', $error));
            array_push($data['header'], App::get('processing')->createRedirect('#login-pop-in'));
        }

        return App::view('index', $data);
    }

    // '/logout' function.
    public function logout() {
        // Clean up and end the current session.
        App::get('session')->endSession();
        // Redirect to the landing page.
        App::redirect('');
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
    // '/gebruik' function, for the user page.
    public function gebruik() {
        // Prepare the required data structure for the user page.
        $data = [ 'header' => [], 'series' => [], 'albums' => [], 'collecties' => [] ];

        // Check if there is 'series' datam and populate if there isn't
        if(empty($data['series'])) {
            $temp = App::get('database')->selectAll('series');
            // Push each serie in to the page data array.
            foreach($temp as $key => $value) {
                array_push($data['series'], $temp[$key]);
            }
        }

        // If a serie was selected, we need to populate the 'albums' and 'collecties' data.
        if(isset($_POST['serie_naam'])) {
            // Get the proper identifiers/data required for the request.
            $serieObject = App::get('database')->selectAllWhere('series', [ 'Serie_Naam' => $_POST['serie_naam'] ])[0];
            $serieIndex = [ 'Album_Serie' => $serieObject['Serie_Index'] ];

            // Check if 'albums' is empty
            if(empty($data['albums'])) {
                $temp = App::get('database')->selectAllWhere('albums', $serieIndex);
                // Push each album in to the page data
                foreach($temp as $key => $value) {
                    array_push($data['albums'], $temp[$key]);
                }
            }

            // Check if 'collecties' is empty
            if(empty($data['collecties'])) {
                // Get the proper identifiers/data from the database.
                $gebrObject = App::get('database')->selectAllWhere('gebruikers', [ 'Gebr_Email' => $_SESSION['Gebr_Email'] ])[0];
                $collecties = App::get('database')->selectAllWhere('collecties', [ 'Gebr_Index' => $gebrObject['Gebr_Index'] ]);

                // Add each data row to the page data.
                foreach($collecties as $key => $value) {
                    array_push($data['collecties'], $collecties[$key]);
                }
            }

            // Make sure the 'serie-naam' is always returned to JS.
            array_push($data['header'], App::get('processing')->createData('local', 'huidigeSerie', "{$_POST['serie_naam']}"));
        }

        return App::view('gebruik', $data);
    }

    // '/albSta' function, to update the 'collecties' data, based on the HTML switch.
    public function albSta() {
        // Get all required data for the request.
        $tempGebr = App::get('database')->selectAllWhere("gebruikers", [ 'Gebr_Email' => $_SESSION['Gebr_Email'] ])[0];;
        $tempSerie = App::get('database')->selectAllWhere("series", [ 'Serie_Naam' => $_POST['serie_naam'] ])[0];
        $tempAlbum = App::get('database')->selectAllWhere("albums", [ 'Album_Naam' => $_POST['album_naam'], 'Album_Serie' => $tempSerie['Serie_Index'] ])[0];

        // Check the album states that are changed/requested.
        if($_POST['aanwezig'] === 'true') {
            // Prepare the data for SQL
            $tempCol = [
                "Gebr_Index" => $tempGebr["Gebr_Index"],
                "Alb_Index" => $tempAlbum["Album_Index"],
                "Alb_Staat" => "",
                "Alb_DatumVerkr" => date('Y-m-d'),
                "Alb_Aantal" => 1,
                "Alb_Opmerk" => ""
            ];

            // Attempt to add data to database.
            $newCol = App::get('processing')->set_Object('collecties', $tempCol);

            // Check for errors, and provide feedback to the user via JS.
            if(isset($newCol)) {
                echo json_encode($newCol['Col_Toev']);
            } else { echo json_encode('Toevoegen van het album aan de collectie is gelukt'); }
        } else if ($_POST['aanwezig'] === 'false') {
            // Prepare the data for SQL
            $tempCol = [
                "Gebr_Index" => $tempGebr["Gebr_Index"],
                "Alb_Index" => $tempAlbum["Album_Index"]
            ];

            // Attempt to remove the data from the database.
            $newCol = App::get('processing')->remove_Object('collecties', $tempCol);

            // Check for errors, and provide feedback to the user via JS.
            if(isset($newCol)) {
                echo json_encode($newCol['Col_Verw']);
            } else {
                echo json_encode('Verwijderen van het album uit de collectie is gelukt');
            }
        }
    }
}
?>