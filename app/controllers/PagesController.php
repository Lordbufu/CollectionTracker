<?php

namespace App\Controllers;

use App\Core\App;

class PagesController {
	/*	landing():
			The landing/homepage of the website, that checks if the database tables are present.
			If not present (error 42S02), i redirect to a route that creates all tables and the admin account.

			Return Value:
				On Validation		(view)		-> index.view.php
				Failed Validation	(redirect)	-> ../createDB
	 */
	public function landing() {
		if( App::get( "database" )->testTable( "gebruikers" ) === "42S02" ) {
			App::redirect( "createDB" );
		} else {
			return App::view( "index" );
		}
	}

	/*	beheer():
			The admin page get function, to load the default series data, and validate the user & rights.
				$userCheck (Array)	- Error for when account validation failed
				$impTags (Array)	- Tag that determin if the session page-data can be unset or not
				$serId (Int/Array)	- The id required for getting albums associated with a series.
				$tempAlbums (Array)	- The temp store for getAlbum(), so i can check for errors

			Return Value:
				On Validation		(view)		-> beheer.view.php
				Failed Validation	(redirect) 	-> index.view.php
	 */
	public function beheer() {
        /* If the user session data is present, evaluate it for the admin rights, if not we pass a invalid id to get a error back. */
		$userCheck = isset( $_SESSION["user"]["id"] ) ? App::get( "user" )->checkUser( $_SESSION["user"]["id"], "rights" ) : App::get( "user" )->checkUser( -1, "rights" );

		/* Validate the userCheck result, and execute the correct logic. */
		if( !is_array( $userCheck ) ) {
			/* If certain page states are not set, remove the data for repopulation. */
			$impTags = [ "add-album", "new-serie", "edit-serie", "huidige-serie", "album-dupl", "album-cover", "isbn-scan", "isbn-search" ];

			if( !App::get( "session" )->checkVariable( "page-data", $impTags ) ) {
				unset($_SESSION["page-data"]);
			}

			/* If no series data is set, set the series data. */
            if( empty( $_SESSION["page-data"]["series"] ) ) {
				$tempSeries = App::get( "series" )->getSeries();
			} elseif( !empty( $_SESSION["page-data"]["series"] ) ) {
				unset( $_SESSION["page-data"]["series"] );
				$tempSeries = App::get( "series" )->getSeries();
			}

			if( isset( $tempSeries["error"] ) ) {
				App::get( "session" )->setVariable( "header", $tempSeries );
			} else {
				App::get( "session" )->setVariable( "page-data", $tempSeries );
			}


			// REVIEW
				// There might be more then 1 error here that needs to be stored.
			/* If no album data is set, and we are not looking at a series, re-populate the albums. */
			if( empty( $_SESSION["page-data"]["albums"] ) && isset( $_SESSION["page-data"]["huidige-serie"] ) ) {
				$serId = [ "Album_Serie" => App::get( "series" )->getSerAtt( "Serie_Index", [ "Serie_Naam" => $_SESSION["page-data"]["huidige-serie"] ] ) ];
				$tempAlbums = App::get( "albums" )->getAlbums( $serId );

				if( isset( $tempAlbums ) && !isset( $tempAlbums["error"] ) ) {
					App::get( "session" )->setVariable( "page-data", $tempAlbums );
				} else {
					App::get( "session" )->setVariable( "header", $tempAlbums );
				}
			}

			return App::view( "beheer" );
		} else {
			App::get( "session" )->setVariable( "header", $userCheck );
			return App::redirect( "" );
		}
	}

	/*	gebruik():
			The user page get function, to load the default series data, and validate the user & rights.
				$userCheck (Array)	- Error for when account validation failed
				$tempSeries (Array) - The temp store for getSeries(), so i can check for errors
				$tempCol (Array)	- The temp store for getCol(), so i can check for errors

			Return Value:
				On Validation		(view)		-> gebruik.view.php
				Failed Validation	(redirect) 	-> index.view.php
	 */
	public function gebruik() {
		/* If the user session data is present, if not we pass a invalid id to get a error back. */
		$userCheck = isset( $_SESSION["user"]["id"] ) ? App::get("user")->checkUser( $_SESSION["user"]["id"] ) : App::get("user")->checkUser( -1 );

		/* Validate the userCheck result, and execute the correct logic. */
		if( !is_array( $userCheck ) ) {
			/* If the series page-data is not set set it, otherwhise unset and reset it. */
			if( !isset( $_SESSION["page-data"]["series"] ) ) {
				$tempSeries = App::get( "series" )->getSeries();
			} else {
				unset( $_SESSION["page-data"]["series"] );
				$tempSeries = App::get( "series" )->getSeries();
			}

			if( isset( $tempSeries["error"] ) ) {
				App::get( "session" )->setVariable( "header", $tempSeries );
			} else {
				App::get( "session" )->setVariable( "page-data", $tempSeries ); 
			}

			/* Always unset and reset the collection data, before redirecting to the user page. */
			unset( $_SESSION["page-data"]["collections"] );
			$tempCol = App::get( "collecties" )->getCol( [ "Gebr_Index" => $_SESSION["user"]["id"] ] );

			/* Either store the error or the collection data in the session */
			if( !isset( $tempCol["error"] ) ) {
				App::get( "session" )->setVariable( "page-data", $tempCol );
			} else {
				App::get( "session" )->setVariable( "header", $tempCol );
			}
			
			return App::view( "gebruik" );
		} else {
			App::get( "session" )->setVariable( "header", $userCheck );
			return App::redirect( "" );
		}
	}
}
?>