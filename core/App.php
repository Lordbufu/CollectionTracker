<?php

namespace App\Core;

/*  De App class:
        Hier staan alle App specifieke functies, voor dit project ook de relaties met gebruikers, verzamelingen en albums etc.
        Normaal zouden die in een apparte class staan, maar een goed ontwerp is iets meer werk dan ik zou willen, dus staat het allemaal hier.
 */
class App {
    /*  $registery:
            Array voor het opslaan van gegevens (gebaseerd op de Laracast cursus), vnml database config gegevens.
     */
    protected static $registry = [];

    /*  bind($key, $value):
            De functie die in de bootstrap verbinding maakt met de database (gebaseerd op de Laracast cursus).
            En bind de gegevens aan het register, op basis van $key en $value.
     */
    public static function bind($key, $value) {
        static::$registry[$key] = $value;
    }

    /*  get($key):
            De functie die samen met bind(), ervoor zorgt dat de databse verbinding tot stand komt (gebaseerd op de Laracast cursus).
            Op basis van de $key, kan deze functie dan de juiste value op halen uit het register.
     */
    public static function get($key) {
        if(! array_key_exists($key, static::$registry)) {
            throw new Exception("No {$key} is bound in the container.");
        }
        return static::$registry[$key];
    }

    /*  view($name, $data = []):
            De functie die een hele view terug geeft aan de browser, voornamelijk voor GET requests (gebaseerd op de Laracast cursus).
            De bestand extensie & locatie, zijn hardcoded zoals je normaal in een kant en klaar framework ook zou hebben (zoals bv Laravel).

                $name   - De naam van het document, zonder bestands extensie.
                $data   - Een lege Array, zodat data in verschillende formats kan worden doorgegeven.
            
                Bestands extensie   : '*.view.php'
                Bestands locatie    : '../app/views/'

            Zou ik bv de index willen aanvragen voor de landings pagina, dan doe ik dat als volgt:
                App::view('index');
            
            De data die dan terug komt is dan als volgt:
                return require "../app/view/index.view.php"
            
            extract($data):
                Dit pakt de data in array formaat, uit naar losse variable, op basis van de $key van de array.

                Als ik dus de volgende data heb:
                    $data = [ 'gebruiker' => 'piet' ];

                Komt deze data als volgt aan op de aangevraagde pagina:
                    $gebruiker = 'piet';
     */
    public static function view($name, $data = []) {
        extract($data);
        return require "../app/views/{$name}.view.php";
    }

    /*  redirect($path):
            Soms zijn de view() en getHTML() functie, niet de perfecte oplossing voor PhP routing.
            Deze redirect maakt het dan mogelijk, om bv na een POST request naar '/login', terug te gaan naar '/' (de landingspagina).
            De redirect wordt gedaan via de HTML header, en werkt daardoor veder als een normale PhP routing request.
            En ik ga uit van een automatische redirect naar HTTPS aan de server side.

                $path                   - De route die we willen gebruiken.
                $_SERVER['SERVER_NAME'] - De server naam, zodat de URL logische\kloppend blijft.
            
            Deze functie wil niet altijd goed werken met URI Anchors, zoals bv 'localhost\#login-form'.
            Dit is voornamelijk het geval, als er gebruik word gemaakt van JavaScript, om zaken af te handelen op de geladen pagina.
     */
    public static function redirect($path, $data = []) {
        extract($data);
        header("location:http://{$_SERVER['SERVER_NAME']}/{$path}");
        return;
    }
}
?>