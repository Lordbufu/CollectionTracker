<?php
namespace App\Core\Database;

use PDO;
use PDOException;

/* sprintf() reminder:
    The sprintf() function is very usefull to create querry string for the PDO class to execute/process.
    Reading the sprintf() code blocks, can be a bit confusing imo, so i opted to leave this reminder up here.

    Lets consider the following data being passed in:
        $table = "test";
        $id = [ "key1" => "value1" ];
        $id2 = [ "key1" => "value1", "key2" => "value2" ];

    We now have the following 3 example querries:
        select * from `table` where `key1` = `value1`
        select * from `table` where `key1` = `value1` and `key2` = `value2`
        insert into `table` (keys) values (values)

    First we have 1 key and value pair that need to go into a specific location.
    Then we have 2 key and value pairs, again into fixed locations.
    And as last, we have the pairs split up, but all the keys and values in the same location.

    Let cover the basics first, and see how we can simply search with a single key/value pair:
        $sql = sprintf(
            select * from `%s` where %s = %s,                   // notice the '%s' placeholders
            $table                                              // table name into the first placeholder
            implode(array_keys($id)),                           // key can go straight into the second placeholder
            ':'.implode(array_keys($id))                        // then we add ':' infront of the key for the last placeholder
        );
    
    The last placeholder, is basically another placeholder so the PDO knows where the value of said key has to go.
    The string stored in $sql, would look like the following now:
        select * from `test` where key1 = :key1
    
    Now to add to this example, and create the querry with a AND condition added, the diffence would be the following:
        select * from %s where %s = %s and %s = %s,             // Obviously the query is different
        implode(array_keys(array_slice($id, 0, 1))),            // Then i need to slice of the first pair to only get that key
        ':' . implode(array_keys(array_slice($id, 0, 1)))       // Same here but with the ':' infront again.

    And as you might have guessed, the second pair is done exactly the same, only shifting the slice up to only catch the second pair.
    The string stored in $sql, would look like the following now:
        select * from `test` where key1 = :key1 and key2 = :key2
    
    Things get easier if the keys and value are all in the same place, for the insert the changed lines would look like this:
        insert into %s (%s) values (%s),
        implode(', ', array_keys($id)),                         // Now we only need all keys seperated by a ','
        ':'.implode(', :', array_keys($id))                     // And now we can add a ':' before the implode, and to the ',' to make all value placeholders

    This should cover every situation i could think of.
 */
class QueryBuilder {
    // PDO object
    protected $pdo;

    // __construct(PDO $pdo): set this PDO to PDO class object.
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    //  testTable($name): Check if a table is present in the DB.
    //      $name (string)  - The name of a table i want to check.
    public function testTable($name) {
        // Should generate a error if the table is not there.
        $sql = sprintf("select 1 from %s LIMIT 1", $name);
        $statement = $this->pdo->prepare($sql);
        // Only here for production reasons, normally not required.
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $statement->execute();
        // return the error, so we can check if the table was there or not.
        return $statement->errorCode();
    }

    //  createTable($naam): To create the inital database tabels.
    //      $naam (string)  - The name of the Table i want to create.
    public function createTable($naam) {
        switch($naam) {
            case "gebruikers":
                $sql = sprintf(
                    "create table `%s` (
                        `Gebr_Index` INT NOT NULL AUTO_INCREMENT COMMENT 'Gebruikers index.',
                        `Gebr_Naam` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Naam v/d gebruiker.',
                        `Gebr_Email` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'E-mail voor de login.',
                        `Gebr_WachtW` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Wachtwood (hashed).',
                        `Gebr_Rechten` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'App Account rechten.',
                        PRIMARY KEY (`Gebr_Index`) COMMENT 'Primary index key voor gebruikers.',
                        UNIQUE (`Gebr_Email`) COMMENT 'Unique waarde voor e-mails, zodat er geen dubbele waardes zijn.'
                    ) CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT = 'Tafel voor de App Gebruikers.'",
                    $naam
                );

                return $this->executeQuerry($sql);
            case "series":
                $sql = sprintf(
                    "create table `%s` (
                        `Serie_Index` INT NOT NULL AUTO_INCREMENT COMMENT 'Serie index.',
                        `Serie_Naam` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Serie naam.',
                        `Serie_Maker` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Schrijver, Tekenaar etc',
                        `Serie_Opmerk` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Korte opmerking, niet account specifiek.',
                        PRIMARY KEY (`Serie_Index`) COMMENT 'Primary index key voor series.'
                    ) CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT = 'Tafel voor de series.'",
                    $naam
                );

                return $this->executeQuerry($sql);
            case "serie_meta":
                $sql = sprintf(
                    "create table `%s` (
                        `Meta_Index` int NOT NULL AUTO_INCREMENT COMMENT 'Unique index.',
                        `Serie_Index` int NOT NULL COMMENT 'Serie_Index link.',
                        `Gebr_Index` int NOT NULL COMMENT 'Gebr_Index link.',
                        `Serie_Opm` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Korte opmerking, account specifiek.',
                        UNIQUE KEY `Meta_Index` (`Meta_Index`),
                        KEY `SerieM_Gebr_Link` (`Gebr_Index`),
                        KEY `SerieM_Verz_Link` (`Serie_Index`),
                        CONSTRAINT `VerzM_Gebr_Link` FOREIGN KEY (`Gebr_Index`) REFERENCES `gebruikers` (`Gebr_Index`),
                        CONSTRAINT `SerieM_Serie_Link` FOREIGN KEY (`Serie_Index`) REFERENCES `series` (`Serie_Index`) ON DELETE RESTRICT ON UPDATE RESTRICT
                    ) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tafel voor collectie meta-data.'",
                    $naam
                );

                return $this->executeQuerry($sql);
            case "albums":
                $sql = sprintf(
                    "create table `%s` (
                        `Album_Index` int NOT NULL AUTO_INCREMENT COMMENT 'Unique index.',
                        `Album_Serie` int NOT NULL COMMENT 'Serie index link.',
                        `Album_Nummer` int COMMENT 'Uitgavenummer',
                        `Album_Naam` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Titel van het album.',
                        `Album_Cover` longblob COMMENT '(Optioneel) Cover plaatje (jpeg).',
                        `Album_UitgDatum` date COMMENT 'Datum van uitgave.',
                        `Album_ISBN` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Album ISBN nummer.',
                        `Album_Opm` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Evt opmerkingen over een specifiek album.',
                        UNIQUE KEY `Album_Index` (`Album_Index`),
                        KEY `Album_Serie_Link` (`Album_Serie`),
                        CONSTRAINT `Album_Serie_Link` FOREIGN KEY (`Album_Serie`) REFERENCES `series` (`Serie_Index`)
                    ) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tafel voor alle albums uit alle series.'",
                    $naam
                );

                return $this->executeQuerry($sql);
            case "collecties":
                $sql = sprintf(
                    "create table `%s` (
                        `Col_Index` int NOT NULL AUTO_INCREMENT COMMENT 'Collectie index.',
                        `Gebr_Index` int NOT NULL COMMENT 'Account index link.',
                        `Alb_Index` int NOT NULL COMMENT 'Album index link.',
                        `Alb_Staat` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'De staat van een specifiek album of uitgave',
                        `Alb_DatumVerkr` date DEFAULT NULL COMMENT 'De datum dat het album is verkregen',
                        `Alb_Aantal` int DEFAULT NULL COMMENT 'Aantal albums in het bezit v/d gebruiker.',
                        `Alb_Opmerk` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Opmerking over een album voor de gebruiker zelf.',
                        UNIQUE KEY `Col_Index` (`Col_Index`),
                        KEY `Coll_Gebr_Link` (`Gebr_Index`),
                        KEY `Coll_Alb_Link` (`Alb_Index`),
                        CONSTRAINT `Coll_Alb_Link` FOREIGN KEY (`Alb_Index`) REFERENCES `albums` (`Album_Index`),
                        CONSTRAINT `Coll_Gebr_Link` FOREIGN KEY (`Gebr_Index`) REFERENCES `gebruikers` (`Gebr_Index`)
                    ) CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Alle link data voor series en albums voor Accounts.'",
                    $naam
                );

                return $this->executeQuerry($sql);
        }
    }

    // createAdmin(): To create the default administrator account.
    public function createAdmin() {
        $wwHashed = password_hash('wachtwoord123', PASSWORD_BCRYPT);
        $sql = "insert into `gebruikers` (`Gebr_Naam`, `Gebr_Email`, `Gebr_WachtW`, `Gebr_Rechten`) values ('Administrator','admin@colltrack.nl','{$wwHashed}','Admin')";

        return $this->executeQuerry($sql);
    }

    //  countAblums($id): Count albums from a specific serie.
    //      $id (int)       - The index from the serie we want to count its albums.
    //
    //      Return Value (int)
    public function countAlbums($id) {
        $sql = sprintf("select count(*) from `albums` where `Album_Serie`=%s", $id);
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC)[0]['count(*)'];
    }

    /*  executeQuerry($sql, $id = []): Seperate execute function, to reuse the same code.
            $sql (string)       - The querry string with placeholders that has been prepared.
            $id (Assoc Array)   - The identifiers required to select/update/remove specific data
    
            Return Value:
                On success  (Assoc Array)   - The data that was requested.
                On fail     (String)        - The database error in full detail.
     */
    public function executeQuerry($sql, $id = []) {
        // If the '$id' is empty, i dont need to worry about placeholders for identifying data.
        if(empty($id)) {
            try {
                $statement = $this->pdo->prepare($sql);
                $statement->execute();
                return $statement->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                return "Error: ". $e->getMessage();
            }
        // In all other cases, i need to use '$id', to get the data casted into the right placeholders.
        } else {
            try {
                $statement = $this->pdo->prepare($sql);
                $statement->execute($id);
                return $statement->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                //die('test 2');
                //die(var_dump(print_r("Error: " . $e->getMessage())));
                return "Error: " . $e->getMessage();
            }
        }
    }

    //  selectAll($tafel): Select all doesnt need anything to complex.
    //      $tafel (string) - The table name i want to get all data from.
    public function selectAll($tafel) {
        $sql = sprintf('select * from `%s`', $tafel);
        $temp = $this->executeQuerry($sql);
        return $temp;
    }

    //  selectAllWhere($tafel, $id): Zoek alles op basis van een identifier, zoals bv een index, of een index + naam.
    //      $tafel (string)     - The table name i want to get specific data from.
    //      $id (Assoc Array)   - The identifiers required to select specific data.
    public function selectAllWhere($tafel, $id) {
        // I check if there are multiple identifier.
        if(count($id) > 1) {
            // I create the querry string using sprintf (details at the top)
            $sql = sprintf(
                'select * from `%s` where %s = %s and %s = %s',
                $tafel,
                implode( array_keys( array_slice( $id, 0, 1 ) ) ),
                ':' . implode( array_keys( array_slice( $id, 0, 1 ) ) ),
                implode( array_keys( array_slice( $id, 1, 2 ) ) ),
                ':' . implode( array_keys( array_slice( $id, 1, 2 ) ) )
            );
        // If there is only a single identifier.
        } else {
            // I create the querry string using sprintf (details at the top)
            $sql = sprintf(
                'select * from `%s` where %s = %s',
                $tafel,
                implode( array_keys($id)),
                ':' . implode( array_keys($id))
            );
        }
        
        return $this->executeQuerry($sql, $id);
    }

    //  insert($tafel, $data): Simple insert querry.
    //      $tafel (string)     - The table name i want to add specific data to.
    //      $data (Assoc Array) - The data i want to add, where $key is the colum name that i want to set data in.
    public function insert($tafel, $data) {
        // I create the querry string using sprintf (details at the top)
        $sql = sprintf(
            'insert into `%s` (%s) values (%s)',
            $tafel,
            implode(', ', array_keys($data)),
            ':' . implode(', :', array_keys($data))
        );

        return $this->executeQuerry($sql, $data);
    }

    //  remove($tafel, $cond): Simple remove querry.
    //      $tafel (string)     - The table name i want to remove specific data from.
    //      $cond (Assoc Array) - The conditions (identifier) of the data i want to remove.
    public function remove($tafel, $cond) {
        // I check if there are multiple identifier.
        if(count($cond) > 1) {
            // I create the querry string using sprintf (details at the top)
            $sql = sprintf(
                'delete from %s where %s = %s and %s = %s',
                $tafel,
                implode( array_keys( array_slice( $cond, 0, 1 ) ) ),
                ':' . implode( array_keys( array_slice( $cond, 0, 1 ) ) ),
                implode( array_keys( array_slice( $cond, 1, 2 ) ) ),
                ':' . implode( array_keys( array_slice( $cond, 1, 2 ) ) )
            );
        // If there is only a single identifier.
        } else {
            // I create the querry string using sprintf (details at the top)
            $sql = sprintf(
                'delete from %s where %s = %s',
                $tafel,
                implode( array_keys($cond)),
                ':' . implode( array_keys($cond))
            );
        }

        return $this->executeQuerry($sql, $cond);
    }

    //  update($tafel, $data, $id): Simple update querry, using a loop to process $data instead of sprintf().
    //      $tafel (string)     - The table name i want to update specific data in.
    //      $data (Assoc Array) - The data i want to update, paired by column name (key) and the value it should have.
    //      $id (Assoc Array)   - The identifiers to find the data i want to update.
    public function update($tafel, $data, $id) { 
        // Variable to re-format the data array, into something more easily worked with.
        $update;

        // First i need to format the $data into $update
        foreach($data as $key => $value) {
            // If there it nothing in $update,
            if(!isset($update)) {
                // i add the first key + value.
                $update = $key . '=' . "'" . $value . "'";
            // If there is something there already, i add the current info first, then the next $key + $value.
            } elseif(isset($update)) { $update = $update . ', ' . $key . ' = ' . "'" . $value . "'"; }
        }

        if(count($id) > 1) {
            // I create the querry string using sprintf (details at the top)
            $sql = sprintf(
                'update %s set %s where %s = %s and %s = %s',
                $tafel,
                $update,
                implode( array_keys( array_slice($id, 0, 1 ) ) ),
                ':' . implode( array_keys( array_slice($id, 0, 1 ) ) ),
                implode( array_keys( array_slice($id, 1, 2 ) ) ),
                ':' . implode( array_keys( array_slice($id, 1, 2 ) ) )
            );
        } else {
            $sql = sprintf(
                'update %s set %s where %s = %s',
                $tafel,
                $update,
                implode( array_keys( array_slice($id, 0, 1 ) ) ),
                ':' . implode( array_keys( array_slice($id, 0, 1 ) ) )
            );
        }

        return $this->executeQuerry($sql, $id);
    }
}
?>