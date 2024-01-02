<?php

namespace App\Controllers;

use App\Core\App;

/*  TODO Lijst:
        - Rework Comments to english and more live enviroment setting.
        - Alle 2.0 features moeten nog gemaakt worden voor de beheer en gebruik pagina.
*/
class LogicController {
    /* register():
            Deze functie maakt altijd een standaard gebruiker aan, en hashed het wachtwoord voordat het opgeslagen word.
            De processing class verwerkt het verzoek om de gebruiker in de database te zetten, maar doet veder geen extra checks.
            Alle checks worden al gedaan in JS script, voordat de data verzonden word.
            Ik redirect de gebruiker ook direct naar de login, zodat die meteen gebruik kan maken van de App.

            Deze functie wou ik eigenlijk niet omzetten naar JS fetch, maar dit zou inprincipe nog wel kunnen.
     */
	public function register() {
        $temp = [
            'Gebr_Naam' => $_POST['gebr-naam'],
            'Gebr_Email' => $_POST['email'],
            'Gebr_WachtW' => password_hash($_POST['wachtwoord'], PASSWORD_BCRYPT),
            'Gebr_Rechten' => 'gebruiker'
        ];

        App::get('processing')->set_Object('gebruikers', $temp);
        App::redirect('#login-pop-in');
	}

    /* login():
            Deze functie zorgt ervoor dat een gebruiker kan inloggen, en dat het opgeslagen word in de browser storage.
            Het opgegeven e-mail word gebruikt om de gebruiker te vinden, en met die data controlleer ik of het wachtwoordt klopt.
            Als het wachtwoord klopt, kijk naar de gebruikers rechten, en sla ik de nodige informatie in de $data['header'].
            En dan geef ik de juiste pagina view en data terug aan de caller.

            Als het wachtwoord niet klopt, of er iets anders misgaat, maak ik een redirect naar de pop-in.
            En maak ik een terugkoppeling voor de gebruiker, voordat ik de standaard view en data terug geef aan de caller.
     */
    public function login() {
        $data = [ 'header' => [] ];
        $id = [ 'Gebr_Email' => $_POST['email'] ];
        $gebruiker = App::get('database')->selectAllWhere('gebruikers', $id);

        if(!empty($gebruiker[0])) {
            if(password_verify($_POST['wachtwoord'], $gebruiker[0]['Gebr_WachtW'])) {
                if($gebruiker[0]['Gebr_Rechten'] === "Admin") {
                    array_push($data['header'], App::get('processing')->createData('session', 'gebruiker', $gebruiker[0]['Gebr_Email']));
                    array_push($data['header'], App::get('processing')->createData('session', 'updateUser', 'true'));
                    array_push($data['header'], App::get('processing')->createRedirect('beheer'));
                    return App::view('beheer', $data);
                } else {
                    array_push($data['header'], App::get('processing')->createData('session', 'gebruiker', $gebruiker[0]['Gebr_Email']));
                    array_push($data['header'], App::get('processing')->createData('session', 'updateUser', 'true'));
                    array_push($data['header'], App::get('processing')->createRedirect('gebruik'));
                    return App::view('gebruik', $data);
                }
            } else {
                $error = "Uw inlog gegevens zijn niet correct, probeer het nogmaals!!";
                array_push($data['header'], App::get('processing')->createData('local', 'loginFailed', $error));
                array_push($data['header'], App::get('processing')->createRedirect('#login-pop-in'));
            }
        } else {
            $error = "Uw inlog gegevens zijn niet correct, probeer het nogmaals!!";
            array_push($data['header'], App::get('processing')->createData('local', 'loginFailed', $error));
            array_push($data['header'], App::get('processing')->createRedirect('#login-pop-in'));
        }

        return App::view('index', $data);
    }

    /* beheer():
            Functies voor de beheer pagina, en alle handeling die een pagina refresh verzorgen.
            Dit gaat bv om het laden van serie data, zodat er een serie overzicht in beeld komt.
            Maar ook het laden van albums in een serie, als er een serie bekeken word.

            $data - De data die terug moet naar de pagina.
                'header' - JS data die in de header moet, voor bv redirects of browser local/session storage items.
                'series' - de serie data voor PhP, die met $data['series'] of gewoon $series gebruikt kan worden op de pagina.
                'albums' - de albums data voor een serie, met de zelfde werking als de serie hier boven.

            De view wordt altijd terug gegeven met de data zoals die is samengestelt.
     */
    public function beheer() {
        $data = [
            'header' => [],
            'series' => [],
            'albums' => []
        ];

        /* De loop om serie data te laden, en om de albums in die serie te tellen. */
        if(empty($data['series'])) {
            $localSeries = App::get('database')->selectAll('series');
			$localAlbums = [];
			$count = 0;

            foreach($localSeries as $key => $value) {
			    if(isset($localSeries[$key])) {
				    array_push($data['series'], $localSeries[$key]);
                    $sqlId = ['Album_Serie' => $localSeries[$key]['Serie_Index'] ];
                    array_push($localAlbums, App::get('database')->selectAllWhere('albums', $sqlId));
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

        /* Loop om de albums van een serie te laden, en de juiste script data voor JS. */
        if(!empty($_POST['serie-index']) && !empty($_POST['serie-naam'])) {
            $tempAlbums = App::get('database')->selectAllWhere('albums', [ 'Album_Serie' => $_POST['serie-index'] ]);

            foreach($tempAlbums as $key => $value) {
                array_push($data['albums'], $value);
            }

            array_push($data['header'], App::get('processing')->createData('local', 'huidigeSerie', $_POST['serie-naam']));
            array_push($data['header'], App::get('processing')->createData('local', 'huidigeIndex', $_POST['serie-index']));
            array_push($data['header'], App::get('processing')->createData('local', 'serieWeerg', true));
        }

        return App::view('beheer', $data);
    }

    /* serieM():
            De functie voor het openen van de serie-maken pop-in, en het maken van een serie.
            Eerst kijk ik of het om het openen van de pop-in gaat, of het maken van een serie van die pop-in zelf.
            Als het om de naam-check gaat (openen van de pop-in), dan haal ik eerst alle series uit de database.
            Dan loop ik over die series heen, en kijk of de serie-naam (naam-check) uit de POST, overeenkomt met een al bestaande serie.
            Als die overeenkomt, dan zet ik naamError op waar, en evalueer ik dat variable voor de terugkoppeling.
            Als het waar is echo ik een json_encode string terug, die JS aangeeft dat de pop-in geopend moet worden.
            Als het niet waar is echo ik een kson_encode string terug, die via JS een terugkoppeling geeft naar de gebruiker.

            Als het om het maken van een serie gaat, sla ik eerst de verplichte form op in sqlData.
            Dan kijk ik welke andere data er aanwezig is, en zorg ik dat die op de juiste manier in sqlData komen te staan.
            Dan probeer ik die sqlData via de processing class op te slaan, en evalueer ik het resultaat daarvan.
            Als er foutmeldingen zijn, echo ik die met json_encode terug naar JS, zodat de gebruiker weet wat er mis ging.
            Als er geen foutmeldingen zijn, echo ik een geslaagd bericht terug met json_encode.
     */
    public function serieM() {
        $naamError = false;

        if(isset($_POST['naam-check'])) {
            $localSeries = App::get('database')->selectAll('series');

            foreach($localSeries as $key => $value) {
                if($value['Serie_Naam'] === $_POST['naam-check']) {
                    $naamError = true;
                }
            }

            if($naamError) {
                echo json_encode("Deze serie naam bestaat al, gebruik een andere naam gebruiken !");
            } else {
                echo json_encode("Serie-Maken");
            }
        } else {
            $sqlData = [ 'Serie_Naam' => $_POST['serie-naam'] ];

            if(isset($_POST['makers'])) {
                $sqlData['Serie_Maker'] = $_POST['makers'];
            } else {
                $sqlData['Serie_Maker'] = '';
            }

            if(isset($_POST['opmerking'])) {
                $sqlData['Serie_Opmerk'] = $_POST['opmerking'];
            } else {
                $sqlData['Serie_Opmerk'] = '';
            }
    
            $newSerie = App::get('processing')->set_Object('series', $sqlData);
    
            if(isset($newSerie)) {
                echo json_encode($newSerie);
            } else {
                echo json_encode("Het toevoegen van: " . $_POST['serie-naam'] . " is gelukt !");
            }
        }
    }

    /* albumT():
            De functie voor het toevoegen van een album aan de database.
            Ik sla direct de required data op vanuit de POST, en maak een leeg errorcheck variable.
            Dan check ik welke andere data aanwezig is, en voeg die toe aan de albumData.
            Voor de album cover, moet ik de inhoud van de image omzetten naar base64, en die in een string zetten met het juiste bestandstype.
            Zodat ik die string als blob kan opslaan, en direct kan gebruiken om de cover weer te geven in HTML.
            Vervolgens probeer ik de albumData via de processing class aan de database toe tevoegen.
            Dan kijk ik of dat gelukt is of niet, en aan de hand daarvan, zet ik de foutmeldingen om naar het juiste format voor JS.
            Als het niet gelukt is, dan echo ik de foutmeldingen terug met json_encode naar de caller.
            Als het wel gelukt is, dan echo ik een gepaste melding terugn met json_encode naar de caller.
     */
    public function albumT() {
        $albumData = [
            'Album_Naam' => $_POST['album-naam'],
            'Album_ISBN' => $_POST['album-isbn'],
            'Album_Opm' => 'W.I.P.'
        ];
        
        if(isset($_POST['serie-index'])) {
            $albumData['Album_Serie'] = $_POST['serie-index'];
        }

        if(isset($_POST['serie-naam']) && !isset($data['serie-index'])) {
            $tempSerie = App::get('database')->selectAllWhere('series', ['Serie_Naam' => $_POST['serie-naam']])[0];
            $albumData['Album_Serie'] = $tempSerie['Serie_Index'];
        }

        if(isset($_POST['album-nummer'])) {
            $albumData['Album_Nummer'] = $_POST['album-nummer'];
        }

        if(isset($_POST['album-datum'])) {
            $albumData['Album_UitgDatum'] = $_POST['album-datum'];
        }
        
        if(isset($_FILES['album-cover'])) {
            $fileName = basename($_FILES["album-cover"]["name"]);
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

            $image = $_FILES['album-cover']['tmp_name'];
            $imgContent = file_get_contents($image);
            $dbImage = 'data:image/'.$fileType.';charset=utf8;base64,'.base64_encode($imgContent);
                
            $albumData['Album_Cover'] = $dbImage;
        }

        $newAlbum = App::get('processing')->set_Object('albums', $albumData);

        if(isset($newAlbum)) {
            $returnData = [];

            foreach($newAlbum as $key => $value) {
                if($key === 'Album_Naam' && isset($value)) {
                    $returnData['aNaamFailed'] = $newAlbum['Album_Naam'];
                }

                if($key === 'Album_ISBN' && isset($value)) {
                    $returnData['aIsbnFailed'] = $newAlbum['Album_ISBN'];
                }
            }

            echo json_encode($returnData);
        } else {
            echo json_encode("Toevoegen van het Album: " . $_POST['album-naam'] . " is gelukt.");
        }
    }

    /* albumV():
            Deze simpele functie verwijderd het album uit de lijst van albums.
            En geeft een echo met json_encode string terug voor de terugkoppeling.
     */
    public function albumV() {
        App::get('processing')->remove_Object('albums', ['Album_Index' => $_POST['album-index']], ['Album_Naam' => $_POST['album-naam']]);

        echo json_encode("Verwijderen van {$_POST['album-naam']}, is gelukt.");
    }

    /* albumB():
            Deze functie zorgt ervoor dat album data die bewerkt is, word gecontroleerd en daarna opgeslagen in de database.
            Om de juiste informatie te krijgen, heb ik de serie-index nodig van het huidige album, dus ik vraag direct het huidige bewerkte album aan uit de database.
            Dan sla ik alle verplichte velden direct op inde de $albumData, en voor de rest kijk ik eerst of die in de POST staan.

            Voor de album-cover, moet ik iets meer doen, en dit lijkt wat omslachtig.
            Ik pak de bestandsnaam en type, zodat ik de inhoud van het bestand, om kan zetten naar base64 code.
            Die base 64 code, zet ik in een kant en klare string met de filetype erbij, zodat ik die direct in een HTML tag kan zetten.
            En de cover in zijn geheel, als blob kan opslaan in de database, en niet een lokale kopie hoef te bewaren.
            Dit was de meest eenvoudige manier die ik kon vinden, om dit doel te berijken, er zijn vast betere manier om dit te doen.

            De foutmeldingen worden behandeld in de 'Processing' class, voordat de data daadwerkelijk word opgeslagen.
            En aan de hand van wat er terug komt, echo de juiste data terug met een json_encode, zodat Javascript het kan uitlezen.
     */
    public function albumBew() {
        $albumData = [];
        $erroCheck;

        $tempAlbum = App::get('database')->selectAllWhere('albums', ['Album_Index' => $_POST['album-index']])[0];

        $albumData['Album_Serie'] = $tempAlbum['Album_Serie'];
        $albumData['Album_Naam'] = $_POST['album-naam'];
        $albumData['Album_ISBN'] = $_POST['album-isbn'];

        if(isset($_POST['album-nummer'])) {
            $albumData['Album_Nummer'] = $_POST['album-nummer'];
        }

        if(isset($_POST['album-datum'])) {
            $albumData['Album_UitgDatum'] = $_POST['album-datum'];
        }

        if(!empty($_FILES['album-cover']['name'])) {
            $fileName = basename($_FILES["album-cover"]["name"]);
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

            $image = $_FILES['album-cover']['tmp_name'];
            $imgContent = file_get_contents($image);
            $dbImage = 'data:image/'.$fileType.';charset=utf8;base64,'.base64_encode($imgContent);

            $albumData['Album_Cover'] = $dbImage;
        }

        $infoAlbum = App::get('processing')->update_Object('albums', ['Album_Index' => $_POST['album-index']], $albumData);

        if(isset($infoAlbum) && $infoAlbum != 0) {
            echo json_encode($infoAlbum);
        } else {
            echo json_encode("Het album: " . $_POST['album-naam'] . " is bijgewerkt.");
        }
    }

    /* serieBew():
            Deze functie zorgt ervoor dat serie data die bewerkt is, ge-update en ge-controleerd word.
            Er is geen voorbewerking nodig voor de POST data, ik moet allen de 'key' een andere naam geven voor de database velden.
            Als er foutmeldingen zijn, echo ik die terug met een json_encode, zodat JS het kan lezen.
            Als er geen foutmeldingen zijn, echo ik een jscon_encode bericht terug, zodat de gebruiker kan zien dat het gelukt is.
     */
    public function serieBew() {
        $serieData = [
            'Serie_Naam' => $_POST['naam'],
            'Serie_Maker' => $_POST['makers'],
            'Serie_Opmerk' => $_POST['opmerking']
        ];

        $checkSerie = App::get('processing')->update_Object('series', ['Serie_Index' => $_POST['index']], $serieData);

        if(isset($checkSerie)) { 
            echo json_encode($checkSerie);
        } else {
            echo json_encode('Het bijwerken van de Serie is gelukt !');
        }
    }

    /* serieVerw():
            Deze functie verwijdert een serie en al haar albums uit de database.
            Alle benodigde gebruikers validaties zijn all gedaan, en error checks zijn niet nodig.
            Dus een echo met json_encode() string dat het gelukt is, is het enige dat terug hoeft naar de caller.
     */
    public function serieVerw() {
        $serieId = [ 'Serie_Index' => $_POST['serie-index'], 'Serie_Naam' => $_POST['serie-naam'] ];

        App::get('processing')->remove_Object('albums', ['Album_Serie' => $_POST['serie-index']]);
        App::get('processing')->remove_Object('series', $serieId);

        echo json_encode("Verwijderen van {$_POST['serie-naam']}, en alle albums is gelukt");
    }

    /* adminReset():
            Deze functie doet niet meer dan het updaten van gebruikers data, aangezien er duidelijk om bevestiging gevraagd word op de pagina zelf.
            Er is nog ruimte voor een check in de Processing::update_Object() functie, maar momenteel zals deze functie altijd slagen.
     */
    public function adminReset() {
        $reset = App::get('processing')->update_Object('gebruikers', ['Gebr_Email' => $_POST['email']], ['Gebr_WachtW' => password_hash($_POST['wachtwoord1'], PASSWORD_BCRYPT)]);

        if(isset($reset)) {
            echo json_encode('De wachtwoord reset is niet gelukt');
        } else {
            echo json_encode('De wachtwoord reset is geslaagd');
        }
    }

    /* gebruik():
            Deze functie verwerkt alle POST request die niet handig zijn via een JS Fetch.
            En is daarom om iets uigebreider dan de GET variant in de PagesController.
            Bij elke afzonderlijk loop heb ik een comment gemaakt, die uitlegt wat het doel van die loop is.

            De $data array heeft de huidige opmaak en functie:
                'header' -> JS script data die ik in de header zet, om gegevens in de local storage van de browser te zetten.
                'series' -> De serie data die ik in PhP gebruik om de juiste gegevens weer te geven (selecteren van een serie).
                'albums' -> De album data die ik in PhP gebruik om de juiste albums van een serie in beeld te zetten.
                'collecties' -> De collectie data die ik in PhP gebruik, om aan te geven of een album in het bezit is van een gebruiker of niet.
     */
    public function gebruik() {
        $data = [
            'header' => [],
            'series' => [],
            'albums' => [],
            'collecties' => []
        ];

        /* Deze loop zorgt er voor, dat er altijd 'serie' data is, en altijd op een manier dat ik er makkelijk gebruik van kan maken */
        if(empty($data['series'])) {
            $temp = App::get('database')->selectAll('series');
            foreach($temp as $key => $value) {
                array_push($data['series'], $temp[$key]);
            }
        }

        /* Deze loop is voor het ophalen van de 'albums' & 'collecties' data, als er een serie selectie is gemaakt */
        if(isset($_POST['serie_naam'])) {
            $serieObject = App::get('database')->selectAllWhere('series', [ 'Serie_Naam' => $_POST['serie_naam'] ])[0];
            $serieIndex = [ 'Album_Serie' => $serieObject['Serie_Index'] ];

            /* Dan zorg ik dat de 'albums' data op de juiste plek, en in de juiste format komt te staan */
            if(empty($data['albums'])) {
                $temp = App::get('database')->selectAllWhere('albums', $serieIndex);
                foreach($temp as $key => $value) {
                    array_push($data['albums'], $temp[$key]);
                }
            }

            /* Dan zorg ik dat de 'collecties' data op de juiste plek, en in de juiste format komt te staan */
            if(empty($data['collecties'])) {
                $gebrObject = App::get('database')->selectAllWhere('gebruikers', [ 'Gebr_Email' => $_POST['gebr_email'] ])[0];
                $collecties = App::get('database')->selectAllWhere('collecties', [ 'Gebr_Index' => $gebrObject['Gebr_Index'] ]);

                foreach($collecties as $key => $value) {
                    array_push($data['collecties'], $collecties[$key]);
                }
            }

            /* En ik geef ook de huidige serie-naam mee terug voor javascript */
            array_push($data['header'], App::get('processing')->createData('local', 'huidigeSerie', "{$_POST['serie_naam']}"));
        }

        /* De standaard return view + data functie, zodat alles op de pagina terecht komt */
        return App::view('gebruik', $data);
    }

    /* valUsr():
            Deze functie doet niet meer dat kijken of een gebruiker bestaat, op basis van het aangeleverde e-mail adress.
            Als de gebruiker niet bestaat echo ik een 'Invalid User' string terug, en als die wel bestaat een 'Valid User' string.
            In het geval dat er geen e-mail is, echo ik een 'Validatie Mislukt' string terug.
            Bij alle gevallen, komt de melding terug in een JS Fetch, en dus is de json_encode nodig om het leesbaar te maken voor JS.
     */
    public function valUsr() {
        if(isset($_POST['gebr_email'])) {
            $tempGebr = App::get('database')->selectAllWhere("gebruikers", [ 'Gebr_Email' => $_POST['gebr_email'] ]);

            if(!isset($tempGebr) || empty($tempGebr)) {
                echo json_encode('Invalid User');
            } else {
                if($_POST['gebr_email'] === $tempGebr[0]['Gebr_Email']) {
                    echo json_encode('Valid User');
                } else {
                    echo json_encode('Invalid User');
                }
            }
        } else {
            echo json_encode('Validatie mislukt!');
        }
    }

    /* albSta():
            Deze functie zet albums op aan-/afwezig voor de gebruiker zijn/haar collecties.
            Met de POST data vraag ik de juiste gegevens aan, zodat ik de juiste index waardes heb voor het toevoegen.
            Aan de hand van de 'aanwezig' waarde, bepaal ik wat voor data ik naar de database moet sturen of verwijderen.
            En op basis van wat er in $newcol gezet word door de Processing class, bepaal ik wat er terug moet naar de caller.

            Alle data die terug moet, kom terug in een JS fetch, en dus moet json_encode() worden zodat het uit te lezen is.
            De foutmeldingen worden afgehandeld door de Processing class.
     */
    public function albSta() {
        $tempGebr = App::get('database')->selectAllWhere("gebruikers", [ 'Gebr_Email' => $_POST['gebr_email'] ])[0];;
        $tempSerie = App::get('database')->selectAllWhere("series", [ 'Serie_Naam' => $_POST['serie_naam'] ])[0];
        $tempAlbum = App::get('database')->selectAllWhere("albums", [ 'Album_Naam' => $_POST['album_naam'], 'Album_Serie' => $tempSerie['Serie_Index'] ])[0];

        if($_POST['aanwezig'] === 'true') {
            $tempCol = [
                "Gebr_Index" => $tempGebr["Gebr_Index"],
                "Alb_Index" => $tempAlbum["Album_Index"],
                "Alb_Staat" => "",
                "Alb_DatumVerkr" => date('Y-m-d'),
                "Alb_Aantal" => 1,
                "Alb_Opmerk" => ""
            ];

            $newCol = App::get('processing')->set_Object('collecties', $tempCol);

            if(isset($newCol)) {
                echo json_encode($newCol['Col_Toev']);
            } else {
                echo json_encode('Toevoegen van het album aan de collectie is gelukt');
            }
        } else if ($_POST['aanwezig'] === 'false') {
            $tempCol = [
                "Gebr_Index" => $tempGebr["Gebr_Index"],
                "Alb_Index" => $tempAlbum["Album_Index"]
            ];

            $newCol = App::get('processing')->remove_Object('collecties', $tempCol);

            if(isset($newCol)) {
                echo json_encode($newCol['Col_Verw']);
            } else {
                echo json_encode('Verwijderen van het album uit de collectie is gelukt');
            }
        }
    }
}
?>