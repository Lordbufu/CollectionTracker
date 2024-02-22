<?php
namespace App\Core;

use App\Core\App;

class Processing {
    /* createData($type, $key, $value):
            Parameters:
                $type (string)          - The storage type i want to use 'local' or 'session'.
                $key (string)           - The name/key the item should have in said storage.
                $value (string/array)   - The value that should be stored, with said key.
     */
    public static function createData($type, $key, $value) {
        switch($type) {
            case 'local':
                $temp = "<script>localStorage.setItem('$key', '$value');</script>";
                return $temp;
            case 'session':
                $temp = "<script>sessionStorage.setItem('$key', '$value');</script>";
                return $temp;
        }
    }

    /* createRedirect($path):
            Parameters:
                $path (string)       - The URI i want to redirect to, 'beheer' would be equal to the PhP get route '/beheer'.
     */
    public static function createRedirect($path) {
        $location = "https://{$_SERVER['SERVER_NAME']}/{$path}";
        $temp = "<script>window.location.replace('$location')</script>";
        return $temp;
    }

    /*  set_Object($naam, $data):
            Parameters:
                $naam (string)          - The name of the database table the data should be set in.
                $data (assoc array)     - The data i want to store in the database, with the key being the field name.
            
            Scoped Variables:
                $errorMsg (assoc array) - The error data that will be returned, for user feedback.
                $..Err (string)         - Collection of potential errors, stored here for sorter code.
     */
    public static function set_Object($naam, $data) {
        $errorMsg = [];
        $userNameErr = "Deze gebruiker bestaat al.";
        $userEmailErr = "E-mail adres reeds ingebruik.";
        $albumErr = "Deze Album Naam bestaat al in de huidige Serie, kies een andere naam.";
        $isbnErr = "Deze Album ISBN bestaat al, controleer of deze juist is.";
        $serieErr = "Deze Serie Naam staat reeds in de Database!!";
        $collErr = "Dit Album is al aanwezig in de huidige Collectie!!";

        switch($naam) {
            case "gebruikers":
                // Get all user in database
                $tempUsers = App::get('database')->selectAll('gebruikers');

                // Check for duplicate names and e-mails.
                if(!empty($tempUsers)) {
                    foreach($tempUsers as $key => $user) {
                        if($user['Gebr_Naam'] === $data['Gebr_Naam']) {
                            $errorMsg['gebrNaam'] = $userNameErr;
                        }

                        if($user['Gebr_Email'] === $data['Gebr_Email']) {
                            $errorMsg['gebrEmail'] = $userEmailErr;
                        }
                    }
                }

                // Evaluate if there was and error, and return those.
                if(!empty($errorMsg)) {
                    return $errorMsg;
                // Filter the user name, before putting in the database.
                } else {
                    $data['Gebr_Naam'] = htmlspecialchars($data['Gebr_Naam']);
                    App::get('database')->insert($naam, $data);
                    return;
                }
            case "albums":
                $tempAlbums = App::get('database')->selectAll('albums');

                // Check for double names in the same serie, and if the ISBN was already in the database.
                if(!empty($tempAlbums)) {
                    foreach($tempAlbums as $key => $value) {
                        if($value['Album_Naam'] == $data['Album_Naam']) {
                            if($value['Album_Serie'] == $data['Album_Serie']) {
                                $errorMsg["Album_Naam"] = $albumErr;
                            }
                        }

                        if($value['Album_ISBN'] == $data['Album_ISBN']) {
                            $errorMsg["Album_ISBN"] = $isbnErr;
                        }
                    }
                }

                // If errors are found, return them to the caller,
                if(!empty($errorMsg)) {
                    return $errorMsg;
                // if not filter strings for special characters
                } else {
                    foreach($data as $key => $value) {
                        if($data[$key] === 'Album_Naam') {
                            $data[$key] = htmlspecialchars($value);
                        
                        }
                        if($data[$key] === 'Album_Opm') {
                            $data[$key] = htmlspecialchars($value);
                        }
                    }

                    App::get('database')->insert($naam, $data);
                    return;
                }
            case "series":
                $tempSerie = App::get('database')->selectAll('series');

                // Check for double names for all series.
                if(!empty($tempSerie)) {
                    foreach($tempSerie as $key => $value) {
                        if($value['Serie_Naam'] == $data['Serie_Naam']) {
                            $errorMsg['Serie_Naam'] = $serieErr;
                        }
                    }
                }

                // If errors are found, return them to the caller,
                if(!empty($errorMsg)) {
                    return $errorMsg;
                // if not filter strings for special characters
                } else {
                    foreach($data as $key => $value) {
                        if($data[$key] === 'Serie_Naam') {
                            $data[$key] = htmlspecialchars($value);
                        }

                        if($data[$key] === 'Serie_Maker') {
                            $data[$key] = htmlspecialchars($value);
                        }

                        if($data[$key] === 'Serie_Opmerk') {
                            $data[$key] = htmlspecialchars($value);
                        }
                    }

                    App::get('database')->insert($naam, $data);
                    return;
                }
            case "collecties":
                $tempCol = App::get('database')->selectAll('collecties');

                // check if album was already added to this collection
                if(!isset($tempCol)) {
                    foreach($tempCol as $key => $value) {
                        if($tempCol[$key]['Alb_Index'] === $data['Alb_Index'] ) {
                            $submit = false;
                            $errorMsg['Col_Toev'] = $collEr;
                        }
                    }
                }

                if(!empty($errorMsg)) {
                    return $errorMsg;
                } else {
                    App::get('database')->insert($naam, $data);
                    return;
                }
        }
    }

    /*  update_Object($naam, $id, $data):
            Parameters:
                $naam (string)      - The name of the database table i want to update.
                $id (assoc array)   - The identifier of the data i want to update (usually the name and/or index).
                $data (assoc array) - The actual data that needs to be updated, with the key being the field name.
            
            Scoped Variables:
                $errorMsg (assoc array) - The error data that will be returned, for user feedback.
                $..Err (string)          - Collection of potential errors, stored here for sorter/more readable code lines.
     */
    public static function update_Object($naam, $id, $data) {
        $errorMsg = [];
        $serieErr = "Deze Serie Naam bestaat al, kies een andere naam.";
        $albumErr = "Deze Album Naam bestaat al in de huidige Serie.";
        $isbnErr = "Dit ISBN nummer is al gebruikt voor een album.";

        switch($naam) {
            case "gebruikers":
                App::get('database')->update($naam, $data, $id);
                return;
            case "albums":
                $tempAlbums = App::get('database')->selectAll('albums');
                $huidigeAlbum = App::get('database')->selectAllWhere('albums', $id)[0];

                // Check for double names in the same serie, and if the ISBN was already in the database.
                if(!empty($tempAlbums)) {
                    foreach($tempAlbums as $key => $value) {
                        if($value['Album_Naam'] == $data['Album_Naam']) {
                            if($value['Album_Serie'] == $huidigeAlbum['Album_Serie']) {
                                if($value['Album_Index'] != $huidigeAlbum['Album_Index']) { $errorMsg['albumNaam'] = $albumErr; }
                            }
                        }

                        if($value['Album_ISBN'] == $data['Album_ISBN']) {
                            if($value['Album_Index'] != $huidigeAlbum['Album_Index']) { $errorMsg['albumIsbn'] = $isbnErr; }
                        }
                    }
                }

                if(!empty($errorMsg)) {
                    return $errorMsg;
                } else {
                    // filter strings for special characters
                    foreach($data as $key => $value) {
                        if($data[$key] === 'Album_Naam') { $data[$key] = htmlspecialchars($value); }
                        if($data[$key] === 'Album_Opm') { $data[$key] = htmlspecialchars($value); }
                    }

                    App::get('database')->update($naam, $data, $id);
                    return;
                }
            case "series":
                $tempSeries = App::get('database')->selectAll($naam);
                $huidigeSerie = App::get('database')->selectAllWhere($naam, $id)[0];

                // Check for doubble names, with a extra check for if there was no name.
                if(!empty($tempSeries)) {
                    foreach($tempSeries as $key => $value) {
                        if(!empty($huidigeSerie)) {
                            if(htmlspecialchars($value['Serie_Naam']) === htmlspecialchars($data['Serie_Naam'])) {
                                if($huidigeSerie['Serie_Index'] != $value['Serie_Index']) { $errorMsg['Serie_Naam'] = $serieErr; }
                            }
                        } else { $errorMsg['Serie_Naam'] = 'Er ging iets mis bij het controleren van de serie naam !'; }
                    }
                }

                if(!empty($errorMsg)) {
                    return $errorMsg;
                } else {
                    // filter strings for special characters
                    foreach($data as $key => $value) {
                        if($data[$key] === 'Serie_Naam') { $data[$key] = htmlspecialchars($value); }
                        if($data[$key] === 'Serie_Maker') { $data[$key] = htmlspecialchars($value); }
                        if($data[$key] === 'Serie_Opmerk') { $data[$key] = htmlspecialchars($value); }
                    }

                    App::get('database')->update($naam, $data, $id);
                    return;
                }
        }
    }

    /*  remove_Object($naam, $id1 = [], $id2 =[]):
            Parameters:
                $naam (string)      - The name of the database table i want to remove data from.
                $id (assoc array)   - The first identifier for the data that i want to remove.
                $id2 (assoc array)  - The second identifier for the data that i want to remove (initially empty, because its not always required).
     */
    public static function remove_Object($naam, $id, $id2 = []) {
        switch($naam) {
            case "albums":
                $data = array_merge($id, $id2);
                App::get('database')->remove($naam, $data);
                return;
            case "series":
                $data = array_merge($id, $id2);
                App::get('database')->remove($naam, $data);
                return;
            case "collecties":
                App::get('database')->remove($naam, $id);
                return;
        }

    }
}
?>