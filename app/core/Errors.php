<?php

namespace App\Core;

/* An interface to replace exception with 'useable' user feedback. */
class Errors {
    /* All database related costum errors. */
    protected $database = [
        'init-error' => 'De Database verbinding is mislukt, neem contact op met uw Administrator!',
        'default-content' => 'Niet all database inhoud kon worden gemaakt, neem contact op met uw Administrator!',
        'find-error' => 'De opgevraagde informatie, kon niet gevonden worden in de database!',
        'store-error' => 'Er ging iets mis met het opslaan van uw gegevens, neem contact op met uw Administrator!'
    ];

    /* Validation specific errors. */
    protected $validation = [
        'user-name' => 'Uw gebruikers naam voldoet niet aan de eisen, zorg dat het tussen de 5 en 35 tekens is.',
        'user-mail' => 'Het Email adress dat u opgegeven heeft, is geen geldig email adress.',
        'user-pw' => 'Het wachtwoord voldoet niet aan de gestelde eisen, zorg dat het tussen de 7 en 35 tekens is.',
        'pw-sec' => 'Uw wachtwoord bevat aleen letters en\of nummers, gebruik aub een combinatie van verschillende dingen.',
        'naam-input' => 'De naam input voldoet niet aan de eisen, probeer het tussen de 5 en 50 tekens te houden.',
        'autheur' => 'De makers input voldoet niet aan de eisen, probeer het tussen de 7 en 50 tekens te houden.',
        'opmerking' => 'De opmerking voldoet niet aan de eisen, probeer het tussen de 1 en 254 tekens te houden.',
        'isbn' => 'De ISBN die is ingevoerd, is niet lang genoeg, er worden 10 of 13 cijfers verwacht !'
    ];

    /* FileHandler specific errors. */
    protected $fileHand = [
        'no-file' => 'Het bestand voor de plaatje/cover, is verloren gegaan, neem aub contact op met uw administrator!',
        'proc-fail' => 'Er ging iets mis met het verwerken van u plaatje/cover, neem aub contact op met uw administrator!',
        'no-string' => 'Er is geen cover string gevonden, uw cover is dus niet opgeslagen, neem aub contact op met uw administrator!'        
    ];

    /* User specific errors */
    protected $user = [
        'user-dupl' => 'De gegevens die u gebruikt, bestaan al in onze database, probeer het nogmaals.',
        'user-fetch' => 'Het is mislukt om uw gebruikers gegevens te laden, neem contact op met uw Administrator!'
    ];

    /* Login specific errors. */
    protected $login = [
        'failed' => 'Uw inlog gegevens waren niet correct, probeer het nogmaals!'
    ];

    /* Reeks (series) specific errors. */
    protected $reeks = [
        'find-error' => 'De opgevraagde reeks informatie, kon niet gevonden worden in de database!',
        'store-error' => 'Kan de reeks niet aanmaken, als die blijf gebeuren neem dan aub contact op met uw Administrator!',
        'duplicate' => 'De reeks die je probeert te maken, bestaat al in de database, probeer aub een andere naam!',
        'rem-fail' => 'Ergens ging iets mis, en de reeks is niet verwijdert, neem aub contact op met uw Administrator!'
    ];

    /* Items (albums) specific errors. */
    protected $items = [
        'find-error' => 'De opgevraagde item informatie, kon niet gevonden worden in de database!',
        'store-error' => 'Kan het item niet aanmaken, als die blijf gebeuren neem dan aub contact op met uw Administrator!',
        'duplicate' => 'Het item dat je probeert te maken, bestaat al in de database, probeer aub een andere naam!',
        'rem-fail' => 'Ergens ging iets mis, en de items zijn niet verwijdert, neem aub contact op met uw Administrator!'
    ];

    /* Collectie specific errors. */
    protected $collectie = [
        'set-error' => 'Er kan geen collectie data opgehaald worden, neem contact op met uw Administrator!',
        'dup-error' => 'Dit item zit al in uw collectie, als dit bericht blijft terug komen neem dan contact op met uw Administrator!',
        'rem-fail' => 'Ergens ging iets mis, en de collecties zijn niet opgeschoont, neem aub contact op met uw Administrator!'
    ];

    /* Isbn (barcode scan) specific errors. */
    protected $isbn = [
        'no-items' => 'Er zijn geen items gevonden in de Google API voor deze barcode, probeer aub een andere!',
        'no-request' => 'Er is geen verzoek gedaan bij de Google API, neem contact op met uw Administrator!',
        'no-match' => 'Er zijn geen items in deze reeks gevonden, die overeenkomen met de gescande ISBN!',
        'search-error' => 'Er ging iets mis tijdens het zoeken op die ISBN nummer, probeer het aub nogmaals.',
        'choice-fail' => 'De google API werkt wat raar soms, probeer het aub nogmaals.'
    ];

    /* Form validation specific errors. */
    protected $forms = [
        'input-missing' => 'Er zijn wat ingevulde gegevens verloren gegaan, probeer het nogmaals!'
    ];

    /* Processing form data errors. */
    protected $processing = [
        'failed' => 'De data die u ingevult heeft, kon niet verwerkt worden, neem contact op met uw administrator als dit blijft gebeuren!'
    ];

    /*  getError($object, $type):
            Return the correct error type, for the requested object.
                $object (String)    - The object array name of where the error happenend ('user', 'database' etc).
                $type (String)      - The key of the error string, inside the request object array.
            
            Return Value: String.
     */
    public function getError($object, $type) {
        return $this->$object[$type];
    }
}