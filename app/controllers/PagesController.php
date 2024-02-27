<?php

namespace App\Controllers;

use App\Core\App;

//	TODO: If i feel like it, i might want to find a better way to prepare the series data for the beheer() function.
// Finished and cleaned up.
class PagesController {
	// Finished and cleaned up.
	public function landing() {																	// Landing-page function
		$test = App::get('database')->testTable('gebruikers');									// Check if a table is present or not,

		if($test === '42S02') {																	// if not present we redirect to a db creation route.
			App::redirect('createDB');
		} else {																				// If present we return the index view.
			return App::view('index');
		}
	}

	// Finished and cleaned up.
	public function beheer() {																	// Function for the '/beheer' get route, default admin page.
		$authFailed = ["fetchResponse" => "Access denied, Account authentication failed !"];	// Error for when the user is not authenticated.
		$unexError = ["Unexpected error occured, plz contact your admin"];						// If for some reason there was no user id.

		if(isset($_SESSION['user']['id'])) {													// Check if there is user data in the session data,
			if($_SESSION['user']['admin']) {													// then check if the user is a admin or not,
				if(App::get('user')->checkUSer($_SESSION['user']['id'])) {						// and just to be sure validate the user id,
					$_SESSION['page-data']['series'] = App::get('collection')->getSeries();		// store all series in the session,

					// Loop over each series, and count the albums per serie and store that in the session.
					foreach($_SESSION['page-data']['series'] as $index => $value) {
						$count = App::get('database')->countAlbums($value['Serie_Index']);
						$_SESSION['page-data']['series'][$index]['Album_Aantal'] = $count['count(*)'];
					}

					return App::view('beheer');													// Return the default admin view.
				} else {																		// If Authentication failed, (to catch coding fails from me?)
					$_SESSION['header']['error'] = $authFailed;									// we store the error in the session,
					return App::redirect('');													// and we redirect to home because there was not valid user.
				}
			} else {																			// If Authentication failed, (to catch coding fails from me?)
				$_SESSION['header']['error'] = $authFailed;										// we store the error in the session,
				return App::redirect('');   													// and we redirect to home because there was not valid user.
			}
		} else {																				// If there was no user data at all,
			die($unexError);																	// we die the unexError to end the request process.
		}
	}

	// Finished and cleaned up.
	public function gebruik() {																	// Function for the '/gebruik' get route, default user page.
		$authFailed = ["fetchResponse" => "Access denied, Account authentication failed !"];	// Error for when the user is not authenticated.
		$unexError = ["Unexpected error occured, plz contact your admin"];						// If for some reason there was no user id.

		if(isset($_SESSION['user']['id'])) {													// Authenticate the user with the session data,
			if(App::get('user')->checkUSer($_SESSION['user']['id'])) {							// use the user class to check the id
				$_SESSION['page-data']['series'] = App::get('collection')->getSeries();			// set series data in the session.
			} else {																			// If Authentication failed, (to catch coding fails from me?)
				$_SESSION['header']['error'] = $authFailed;										// we store the error in the session.
			}
		} else {																				// If there was no user data at all,
			die($unexError);																	// we die the unexError to end the request process.
		}

		return App::view('gebruik');															// Return the default user view.
	}
}
?>