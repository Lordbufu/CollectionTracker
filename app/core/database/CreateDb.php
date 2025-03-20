<?php

namespace App\Core\Database;

class CreateDb {
    // SQL code for the 'gebruiker' table.
    protected static function gebruikersCreate() {
        return $sql = "CREATE TABLE `gebruikers` (
            `Gebr_Index` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Gebruikers index.',
            `Gebr_Naam` varchar(255) NOT NULL COMMENT 'Naam v/d gebruiker.',
            `Gebr_Email` varchar(255) NOT NULL COMMENT 'E-mail voor de login.',
            `Gebr_WachtW` text NOT NULL COMMENT 'Wachtwood (hashed).',
            `Gebr_Rechten` tinytext NOT NULL COMMENT 'App Account rechten.',
            PRIMARY KEY (`Gebr_Index`) COMMENT 'Primary index key voor gebruikers.',
            UNIQUE KEY `Gebr_Email` (`Gebr_Email`) COMMENT 'Unique waarde voor e-mails, zodat er geen dubbele e-mails zijn.',
            UNIQUE KEY `Gebr_Naam` (`Gebr_Naam`) USING HASH COMMENT 'Unique waarde voor namen, zodat er geen dubbele namen zijn.'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tafel voor de App Gebruikers.'";
    }

    // SQL code for the 'reeks' table.
    protected static function reeksCreate() {
        return $sql = "CREATE TABLE `reeks` (
            `Reeks_Index` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Reeks index.',
            `Reeks_Naam` tinytext NOT NULL COMMENT 'Reeks naam.',
            `Reeks_Maker` tinytext DEFAULT NULL COMMENT 'Uitgever, Tekenaar etc',
            `Reeks_Opmerk` tinytext DEFAULT NULL COMMENT 'Korte opmerking, niet account specifiek.',
            `Reeks_Cover` blob DEFAULT NULL COMMENT 'Cover plaatje van de hele reeks.',
            PRIMARY KEY (`Reeks_Index`) COMMENT 'Primary index key voor Reeksen.'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tafel voor de Reeks.'";
    }

    // SQL code for the 'items' table.
    protected static function itemsCreate() {
        return $sql = "CREATE TABLE `items` (
            `Item_Index` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique index voor een item.',
            `Item_Reeks` int(11) NOT NULL COMMENT 'Reeks index link, om een item te koppelen aan een reeks.',
            `Item_Nummer` int(11) DEFAULT NULL COMMENT 'Uitgavenummer indien beschikbaar.',
            `Item_Auth` tinytext DEFAULT NULL COMMENT 'Autheur/Schrijver/Maker van een item.',
            `Item_Naam` tinytext NOT NULL COMMENT 'Titel/Naam van het item.',
            `Item_Plaatje` longblob DEFAULT NULL COMMENT '(Optioneel) Plaatje van de omslag, cover, doos etc.',
            `Item_Uitgd` date DEFAULT NULL COMMENT 'Datum van uitgave.',
            `Item_Isbn` tinytext NOT NULL COMMENT 'Item ISBN/EAN nummer.',
            `Item_Opm` tinytext DEFAULT NULL COMMENT 'Evt opmerkingen over een specifiek item.',
            UNIQUE KEY `Item_Index` (`Item_Index`) COMMENT 'Index waarde for een item.',
            KEY `Item_Reeks_Key` (`Item_Reeks`) COMMENT 'De link tussen items en reeks.',
            CONSTRAINT `Item_Reeks_Const` FOREIGN KEY (`Item_Reeks`) REFERENCES `reeks` (`Reeks_Index`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tafel voor alle items uit alle reeksen.'";
    }

    // SQL code for the 'collecties' table.
    protected static function collectieCreate() {
        return $sql = "CREATE TABLE `collectie` (
            `Col_Index` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Collectie index.',
            `Gebr_Index` int(11) NOT NULL COMMENT 'Account index link.',
            `Item_Index` int(11) NOT NULL COMMENT 'Item index link.',
            `Reeks_Index` int(11) NOT NULL COMMENT 'Link to the Reeks table, for db actions.',
            UNIQUE KEY `Col_Index` (`Col_Index`),
            KEY `Coll_Gebr_Key` (`Gebr_Index`),
            KEY `Coll_Item_Key` (`Item_Index`),
            KEY `Coll_Reeks_Cons` (`Reeks_Index`),
            CONSTRAINT `Coll_Gebr_Cons` FOREIGN KEY (`Gebr_Index`) REFERENCES `gebruikers` (`Gebr_Index`),
            CONSTRAINT `Coll_Item_Cons` FOREIGN KEY (`Item_Index`) REFERENCES `items` (`Item_Index`),
            CONSTRAINT `Coll_Reeks_Cons` FOREIGN KEY (`Reeks_Index`) REFERENCES `reeks` (`Reeks_Index`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Alle link data voor series en albums voor Accounts.'";
    }

    // SQL code for the default 'admin' gebruiker.
    protected static function adminCreate() {
        $wwHashed = password_hash('wachtwoord123', PASSWORD_BCRYPT);
        $sql = "INSERT INTO `gebruikers` (`Gebr_Naam`, `Gebr_Email`, `Gebr_WachtW`, `Gebr_Rechten`) VALUES ('Administrator','admin@colltrack.nl','{$wwHashed}','Admin')";

        return $sql;
    }

    // - CREATE->functie (maken van de default database en default admin)
    public static function create($name) {
        $functionName = $name . 'Create';

        return self::$functionName();
    }
}