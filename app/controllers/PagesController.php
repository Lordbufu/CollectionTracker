<?php

namespace App\Controllers;

use App\Core\App;

class PagesController {
	// Landing-page function
	public function landing() {
		// Check if a table is present or not,
		$test = App::get('database')->testTable('gebruikers');

		// if not present we redirect to a db creation route.
		if($test === '42S02') {
			App::redirect('createDB');
		// If present we return the index view.
		} else {
			return App::view('index');
		}
	}

	// Admin-page function
	public function beheer() {
		// Page data that is expected
		$data = [ 'series' => [] ];

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
				if(isset($localSeries[$key])) {
					array_push($data['series'], $localSeries[$key]);
				}

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

		return App::view('beheer', $data);
	}

	// Finished and cleaned up.
	/*	gebruik():
			Function for the '/gebruik' get route, default user page.
			The minimal required data for the user page, is all the series the user can view.
			We die an error if there is no session user data, this can happen via bookmarks but also malicious intent/bots.
	 */
	public function gebruik() {
		$authFailed = ["fetchResponse" => "Access denied, Account authentication failed !"];	// Error for when the user is not authenticated.
		$unexError = ["Unexpected error occured, plz contact your admin"];						// If for some reason there was no user id.

		//unset($_SESSION['page-data']);
		if(isset($_SESSION['page-data']['albums'])) {
			die('is not set');
		}

		if(isset($_SESSION['user']['id'])) {													// Authenticate the user with the session data,
			if(App::get('user')->checkUSer($_SESSION['user']['id'])) {							// use the user class to check the id
				$_SESSION['page-data']['series'] = App::get('collection')->getSeries();			// set series data in the session.
			} else {																			// If Authentication failed, (to catch coding fails from me)
				$_SESSION['header']['error'] = $authFailed;										// we store the error in the session.
			}
		} else {																				// If there was no user data at all,
			die($unexError);																	// we die the unexError to end the request process.
		}

		//die(print(isset($_SESSION['page-data']['series'])));

		return App::view('gebruik');															// Return the default user view.
	}
}
?>