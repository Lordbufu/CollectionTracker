<?php
namespace App\Controllers;

use App\Core\App;

/*	PageController Class:
		De simpele GET routes voor de website, die zorgen dat de informatie altijd actueel is.
		Een hoop comments zijn achterwegen gelaten, omdat alles vrij eenvoudig is, zoals ook de bedoelling is voor de PagesController.
		Liever had ik hier nog minder code gehad, maar dit was de snellere/simpelere oplossing voor de scope van dit project.
 */
class PagesController {
	/* landing(): De GET route voor de landingspagina, die ook alle database tafels maakt, als die niet aanwezig zijn. */
	public function landing() {
		App::get('database')->createTable('gebruikers');
		App::get('database')->createAdmin();
		App::get('database')->createTable('series');
		App::get('database')->createTable('serie_meta');
		App::get('database')->createTable('albums');
		App::get('database')->createTable('collecties');

		return App::view('index');
	}

	/* beheer(): De GET route voor de beheer pagina, die zorgt dat de data op de pagina actueel is. */
	public function beheer() {
		$data = [ 'series' => [] ];

        if(empty($data['series'])) {
            $localSeries = App::get('database')->selectAll('series');
			$localAlbums = [];
			$count = 0;

            foreach($localSeries as $key => $value) {
                $sqlId = ['Album_Serie' => $localSeries[$key]['Serie_Index'] ];
				array_push($localAlbums, App::get('database')->selectAllWhere('albums', $sqlId));

				if(isset($localSeries[$key])) {
					array_push($data['series'], $localSeries[$key]);
				}

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

	/* gebruik(): De GET route voor de gebruik pagina, die zorgt dat de data op de pagina actueel is. */
	public function gebruik() {
		$data = [ 'series' => [] ];

        if(empty($data['series'])) {
            $temp = App::get('database')->selectAll('series');
            foreach($temp as $key => $value) {
                array_push($data['series'], $temp[$key]);
            }            
        }

		return App::view('gebruik', $data);
	}
}
?>