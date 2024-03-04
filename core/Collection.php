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
    public function getAlbId($name) {
        $tempAlbum = App::get('database')->selectAllWhere('albums', [
            'Album_Naam' => $name
        ])[0];
        
        return $tempAlbum['Album_Index'];
    }

    // W.I.P.
    protected function countAlbums() {
        // Loop over all stored series,
        foreach($this->series as $key => $value) {
            // Then count the albums in the data base, and store it with each serie.
            $this->series[$key]['Album_Aantal'] = App::get('database')->countAlbums($value['Serie_Index']);
        }

        // return to caller.
        return;
    }

    /*  getSeries():
            Simple get all series from DB, add a album count to each serie, and return them all to the caller.

            Return Value: Multi-Dimensional Array.
     */
    public function getSeries() {
        $this->series = App::get('database')->selectAll('series');
        $this->countAlbums();
        return $this->series;
    }

    // W.I.P.
    public function getSerName($ind) {
        if(!isset($this->series)) {
            $this->getSeries();
        }

        foreach($this->series as $index => $serie) {
            if($ind == $serie['Serie_Index']) {
                return $this->series[$index]['Serie_Naam'];
            }
        }
    }

    // W.I.P.
    public function getSerInd($name) {
        if(!isset($this->series)) {
            $this->getSeries();
        }

        foreach($this->series as $index => $serie) {
            if($name == $serie['Serie_Naam']) {
                return $this->series[$index]['Serie_Index'];
            }
        }
    }

    // set serie in database for the admin
    public function setSerie($data) { }

    // remove serie in database for the admin
    public function remSerie($data) { }

    /*  getAlbums($seriesId):
            This function gets all albums from a series, based on a serie name or index.

            $partId (String or Int)  - Can both take a serie name or index value, to get the associciated albums.

            Return Value: Multi-Dimensional Array.
     */
    public function getAlbums($partId) {
        if(!is_numeric($partId)) {
            $this->albums = App::get('database')->selectAllWhere('albums', [
                'Album_Serie' => $this->getSerId($partId)
            ]);

            return $this->albums;
        } else {
            $this->albums = App::get('database')->selectAllWhere('albums', [
                'Album_Serie' => $partId
            ]);

            return $this->albums;
        }
    }

    // public function getAlbInd($name) {
    //     $temp = App::get('database')->selectAllWhere('albums', ['Album_Naam' => $name])[0];

    //     return $temp['Album_Index'];
    // }

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
            if($value === $data["Alb_Index"]) {
                return FALSE;
            }
        }

        $tempCol = [                                                                // Then we prepare the data that needs to be added,
            "Gebr_Index" => $data["Gebr_Index"],
            "Alb_Index" => $data["Alb_Index"],
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
        $colIds = [
            "Gebr_Index" => $data["Gebr_Index"],
            "Alb_Index" => $data["Alb_Index"]
        ];

        App::get('database')->remove('collecties', $colIds);

        return TRUE;
    }
}

?>