<?php

namespace App\Core\Database;

/* Load the required use directives, even if i can technically by-pass some, there here for make things more read-/understand-able. */
use PDO, PDOException, Exception;
use App\Core\App;
use App\Core\Database\QueryBuilder;
use App\Core\Database\CreateDb;

class Database {
    /* Global Variables, to handle all db related actions. */
    protected $connection;
    protected $statement;
    protected $result;
    protected $dbState;
    protected $defCheck = [];
    protected $defState = [];
    public $errors = [];

    /*  __construct($config):
            Class constructor, that expect's the entire config.php file, so it can create a new PDO connection.
            If the connection fails, the PDOException is set in the global variable, so i can catch it later on in App::initApp().
                $config (Array)  - The entire config.php returned as a multi-dimensional array.
                $dsn    (String) - The PDO connection string, with the connection details parsed into it.
                $cred   (Array)  - The credentials part of the config, to reduce code length when making the connection.
            
            Return Value: None.
     */
    public function __construct($config) {
        try {
            $dsn = 'mysql:' . http_build_query($config['database'], '', ';');
            $cred = $config['credentials'];
            $this->connection = new PDO($dsn, $cred['username'], $cred['password'], $config['options']);
        } catch(PDOException $e) {
            $this->errors['init-error'] = $e->getMessage();
        }
    }

    /*  executeQuery($data):
            This function is just to re-use code, no need to retype this for every code block in prepQuery().
            The PDOExceptions are stored globaly, but are still included in the return value.
                $data   (Array) - The placeholder data that is passed on from prepQuery.
            
            Return Value:
                On success  : Object.
                On exception: String.
     */
    protected function executeQuery($data=null) {
        try {
            if(!$data) {
                $this->statement->execute();
            } else {
                $this->statement->execute($data);
            }

            return $this;
        } catch(PDOException $e) {
            $this->errors['execute-error'] = $e->getMessage();
            return $this;
        }
    }

    /*  loadCheckData():
            This function simply set the result of the testTable() query, so i can evaluated the expected database structure.
     */
    protected function loadCheckData() {
        if(empty($this->defCheck)) {
            $this->defCheck = [
                'gebruikers' => $this->prepQuery('testTable', 'gebruikers')->getErrorCode(),
                'reeks' => $this->prepQuery('testTable', 'reeks')->getErrorCode(),
                'items' => $this->prepQuery('testTable', 'items')->getErrorCode(),
                'collectie' => $this->prepQuery('testTable', 'collectie')->getErrorCode(),
                'admin' => $this->prepQuery('select', 'gebruikers', [
                                'Gebr_Naam' => 'Administrator'
                           ])->getAll()
            ];
        }

        return;
    }

    /*  checkDatabase():
            This function simply checks if the correct tables, and default admin account are present.
            If said content is not there, it will return false, so the createDefDb() can be called.
                $eval (Boolean)     - The result of the evaluation.
            
            Return Value: Boolean.

            Notes:
                Error string 42S02 = table is not there.
                Error string 00000 = table is there.
     */
    public function checkDatabase() {
        $this->loadCheckData();                                                                                 // Load the check data.
        $eval = FALSE;                                                                                          // Set the initial evaluation state to false.

        foreach($this->defCheck as $name => $error) {                                                           // Loop over each item in $content,
            if($name === 'admin' && empty($error)) {                                                            // if the user query returns nothing,
                $eval = FALSE;                                                                                  // the default account isnt there,
            } elseif($error === '42S02') {                                                                      // if other items have a error string,
                $eval = FALSE;                                                                                  // those tables are not in the database,
            } elseif($error === '00000') {                                                                      // if the above did not trigger,
                $eval = TRUE;                                                                                   // all items most be present.
            }
        }

        return $eval;                                                                                           // return the evaluation.
    }

    /*  createDefDb():
            Create the actual database, based on the outcome of the loadCheckData() function.
            Where checkDatabase serves as a TRUE/FALSE trigger, this actually does something if a table is missing.

            Return Value: None.
     */
    public function createDefDb() {
        $this->loadCheckData();                                                                                 // Load the check data,

        foreach($this->defCheck as $name => $error) {                                                           // loop over check data,
            if($error === '42S02') {                                                                            // check if there was a error code,
                $this->statement = $this->connection->prepare(CreateDb::create($name));                         // prepare the correct query string,
                $this->executeQuery();                                                                          // execute the query string.
            } elseif($name === 'admin' && empty($error)) {
                $this->statement = $this->connection->prepare(CreateDb::create($name));                         // prepare the correct query string,
                $this->executeQuery();                                                                          // execute the query string.
            }
        }

        return;
    }

    /*  prepQuery($table, $type, $ids, $data):
            This function helps create queries, using the QueryBuilder class.
            Instead of evaluation the $type, i use the same name as the QueryBuilder function, so i can use the variable to call the function.
            Leaving me to only evaluate the parameters, so i can perpare and execute the right query.
                $type   (string) - The type of request i want to make select/insert/update/etc.
                $table  (string) - (Optional) The table the query should be directed at (only optional for count queries).
                $ids    (Array)  - (Optional) Identifiers to make queries more specific where applicable.
                $data   (Array)  - (Optional) Data that is required to be added/updated in/to the database.
            
            Return Value:
                On success  : Object ($this).
                On exception: String (PDOException message).
     */
    public function prepQuery($type, $table=null, $ids=null, $data=null ) {
        $builder = new QueryBuilder;

        if(!$ids) {                                                                     /* Path for no $id. */
            if(!$data) {                                                                /* Nested path for no $data. */
                $query = $builder->$type($table);
            } else {
                $query = $builder->$type($table, $data);
            }
        }

        if(!$data) {                                                                    /* Path for no $data. */
            if(!$table) {                                                               /* Nested path for no $table. */
                $query = $builder->$type($ids);
            } else if(!$ids) {                                                          // euhm no sure what happend here, but this was messing up the select queries.
                $query = $builder->$type($table);
            } else {
                $query = $builder->$type($table, $ids);
            }
        }

        if(!isset($query)) {                                                            /* 'Happy' builder path. */
            $query = $builder->$type($table, $data, $ids);
        }

        if($type !== 'testTable') {                                                     /* Prepare statement if type is not testTable. */
            $this->statement = $this->connection->prepare($query);
        } else {                                                                        /* Set attributes first when testing tables. */
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $this->statement = $this->connection->prepare($query);
        }

        if(!$data && !$ids) {                                                           /* No $data and no $ids execute path. */
            return $this->executeQuery();
        }

        if(!$ids && !empty($data)) {                                                    /* No $ids but $data execute path. */
            return $this->executeQuery($data);
        }

        if(!$table && !$data && !empty($ids)) {                                         /* No $table and no $data but $ids execute path. */
            return $this->executeQuery($ids);
        }

        if(!empty($ids)) {                                                              /* And just $ids to catch the last few cases. */
            return $this->executeQuery($ids);
        }

        return $this->executeQuery($ids);
    }

    /*  getAll():
            General get all function, that simple returns all PDO results, without any filtering.
            Including a Exception section, to pass on stored PDOExceptions.

            Return Value:
                On success  : Multi-Dimensional or Associative Arrray.
                On exception: String (PDOException message).
     */
    public function getAll() {
        if(!empty($this->errors)) {
            return $this->errors['execute-error'];
        }

        return $this->statement->fetchAll();
    }

    /*  getSingle():
            General get single function, to resolve single item requests only.
            The result is stored globally first, if nothing is stored a Exception is thrown.

            Return Value:
                On success  : Multi-Dimensional or Associative Arrray.
                On exception: String.
     */
    public function getSingle() {
        if(!empty($this->errors)) {
            return $this->errors['execute-error'];
        }

        $this->result = $this->statement->fetch();

        if(!$this->result) {
            return App::resolve('errors')->getError('database', 'find-error');
        }

        return $this->result;
    }

    /*  getCount():
            This function is specifically for counting items in a reeks, and is basically a singleton.
            All it does, return either a error string, or the result of the count operation without any trailing data.

            Return Value:
                On failure  - String.
                On success  - Integer.
     */
    public function getCount() {
        if(!empty($this->errors)) {
            return $this->errors['execute-error'];
        }

        return $this->statement->fetch()[0];
    }

    /*  countItems():
            For a specific function, i need to count how many items a 'series' has, this function deals with that.

            Return Value:
                On success  : Int.
                On exception: String (PDOException message).
     */
    public function countItems($ids) {
        return $this->prepQuery('count', null, $ids)->getCount();
    }

    /*  getErrorCode():
            To check if there are any error code for this statement, so i can more easily detect the type of error.

            Return Value: String.
     */
    public function getErrorCode() {
        return $this->statement->errorCode();
    }

    /*  find($key):
            This function is desgined to request specific key values, from database queries.
            For example, i can find a 'reeks-index' value for a specific items record, if i already have a item-name or item-index.
                $key (String)   - The name of the database column, that i need the value of.
            
            Return Value: Variable (String/Blob/Int).
     */
    public function find($key) {
        $this->result = $this->statement->fetch();

        if(!isset($this->result[$key])) {
            return App::resolve('errors')->getError('database', 'find-error');
        }

        return $this->result[$key];
    }
}