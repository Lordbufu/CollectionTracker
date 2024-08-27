<?php

namespace App\Controllers;

use App\Core\App;

/*  TODO: Edit function comments to reflect the changes over time. */
class PagesController {
	/*	landing():
			The landing/homepage of the website, that checks if the database tables are present.
			If not present (error 42S02), i redirect to a route that creates all tables and the admin account.

			Return Value:
				On Validation		(view)		-> index.view.php
				Failed Validation	(redirect)	-> ../createDB
	 */
	public function landing() {
		if(App::get("database")->testTable("gebruikers") === "42S02") {
			App::redirect("createDB");
		} else {
			return App::view("index");
		}
	}

	/*	beheer():
			The admin page get function, to load the default series data, and validate the user & rights.
				$authFailed (Assoc Array)	- Error for when account validation failed.

			Return Value:
				On Validation		(view)		-> beheer.view.php
				Failed Validation	(redirect) 	-> index.view.php
	 */
	public function beheer() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
        if( isset( $_SESSION["user"]["id"] ) ) {
            $userCheck = App::get("user")->checkUser( $_SESSION["user"]["id"], "rights");
        } else {
            $userCheck = App::get("user")->checkUser( -1, "rights" );
        }

		/* Validate the userCheck result, and execute the correct logic. */
		if( !is_array( $userCheck ) ) {
			/* If certain page states are not set, remove the data for repopulation. */
			$impTags = [ "add-album", "new-serie", "edit-serie", "huidige-serie", "album-dupl", "album-cover", "isbn-scan", "isbn-search" ];
			if( !App::get("session")->checkVariable( "page-data", $impTags ) ) {
				unset($_SESSION["page-data"]);
			}

			/* If no series data is set, set the series data. */
            if( empty( $_SESSION["page-data"]["series"] ) ) {
				App::get("session")->setVariable( "page-data", App::get("collection")->getSeries() );
			}

			/* If no album data is set, and we are not looking at a series, re-populate the albums. */
			if( empty( $_SESSION["page-data"]["albums"] ) && isset( $_SESSION["page-data"]["huidige-serie"] ) ) {
				App::get("session")->setVariable( "page-data", App::get("collection")->getAlbums( $_SESSION["page-data"]["huidige-serie"] ) );
			}

			return App::view("beheer");
		} else {
			App::get("session")->setVariable( "header", [ "error" => $userCheck ] );
			return App::redirect("");
		}
	}

	/*	gebruik():
			The user page get function, to load the default series data, and validate the user & rights.
				$authFailed (Assoc Array)	- Error for when account validation failed

			Return Value:
				On Validation		(view)		-> gebruik.view.php
				Failed Validation	(redirect) 	-> index.view.php
	 */
	public function gebruik() {
		/* If the user session data is present, if not we pass a invalid id to get a error back. */
		if( isset( $_SESSION["user"]["id"] ) ) {
            $userCheck = App::get("user")->checkUser( $_SESSION["user"]["id"] );
        } else {
            $userCheck = App::get("user")->checkUser( -1 );
        }

		/* Validate the userCheck result, and execute the correct logic. */
		if( !is_array( $userCheck ) ) {
			/* If the series page-data is not set set it, otherwhise unset and reset it. */
			if( !isset( $_SESSION["page-data"]["series"] ) ) {
				App::get("session")->setVariable( "page-data", App::get("collection")->getSeries() ); 
			} else {
				unset( $_SESSION["page-data"]["series"] );
				App::get("session")->setVariable( "page-data", App::get("collection")->getSeries() );
			}

			/* Always unset and reset the collection data, before redirecting to the user page. */
			unset( $_SESSION["page-data"]["collections"] );
			App::get("session")->setVariable( "page-data", App::get("collection")->getColl( "collecties", [ "Gebr_Index" => $_SESSION["user"]["id"] ] ) );
			return App::view("gebruik");
		} else {
			App::get("session")->setVariable( "header", $userCheck );
			return App::redirect("");
		}
	}
}
?>