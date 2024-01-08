<?php
// Some stuff dint really fit into the classes, so i bootstrap them to ensure there included\loaded.
use App\Core\{App, Processing};
use App\Core\Database\{QueryBuilder, Connection};

// To allow of a config file for the database, i need to bind said file to a key.
// Tgus creates the following code the access said file: "App::get('config')".
App::bind(
        'config',
        require '../config.php'
);

// Not really sure what this does (other then store said config in a variable), and if its still required in this code.
$config = App::get('config');

// To make it easier to use the QueryBuilder, and interace with the database, i bind the QueryBuilder to a database key.
// The connection i pass into the construction, used the above defined config file, so its also easier to secure the connection details.
// This creates the following code the use for database actions: "App::get('database')->functionName".
App::bind(
        'database',
        new QueryBuilder(
                Connection::make(App::get('config')['database'])
        )
);

// Create a processing link, for more readable code for example: "App::get('processing')->functionName($parameters)".
App::bind('processing', new Processing);
?>