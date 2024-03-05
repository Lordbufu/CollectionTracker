<?php

namespace App\Controllers;

use App\Core\App;

class PagesController {
	/*	landing():
			The landing/homepage of the website, that checks if the database tables are present.
			If not present (err0r 42S02), i redirect to a route that creates all tables and the admin account.

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

		// Don't unset if 'new-serie' was set, else we get unexpected behavior ¿.
		if(!isset($_SESSION['page-data']['new-serie'])) { unset($_SESSION['page-data']); }

		if(App::get('user')->checkUSer($_SESSION['user']['id'], 'rights')) {
			App::get('session')->setVariable('page-data', App::get('collection')->getSeries());

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