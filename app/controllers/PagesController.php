<?php

namespace App\Controllers;

use App\Core\App;

//	TODO: Find a better way to present unexpected errors, so its not just a die to a funky looking printed string.
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

	/*	beheer():
			The admin page get function, to load the default series data, and valid the user & rights.

				$authFailed (Assoc Array)	- Error for when account validation failed

			Return Value:
				On Validation: (view) -> beheer.view.php
				Failed Validation: (redirect) -> index.view.php
	 */
	public function beheer() {
		$authFailed = [ "error" => [ "fetchResponse" => "Access denied, Account authentication failed !" ] ];

		unset($_SESSION['page-data']);		// I find it easier to update, when page-data starts clean each request.

		if(App::get('user')->checkUSer($_SESSION['user']['id'], 'rights')) {
			App::get('session')->setVariable('page-data', App::get('collection')->getSeries());

			return App::view('beheer');
		} else {
			App::get('session')->setVariable('header', $authFailed);

			return App::redirect('');
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