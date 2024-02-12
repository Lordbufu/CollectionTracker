<?php
// Some stuff dint really fit into the classes, so i bootstrap them to ensure there included\loaded.
use App\Core\{App, Processing};
use App\Core\Database\{QueryBuilder, Connection};

// Create a registry binding for the database config file, creating the following syntax: "App:get('config')[configOption]".
App::bind( 'config', require '../config.php' );

// Create a registry binding for the database connection, creating the following syntax: "App::get('database')->functionName($parameters)"
App::bind( 'database', new QueryBuilder( Connection::make(App::get('config')['database']) ) );

// Create a processing link, for more readable code for example: "App::get('processing')->functionName($parameters)".
App::bind('processing', new Processing);

// Create a session manager link
App::bind('session', new SessionMan);

?>