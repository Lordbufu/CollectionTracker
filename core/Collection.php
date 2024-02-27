<?php
namespace App\Core;

use App\Core\App;

//  TODO: Consider some kind of error reporting, for when the DB querries return nothing in certain edge cases.
class Collection {
    protected $albums;
    protected $series;
    protected $collections;

    /* getSetId($name):
            Get serie index based on serie name.

            $name (String)  : The name of the series we want the index for.

            Return Value: INT
     */
    protected function getSerId($name) {
        $tempSerie = App::get('database')->selectAllWhere('series', [
            'Serie_Naam' => $name
        ])[0];

        return $tempSerie['Serie_Index'];
    }

    /* getAlbId($name):
            Get serie index based on album name.

            $name (String)  : The name of the album we want the index for.

            Return Value: INT
     */
    protected function getAlbId($name) {
        $tempAlbum = App::get('database')->selectAllWhere('albums', [
            'Album_Naam' => $name
        ])[0];
        
        return $tempAlbum['Album_Index'];
    }

    /*  getSeries():
            Simple get all series from DB, and return them to the caller.

            Return Value: Multi-Dimensional Array.
     */
    public function getSeries() {
        $this->series = App::get('database')->selectAll('series');
        return $this->series;
    }

    // set serie in database for the admin
    public function setSerie($data) { }

    // remove serie in database for the admin
    public function remSerie($data) { }

    /*  getAlbums($seriesId):
            This function gets all albums from a series, based on a serie name.

            $name (String)  - Expecting the serie name that i need to get albums from.

            Return Value: Multi-Dimensional Array.
     */
    public function getAlbums($name) {
        $id = [ 'Album_Serie' => $this->getSerId($name) ];

        $this->albums = App::get('database')->selectAllWhere('albums', $id);

        return $this->albums;
    }

    // set album in database for the admin
    public function setAlbum($data) { }

    // remove album in database for the admin
    public function remAlbum($data) { }

    /*  getColl($userId):
            Get collection from specific user from the database.

            $userId (int)   - User id from the session data.
            
            Return Type: Multi-Dimensional Array.
     */
    public function getColl($userId) {
        $id = [ 'Gebr_Index' => $userId ];

        $this->collection = App::get('database')->selectAllWhere('collecties', $id);

        return $this->collection;
    }

    /*  setColl($data):
            Function to set collection data, so the user can add items to a collection.

            $data (Associative Array)   : The data required to make a collection database entry.

            Return Value: boolean.
     */
    public function setColl($data) {
        $this->collections = App::get('database')->selectAllWhere('collecties', [   // Get all collections for this user.
            'Gebr_Index' => $data['Gebr_Index']
        ]);

        foreach($this->collections as $key => $value) {                             // First we check if the album was already added in the users collection.
            if($value === $this->getAlbId($data["Album_Naam"])) {
                return FALSE;
            }
        }

        $tempCol = [                                                                // Then we prepare the data that needs to be added,
            "Gebr_Index" => $data["Gebr_Index"],
            "Alb_Index" => $this->getAlbId($data["Album_Naam"]),
            "Alb_Staat" => "",
            "Alb_DatumVerkr" => date('Y-m-d'),
            "Alb_Aantal" => 1,
            "Alb_Opmerk" => ""
        ];

        App::get('database')->insert('collecties', $tempCol);                       // and store the data in the database.

        return TRUE;                                                                // and indicate the data was stored.
    }

    /*  remColl($data):
            Function to remove specifc collection data from the database.

            $data (Associative Array)   - The data required to remove series data.

            Return Type: boolean.
     */
    public function remColl($data) {
        $albInd = $this->getAlbId($data["Album_Naam"]);

        $colIds = [
            "Gebr_Index" => $data["Gebr_Index"],
            "Alb_Index" => $this->getAlbId($data["Album_Naam"])
        ];

        App::get('database')->remove('collecties', $colIds);

        return TRUE;
    }
}

?>