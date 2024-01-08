<?php
namespace App\Core\Database;

use PDO;
use PDOException;

// Create the database connection, using (loaded) the $config file.
class Connection {
    public static function make($config) {
        try {
            return new PDO(
                $config['connection'].';dbname='.$config['name'],
                $config['username'],
                $config['password'],
                $config['options']
            );
        // very basic exception catch
        } catch (PDOException $e) { die($e->getMessage()); }
    }
}

?>