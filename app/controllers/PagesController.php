<?php
//	TODO: Review why createAdmin() generates a new user index in the database, or if that is even the issue.
namespace App\Controllers;

use App\Core\App;

class PagesController {
	// Landing-page function
	public function landing() {
		App::get('database')->createTable('gebruikers');						// Create a gebruikers table if there is non,
		App::get('database')->createAdmin();									// create a default admin account if there is non,
		App::get('database')->createTable('series');							// create a serie table if there is non,
		App::get('database')->createTable('serie_meta');						// create a serie_meta table if there is non,
		App::get('database')->createTable('albums');							// create a albums table if there is non,
		App::get('database')->createTable('collecties');						// create a collecties table if there is non,

		return App::view('index');												// and then return to the index view.
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

	// User-page function
	public function gebruik() {
		// Page data that is expected
		$data = [ 'series' => [] ];

		//die(session_name());
		// If there is not page data get all series,
        if(empty($data['series'])) {
            $temp = App::get('database')->selectAll('series');
			
			// and push each serie into the page data.
            foreach($temp as $key => $value) {
                array_push($data['series'], $temp[$key]);
            }            
        }

		return App::view('gebruik', $data);
	}
}
?>