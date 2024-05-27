<?php

namespace App\Controllers;

use App\Core\App;

class PagesController {
	/*	landing():
			The landing/homepage of the website, that checks if the database tables are present.
			If not present (error 42S02), i redirect to a route that creates all tables and the admin account.

			Return Value:
				On Validation: (view)			-> index.view.php
				Failed Validation: (redirect)	-> ../createDB
	 */
	public function landing() {
		if(App::get('database')->testTable('gebruikers') === '42S02') {
			App::redirect('createDB');
		} else {
			return App::view('index');
		}
	}

	/*	beheer():
			The admin page get function, to load the default series data, and valid the user & rights.

				$authFailed (Assoc Array)	- Error for when account validation failed

			Return Value:
				On Validation: (view) 			-> beheer.view.php
				Failed Validation: (redirect) 	-> index.view.php
	 */
	public function beheer() {
		$authFailed = [ "error" => [ "fetchResponse" => "Access denied, Account authentication failed !" ] ];

		// Session data checks, to prevent unexpected behavior in page logic.
		if(!App::get('session')->checkVariable('page-data', [ 'add-album', 'new-serie', 'edit-serie', 'huidige-serie' ] )) {
			unset($_SESSION['page-data']);
		}

		if(App::get('user')->checkUSer($_SESSION['user']['id'], 'rights')) {
			// Get series if non are set.
            if(empty($_SESSION['page-data']['series'])) {
                App::get('session')->setVariable( 'page-data', App::get('collection')->getSeries() );
            }

			// Get albums if non are set, and admin is in the albums view.
			if(empty($_SESSION['page-data']['albums']) && isset($_SESSION['page-data']['huidige-serie']) ) {
				App::get('session')->setVariable('page-data', App::get('collection')->getAlbums($_SESSION['page-data']['huidige-serie']) );
			}

			return App::view('beheer');
		} else {
			App::get('session')->setVariable('header', $authFailed);

			return App::redirect('');
		}
	}

	/*	gebruik():
			The user page get function, to load the default series data, and validate the user & rights.

				$authFailed (Assoc Array)	- Error for when account validation failed

			Return Value:
				On Validation: (view) 			-> gebruik.view.php
				Failed Validation: (redirect) 	-> index.view.php
	 */
	public function gebruik() {
		$authFailed = ["error" => [ "fetchResponse" => "Access denied, Account authentication failed !" ] ];

		unset($_SESSION['page-data']);

		if(App::get('user')->checkUSer($_SESSION['user']['id'])) {
			App::get('session')->setVariable('page-data', App::get('collection')->getSeries());

			return App::view('gebruik');
		} else {
			App::get('session')->setVariable('header', $authFailed);

			return App::redirect('');
		}
	}
}
?>