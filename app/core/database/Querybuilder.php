<?php

/*  TODO List:
        - Clean up comments on the side, and move the useable parts to the top of the functions.
 */

namespace App\Core\Database;

/*  QueryBuilder:
        Here i store all default queries, that are used to store/request/update/remove and test my database content.
 */
class QueryBuilder {
    public function select($table, $data=null) {                                        // Default select query with 2 options based on the value of $data,
        if(!empty($data)) {                                                             // if there is data,
            if(count($data) > 1) {                                                      // and there are more then 1 or data pairs,
                $sql = sprintf(                                                         // i populate $sql with sprintf,
                    'SELECT * FROM `%s` WHERE %s = %s AND %s = %s',                     // and need a select * from .. where .. = .. and .. = .. query,
                    $table,                                                             // with the table name at the first placeholder,
                    implode(array_keys(array_slice($data, 0, 1))),                      // the first $data pair key for the second,
                    ':' . implode(array_keys(array_slice($data, 0, 1))),                // a placeholder for the first $data pair value on the third,
                    implode(array_keys(array_slice($data, 1, 2))),                      // the second $data pair key on the fourth,
                    ':' . implode(array_keys(array_slice($data, 1, 2)))                 // and finally a placeholder again for the second $data pair value on the fifth.
                );
            } elseif(count($data) === 1) {                                              // If there is only 1 data pair,
                $sql = sprintf(                                                         // i populate $sql with sprintf,
                    'SELECT * FROM `%s` WHERE %s = %s',                                 // and need a select * from .. where .. = .. query,
                    $table,                                                             // with the table name at the first placeholder,
                    implode(array_keys($data)),                                         // the $data pair key for the second,
                    ':' . implode(array_keys($data))                                    // and finally the $data pair value as the third.
                );
            }
        } else {                                                                        // If there is no data at all,
            $sql = sprintf('select * from `%s`', $table);                               // i simply make a select * from .. query with the table name as first placeholder.
        }

        return $sql;                                                                    // regardless of the path above, the saved query string is always returned.
    }

    public function insert($table, $data) {                                             // Insert query,
        $sql = sprintf(                                                                 // i build the $sql with sprintf,
            'INSERT INTO `%s` (%s) VALUES (%s)',                                        // create the query with wildcard placeholders,
            $table,                                                                     // set the table to the first placeholder,
            implode(', ', array_keys($data)),                                           // add the $data keys to the second placeholder,
            ':' . implode(', :', array_keys($data))                                     // add a placeholder for the values as the third placeholder,
        );

        return $sql;                                                                    // return the $sql string.
    }

    public function update($table, $data, $ids) {                                       // Update query with WHERE, AND functions,
        $update;                                                                        // $update variable to store the data pairs,

        foreach($data as $key => $value) {                                              // loop over all data pairs,
            if(!isset($update)) {                                                       // if $update isnt set,
                $update = $key . '=' . "'" . $value . "'";                              // i add the data pairs directly.
            } elseif(isset($update)) {                                                  // If update is set,
                $update = $update . ', ' . $key . ' = ' . "'" . $value . "'";           // i append the data pairs after the stored $update.
            }
        }

        if(count($ids) > 1 && count($ids) !== 3) {                                      // If the $id pairs are more then 1 but no more then 2,
            $sql = sprintf(                                                             // i build the $sql with sprintf,
                'UPDATE %s SET %s WHERE %s = %s AND %s = %s',                           // create the query with wildcard placeholders,
                $table,                                                                 // set the table to the first placeholder,
                $update,                                                                // add the update string to the second placeholder,
                implode(array_keys(array_slice($ids, 0, 1))),                           // add the first id pairs key to the third placeholder,
                ':' . implode(array_keys(array_slice($ids, 0, 1))),                     // add a placeholder for its value to the fourth placeholder,
                implode(array_keys(array_slice($ids, 1, 2))),                           // add the second id pairs key to the fifth placeholder,
                ':' . implode(array_keys(array_slice($ids, 1, 2)))                      // add a placeholder for its value to the sixed placeholder.
            );
        } else {                                                                        // If there was no more then 1 $id pairs,
            $sql = sprintf(                                                             // i build the $sql with sprintf,
                'UPDATE %s SET %s WHERE %s = %s',                                       // create the query with wildcard placeholders,
                $table,                                                                 // set the table to the first placeholder,
                $update,                                                                // add the update string to the second placeholder,
                implode(array_keys(array_slice($ids, 0, 1))),                           // add the first id pairs key to the third placeholder,
                ':' . implode(array_keys(array_slice($ids, 0, 1)))                      // add a placeholder for its value to the fourth placeholder,
            );
        }

        return $sql;                                                                    // regardless of the path above, the saved query string is always returned.
    }

    public function delete($table, $ids) {                                              // Delete query with WHERE, AND fucntions,
        if(count($ids) > 1 ) {                                                          // check if there are more then 1 id pairs,
            $sql = sprintf(                                                             // build the $sql with sprintf,
                'DELETE FROM %s WHERE %s = %s AND %s = %s',                             // create the query with wildcard placeholders,
                $table,                                                                 // set the table to the first placeholder,
                implode(array_keys(array_slice($ids, 0, 1))),                           // add the first ids pairs key to the second placeholder,
                ':' . implode(array_keys(array_slice($ids, 0, 1))),                     // add a placeholder for its value to the third placeholder,
                implode(array_keys(array_slice($ids, 1, 2))),                           // add the second ids pairs key to the fourth placeholder,
                ':' . implode(array_keys(array_slice($ids, 1, 2)))                      // add a placeholder for its value to the fifth placeholder.
            );
        } else {                                                                        // If ids is only 1 data pair,
            $sql = sprintf(                                                             // build the $sql with sprintf,
                'DELETE FROM %s WHERE %s = %s',                                         // create the query with wildcard placeholders,
                $table,                                                                 // set the table to the first placeholder,
                implode(array_keys($ids)),                                              // add the first ids pairs key to the second placeholder,
                ':' . implode(array_keys($ids))                                         // add a placeholder for its value to the third placeholder.
            );
        }

        return $sql;                                                                    // regardless of the path above, the saved query string is always returned.
    }

    public function count($ids) {                                                       // Select query with where statement,
        $sql = sprintf("SELECT count(*) FROM `items` WHERE `Item_Reeks` = %s",          // build sql with sprintf,
            ':' . implode(array_keys(array_slice($ids, 0 , 1)))                         // use placeholder for the $ids,
        );

        return $sql;                                                                    // and return the query.
    }

    public function testTable($name) {                                                  // The testTable function based on a provided name,
        $sql = sprintf( "SELECT 1 FROM %s LIMIT 1", $name );                            // build sql using sprintf inject the name string right away,
        
        return $sql;                                                                    // and return the query.
    }
}