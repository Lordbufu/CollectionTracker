<?php

namespace App\Core\Database;

class QueryBuilder {
    /*  select($table, $data=null):
            Your default select querry, that can also refine the search based on the provided data (id pairs).
                $table (String)     - The table name in string format.
                $data (Assoc Arr)   - Associative data to refine the search (id pairs like index or name).
                $sql (String)       - The query prepared in string format, with placeholders.
            
            Return Value:
                String -> The query prepared for the PDO.
     */
    public function select($table, $data=null) {
        /* Check if the search needs to be refined. */
        if(!empty($data)) {
            /* Check if its not a a single ID pair. */
            if(count($data) > 1) {
                $sql = sprintf(
                    'SELECT * FROM `%s` WHERE %s = %s AND %s = %s',
                    $table,
                    implode(array_keys(array_slice($data, 0, 1))),
                    ':' . implode(array_keys(array_slice($data, 0, 1))),
                    implode(array_keys(array_slice($data, 1, 2))),
                    ':' . implode(array_keys(array_slice($data, 1, 2)))
                );
            /* Check if it is a single ID pair. */
            } elseif(count($data) === 1) {
                $sql = sprintf(
                    'SELECT * FROM `%s` WHERE %s = %s',
                    $table,
                    implode(array_keys($data)),
                    ':' . implode(array_keys($data))
                );
            }
        } else {
            $sql = sprintf('select * from `%s`', $table);
        }

        return $sql;
    }

    /*  insert($table, $data):
            A simple insert query, that should be able to handle all insert request.
                $table (String)     - The table name that should recieve the data.
                $data (Assoc Arr)   - The data that should be inserted into said table.
                $sql (String)       - The query prepared in string format, with placeholders.

            Return Value:
                String -> The query prepared for the PDO.
     */
    public function insert($table, $data) {
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', array_keys($data)),
            ':' . implode(', :', array_keys($data))
        );

        return $sql;
    }

    /*  update($table, $data, $ids):
            A somewhat more complex update function, that does some odd magic to create the data part of the string.
            Appending the id pairs, is done the same as in all other query functions.
                $table (String)     - The table name that should recieve the data.
                $data (Assoc Arr)   - The data that should be updated into said table.
                $ids (Assoc Arr)    - Id Pairs to define what record should be updated.
                $update (String)    - The SET part of the query string, concatinated using a simple foreach loop.
                $sql (String)       - The query prepared in string format, with placeholders.

            Return Value:
                String -> The query prepared for the PDO.
     */
    public function update($table, $data, $ids) {
        $update;

        /* Concatinate the data part of the querry (SET). */
        foreach($data as $key => $value) {
            if(!isset($update)) {
                $update = sprintf(
                    "%s='%s'",
                    $key,
                    $value
                );
            } else {
                $update = $update . sprintf(
                    ", %s='%s'",
                    $key,
                    $value
                );
            }
        }

        /* Check ho many id pairs where provided, and adjust the query construction accordingly. */
        if(count($ids) > 1 && count($ids) !== 3) {
            $sql = sprintf(
                'UPDATE %s SET %s WHERE %s = %s AND %s = %s',
                $table,
                $update,
                implode(array_keys(array_slice($ids, 0, 1))),
                ':' . implode(array_keys(array_slice($ids, 0, 1))),
                implode(array_keys(array_slice($ids, 1, 2))),
                ':' . implode(array_keys(array_slice($ids, 1, 2)))
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET %s WHERE %s = %s',
                $table,
                $update,
                implode(array_keys(array_slice($ids, 0, 1))),
                ':' . implode(array_keys(array_slice($ids, 0, 1)))
            );
        }

        //dd($sql);

        return $sql;
    }

    /*  delete($table, $ids):
            This function prepares a simple delete query, to remove specific data from specific tables.
                $table (String)     - The table name that should have data removed.
                $ids (Assoc Arr)    - Id Pairs to define what record should be removed.
                $sql (String)       - The query prepared in string format, with placeholders.

            Return Value:
                String -> The query prepared for the PDO.
     */
    public function delete($table, $ids) {
        /* Check if how many id pairs are provided, adjusting the query construction as needed. */
        if(count($ids) > 1 ) {
            $sql = sprintf(
                'DELETE FROM %s WHERE %s = %s AND %s = %s',
                $table,
                implode(array_keys(array_slice($ids, 0, 1))),
                ':' . implode(array_keys(array_slice($ids, 0, 1))),
                implode(array_keys(array_slice($ids, 1, 2))),
                ':' . implode(array_keys(array_slice($ids, 1, 2)))
            );
        } else {
            $sql = sprintf(
                'DELETE FROM %s WHERE %s = %s',
                $table,
                implode(array_keys($ids)),
                ':' . implode(array_keys($ids))
            );
        }

        return $sql;
    }

    /*  count(ids):
            A simple count query, that counts how many items are associated with a specific Reeks.
                $ids (Assoc Arr)    - Id Pairs to define what record should be removed.
                $sql (String)       - The query prepared in string format, with placeholders.

            Return Value:
                String -> The query prepared for the PDO.
     */
    public function count($ids) {
        $sql = sprintf("SELECT count(*) FROM `items` WHERE `Item_Reeks` = %s",
            ':' . implode(array_keys(array_slice($ids, 0 , 1)))
        );

        return $sql;
    }

    /*  testTable($name):
            This function checks if a table has been made or not, so the tables can be created in a empty database.
                $name (String)  - The table name in string format.
                $sql (String)   - The query prepared in string format, with placeholders.

            Return Value:
                String -> The query prepared for the PDO.
     */
    public function testTable($name) {
        $sql = sprintf( "SELECT 1 FROM %s LIMIT 1", $name );
        
        return $sql;
    }
}