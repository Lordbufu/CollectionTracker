<?php

namespace App\Controllers;

use App\Core\App;

class PagesController {
	protected $authFailed = [ "error" => [ "fetchResponse" => "Access denied, Account authentication failed !" ] ];

	/*	landing():
			The landing/homepage of the website, that checks if the database tables are present.
			If not present (error 42S02), i redirect to a route that creates all tables and the admin account.

			Return Value:
				On Validation: (view)			-> index.view.php
				Failed Validation: (redirect)	-> ../createDB
	 */
	public function landing() {
		if(App::get("database")->testTable("gebruikers") === "42S02") {
			App::redirect("createDB");
		} else { return App::view("index"); }
	}

	/*	beheer():
			The admin page get function, to load the default series data, and validate the user & rights.
				$authFailed (Assoc Array)	- Error for when account validation failed.

			Return Value:
				On Validation: (view) 			-> beheer.view.php
				Failed Validation: (redirect) 	-> index.view.php
	 */
	public function beheer() {
		if( !App::get("session")->checkVariable( "page-data", [ "add-album", "new-serie", "edit-serie", "huidige-serie", "album-dupl", "album-cover" ] ) ) {
			unset($_SESSION["page-data"]);
		}

		if( App::get("user")->checkUSer( $_SESSION["user"]["id"], "rights" ) ) {
            if( empty( $_SESSION["page-data"]["series"] ) ) {
				App::get("session")->setVariable( "page-data", App::get("collection")->getSeries() );
			}

			if( empty( $_SESSION["page-data"]["albums"] ) && isset( $_SESSION["page-data"]["huidige-serie"] ) ) {
				App::get("session")->setVariable( "page-data", App::get("collection")->getAlbums( $_SESSION["page-data"]["huidige-serie"] ) );
			}

			return App::view("beheer");
		} else {
			App::get("session")->setVariable( "header", $authFailed );

			return App::redirect("");
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
		if( App::get("user")->checkUSer( $_SESSION["user"]["id"] ) ) {
			/* Set serie page data if not set, else clean serie page data, and set to refresh said data. */
			if( !isset( $_SESSION["page-data"]["series"] ) ) {
				App::get("session")->setVariable( "page-data", App::get("collection")->getSeries() ); 
			} else {
				unset( $_SESSION["page-data"]["series"] );
				App::get("session")->setVariable( "page-data", App::get("collection")->getSeries() );
			}

			/* Always refresh the user its collection data. */
			unset( $_SESSION["page-data"]["collections"] );
			App::get("session")->setVariable( "page-data", App::get("collection")->getColl( "collecties", [ "Gebr_Index" => $_SESSION["user"]["id"] ] ) );

			return App::view("gebruik");
		} else {
			App::get("session")->setVariable( "header", $authFailed );

			return App::redirect("");
		}
	}
}
?>