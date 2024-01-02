<?php
/* TODO List:
        - Edit/clean-up comments, to much clutter atm left over from the concept/design stage.
 */

namespace App\Core\Database;

use PDO;

/* QuerryBuilder Class:
    Deze class zijn functie, is het maken en uitvoeren van SQL querries, via het PDO object van PhP.

    Variable:
        $pdo - Beschermt voor het PDO object.

    Functies:
        __construct(PDO $pdo)       - Constructor het PDO object te laden.
        createTable($naam)          - Functie database tafels te maken, die de App nodig heeft om te werken.
        createAdmin()               - Functie om het ingebouwde admin account te maken.
        executeQuerry($naam, $id)   - Functie om querries uit te voeren, zodat ik niet overal de zelfde try/catch loop moet herschrijven.
        selectAll($tafel)           - Functie om alles uit een $tafel te selecteren.
        selectAllWhere($tafel, $id) - Functie om iets op basis van een $id uit een $tafel te selecteren.
        insert($tafel, $data)       - Functie om $data in een $tafel te zetten.
        remove($tafel, $cond)       - Functie om data met deze $cond(ities) uit een $tafel te verwijderen.
        update($table, $params, $id)- Functie om iets met deze $id(entifier(s)) uit een $table, te updaten met deze $data.
    
    sprintf() uitleg:
        sprintf werkt vrij eenvoudig, als ik de volgende querry nodig heb:
            select * from `tafel-naam`

        Kan ik met deze functie de `tafel-naam` via een placeholder op zijn plek zetten, en krijg je deze string:
            select * from %s

        Om de tafel naam uit een string variable te halen, doe ik dan het volgende:
            $sql = sprintf("select * from %s", $tafel);
        
        Dit kan je dan ook voor met complexe querries gebruiken, zolang de variable op de juiste manier voorbewerkt zijn.
        Stel dat we de volgende informatie hebben:
            $tafel = 'gebruikers';
            $id = [ 'gebruikers-index' => 1, 'gebruikers-email' => 'test@test.nl' ];
        
        Als ik dan de gebruiker met die exacte $id gegevens moet hebben, zet ik die als volgt klaar:
            $sql = sprintf(
                'select * from `%s` where %s = %s and %s = %s',                 // De querry zoals ik die hebben wil
                $tafel,                                                         // De tafel naam voor de eerste placeholder
                implode( ', ', array_keys( array_slice( $id, 0, 1 ) ) ),        // De key van array index 0 als kolum naam bv gebruikers-index
                ':' . implode( ', :', array_keys( array_slice( $id, 0, 1 ) ) ), // De key van array index 0 als placeholder bv :gebruiker-index
                implode( ', ', array_keys( array_slice( $id, 1, 2 ) ) ),        // De key van array index 1 als kolum naam bv gebruikers-email
                ':' . implode( ', :', array_keys( array_slice( $id, 1, 2 ) ) )  // De key van array index 1 als placeholder bv :gebruiker-email
            );
        
        Als de $id maar 1 array pair heeft bv ['gebruikers-index' => 1], is array_slice niet nodig.
        Ook bij een insert, ondanks dat er meerdere data pairs zijn, is dit niet nodig.

        De querry ($sql) die dan terug komt van sprintf ziet er als volg uit:
            select * from `gebruikers` where Gebr_Email = :Gebr_Email and Gebr_Index = :Gebr_Index

        En als ik dat dan via de PDO wil uitvoeren, hoef ik alleen $id mee te geven en zet de PDO de juiste waarde op de juiste plek.
        Die regels zien er dan als volgt uit:
            $statement = $this->pdo->prepare($sql);
            $statement->execute($id);
 */
class QueryBuilder {
    protected $pdo;

    // __construct(PDO $pdo): om de PDO te maken in het globale protected variable.
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // createTable($naam): Voor het maken van de database tafel die de App nodig heeft, die alleen de tafel naam verwacht.
    public function createTable($naam) {
        switch($naam) {
            // De query die nodig is voor de gebruikers tafel.
            case "gebruikers":
                $sql = sprintf(
                    "create table if not exists `%s` (
                        `Gebr_Index` INT NOT NULL AUTO_INCREMENT COMMENT 'Gebruikers index.',
                        `Gebr_Naam` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Naam v/d gebruiker.',
                        `Gebr_Email` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'E-mail (hashed) voor de login.',
                        `Gebr_WachtW` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Wachtwood (hashed).',
                        `Gebr_Rechten` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'App Account rechten.',
                        PRIMARY KEY (`Gebr_Index`) COMMENT 'Primary index key voor gebruikers.',
                        UNIQUE (`Gebr_Email`) COMMENT 'Unique waarde voor e-mails, zodat er geen dubbele waardes zijn.'
                    ) CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT = 'Tafel voor de App Gebruikers.'",
                    $naam
                );

                $this->executeQuerry($sql);
            // De query die nodig is voor de series tafel.
            case "series":
                $sql = sprintf(
                    "create table if not exists `%s` (
                        `Serie_Index` INT NOT NULL AUTO_INCREMENT COMMENT 'Serie index.',
                        `Serie_Naam` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Serie naam.',
                        `Serie_Maker` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Schrijver, Tekenaar etc',
                        `Serie_Opmerk` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Korte opmerking, niet account specifiek.',
                        PRIMARY KEY (`Serie_Index`) COMMENT 'Primary index key voor series.'
                    ) CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT = 'Tafel voor de series.'",
                    $naam
                );

                $this->executeQuerry($sql);
            // De query die nodig is voor de series-meta tafel.
            case "serie_meta":
                $sql = sprintf(
                    "create table if not exists `%s` (
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

                $this->executeQuerry($sql);
            // De query die nodig is voor de albums tafel.
            case "albums":
                $sql = sprintf(
                    "create table if not exists `%s` (
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

                $this->executeQuerry($sql);
            // De query die nodig is voor de collecties tafel.
            case "collecties":
                $sql = sprintf(
                    "create table if not exists `%s` (
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

                $this->executeQuerry($sql);
        }
    }

    // createAdmin(): Voor het maken van het standaard admin account.
    public function createAdmin() {
        $wwHashed = password_hash('wachtwoord123', PASSWORD_BCRYPT);
        $sql = "insert into `gebruikers` (`Gebr_Naam`, `Gebr_Email`, `Gebr_WachtW`, `Gebr_Rechten`) values ('Administrator','admin@colltrack.nl','{$wwHashed}','Admin') ON DUPLICATE KEY UPDATE `Gebr_Email`=`Gebr_Email`";

        $this->executeQuerry($sql);
    }

    // executeQuerry($sql, $id = []): Voor het uitvoeren van alle queries.
    public function executeQuerry($sql, $id = []) {
        // Als de id leeg is, moet ik de query uitvoeren zonder die variable.
        if(empty($id)) {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        // Als de id niet leeg is, moet die mee via de execute() voor de placeholders.
        } else {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($id);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    // selectAll($tafel): Om alles uit een specifieke tafel te halen, en terug te geven aan de caller.
    public function selectAll($tafel) {
        $sql = sprintf('select * from `%s`', $tafel);
        $temp = $this->executeQuerry($sql);
        return $temp;
    }

    // selectAllWhere($tafel, $id): Zoek alles op basis van een identifier, zoals bv een index, of een index + naam.
    public function selectAllWhere($tafel, $id) {
        // Voor als er meer dan 1 identifier is.
        if(count($id) > 1) {
            $sql = sprintf(
                'select * from `%s` where %s = %s and %s = %s',
                $tafel,
                implode( ', ', array_keys( array_slice( $id, 0, 1 ) ) ),
                ':' . implode( ', :', array_keys( array_slice( $id, 0, 1 ) ) ),
                implode( ', ', array_keys( array_slice( $id, 1, 2 ) ) ),
                ':' . implode( ', :', array_keys( array_slice( $id, 1, 2 ) ) )
            );
        // Als er maar 1 identifier is.
        } else {
            $sql = sprintf(
                'select * from `%s` where %s = %s',
                $tafel,
                implode(', ', array_keys($id)),
                ':' . implode(', :', array_keys($id))
            );
        }

        return $this->executeQuerry($sql, $id);                                                 // Dan voer ik het uit en geef ik de uitslag van 'executeQuerry()' terug.
    }

    // insert($tafel, $data): Voor het toevoegen van data aan de database.
    public function insert($tafel, $data) {
        $sql = sprintf(
            'insert into `%s` (%s) values (%s)',
            $tafel,
            implode(', ', array_keys($data)),
            ':' . implode(', :', array_keys($data))
        );

        $this->executeQuerry($sql, $data);
    }

    // remove($tafel, $cond): Voor het verwijderen van data uit de database, op basis van bepaalde condities (identifiers).
    public function remove($tafel, $cond) {
        // Als er meer dan 1 conditie is.
        if(count($cond) > 1) {
            $sql = sprintf(
                'delete from %s where %s = %s and %s = %s',
                $tafel,
                implode( ', ', array_keys( array_slice( $cond, 0, 1 ) ) ),
                ':' . implode( ', :', array_keys( array_slice( $cond, 0, 1 ) ) ),
                implode( ', ', array_keys( array_slice( $cond, 1, 2 ) ) ),
                ':' . implode( ', :', array_keys( array_slice( $cond, 1, 2 ) ) )
            );
        // Als er maar 1 conditie is.
        } else {
            $sql = sprintf(
                'delete from %s where %s = %s',
                $tafel,
                implode(', ', array_keys($cond)),
                ':' . implode(', :', array_keys($cond))
            );
        }

        $this->executeQuerry($sql, $cond);
    }

    // update($tafel, $data, $id): Vooer het updaten van objecten in de database.
    public function update($tafel, $data, $id) { 
        // Variable voor het formateren van de $data.
        $update;

        // Loop over de data array, voor maken van de juiste string voor sprintf().
        foreach($data as $key => $value) {
            if(!isset($update)) {
                $update = $key . '=' . "'" . $value . "'";
            } elseif(isset($update)) {
                $update = $update . ', ' . $key . ' = ' . "'" . $value . "'";
            }
        }

        // Het maken van de querry string met placeholders.
        $sql = sprintf(
            'update %s set %s where %s = %s',
            $tafel,
            $update,
            implode(', ', array_keys(array_slice($id, 0, 1 ))),
            ':' . implode(', :', array_keys(array_slice($id, 0, 1 )))
        );

        $this->executeQuerry($sql, $id);
    }
}

?>