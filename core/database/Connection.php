<?php
/* TODO List:
        - Edit/clean-up comments, to much clutter atm left over from the concept/design stage.
 */

namespace App\Core\Database;

use PDO;
use PDOException;

class Connection {
    public static function make($config) {
        try {
            return new PDO(
                $config['connection'].';dbname='.$config['name'],
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}

?>