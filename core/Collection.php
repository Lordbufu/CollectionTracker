<?php
namespace App\Core;

use App\Core\App;

//  TODO: Go over all code, and adjust for database error returned from the collection class, since querry execution now returns the DB error as a string.
class Collection {
    // Protected/Internal functions and variables.
    protected $albums;
    protected $series;
    protected $collections;

    /*  getSetId($name):
            Get serie index based on serie name.

            $name (String)  : The name of the series we want the index for.

            Return Value    : INT
     */
    protected function getSerId($name) {
        $tempSerie = App::get('database')->selectAllWhere('series', [
            'Serie_Naam' => $name
        ])[0];

        return $tempSerie['Serie_Index'];
    }

    /*  countAlbums():
            We use the database to count the number of albums in a series, and store it in the global.
            So it can be displayed, each time getSeries is called, the number is updated.
     */
    protected function countAlbums() {
        foreach($this->series as $key => $value) {
            $this->series[$key]['Album_Aantal'] = App::get('database')->countAlbums($value['Serie_Index']);
        }

        return;
    }

    // W.I.P.
    /*  checkDupl($type, $data = []):
            This function is desgined to check in all items, if there is a duplicated entry in the database.

            $type (String)          : The type of store i want to check, albums/series/collections.
            $data (Assoc Array)     : The data required for the check, this could vary depending on the store, but is usually the name.

            Return value            : Boolean.
     */
    protected function checkDupl($type, $data=[], $sIndex=null) {
        if($type == 'albums') {
            foreach($this->albums as $index => $album) {
                if($data['name'] == $album['Album_Naam']) {
                    return TRUE;
                }
            }

            return FALSE;
        } elseif($type == 'series') {
            foreach($this->series as $index => $serie) {
                if($data['name'] == $serie['Serie_Naam']) {
                    if($index === null) {
                        return TRUE;
                    } else {
                        if($sIndex != $serie['Serie_Index']) {
                            return TRUE;
                        }
                    }
                }
            }
    
            return FALSE;
        } elseif($type == 'collections') {
            // W.I.P.
        }
    }

    // Public get/set functions.
    /*  getSeries():
            Simple get all series from DB, add a album count to each serie, and return them all to the caller.

            Return Value: Multi-Dimensional Array.
     */
    public function getSeries() {
        $this->series = App::get('database')->selectAll('series');
        $this->countAlbums();

        return $this->series;
    }

    /*  getSerName($ind):
            This function takes the serie index number, and finds the matching serie naam.

            $ind (INT)      : The index of the serie.

            Return Value    : String
     */
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

    /*  getSerInd($name):
            This function takes a serie name, and finds the matching serie index.

            $name (String)  : The name of the serie.

            Return Value    : INT
     */
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

    /*  cheSerName($name):
            This function checks if the name/entry, is duplicate or not.

            $name (String)  : The name of the serie.

            Return Value    : Boolean
     */
    public function cheSerName($name, $index=null) {
        if(!isset($this->series)) {
            $this->getSeries();
        }

        $check = $this->checkDupl('series', ['name' => $name], $index);

        return $check;
    }

    /*  setSerie($data):
            This functions set the a serie in the database, all filtering etc is done in the controller.

            $data (Assoc Array) : The data that needs to be stored.

            Return Type:
                On sucess   -> Boolean
                On fail     -> String (the database error)
     */
    public function setSerie($data, $update=null) {
        if($update === null) {
            $store = App::get('database')->insert('series', $data);
        } else {
            $store = App::get('database')->update('series', $data, [ 'Serie_Index' => $update ] );
        }

        return is_string($store) ? $store : TRUE;
    }

    // Need to rename this to something more fitting for its universal use, current used for series and albums.
    // remove serie in database for the admin
    public function remSerie($table, $id) {
        $store = App::get('database')->remove($table, $id);

        return is_string($store) ? $store : TRUE;
    }

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

    /*  getAlbId($name):
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

    /*  cheAlbName($serInd, $name):
            Checks if the requested album name using the global function, and then check if that album was in the same serie.
            If it was in the same serie, it was likely being edited, and thus not duplicate.

                $setInd (INT)           - The index of the serie that contains the album.
                $name (String)          - The album name that needs to be checked.
                $duplicate (Boolean)    - Local variable to return the final eval of the duplication check.
                $check (Boolean)        - The result of the checkDupl() function.

            Return Type : Boolean.
     */
    public function cheAlbName($serInd, $name) {
        $duplicate;

        if(!isset($this->albums)) {
            $this->getAlbums($serInd);
        }

        $check = $this->checkDupl( 'albums', [ 'name' => $name ] );

        if($check) {
            foreach($this->albums as $index => $album) {
                if($album['Album_Naam'] === $name) {
                    $duplicate = FALSE;
                }
            }

            if(!isset($duplicate)) { $duplicate = TRUE; }
        }

        return $duplicate;
    }

    // It's likely best to combine all get name function, i will look into that at a later stage.
    /*  getAlbumName($albId, $serId):
            This functions sets the $albums to what ever series is being worked on, and compares the indexes to return the correct name.
            In all cases i tested, its already set to load the admin-album-view, but to be sure i did include the isset condition.

                $albId (string) - The album-index we want to get the name of.
                $serId (string) - The serie-index that the album is a part of.
            
            Return Value: String.
     */
    public function getAlbumName($albId, $serId) {
        if(!isset($this->albums)) {
            $this->getAlbums($serId);
        }

        foreach($this->albums as $index => $album) {
            if($albId == $album['Album_Index']) {
                return $album['Album_Naam'];
            }
        }
    }

    // W.I.P.
    // Adjust comments for the added update functionality.
    /*  setAlbum($data):
            This function simply sets the Album in the database, since all relevant checks have been done in advance.

            $data (Assoc Array) : The Album data that needs to be stored.

            Return types:
                On-success  : Boolean
                On-failure  : String
     */
    public function setAlbum($data, $update=null) {
        if($update === null) {
            $store = App::get('database')->insert('albums', $data);
        } else {
            $store = App::get('database')->update('albums', $data, $update);
        }

        return is_string($store) ? $store : TRUE;
    }

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

        $temp = App::get('database')->remove('collecties', $colIds);

        return is_string($temp) ? $temp : TRUE;
    }
}

?>