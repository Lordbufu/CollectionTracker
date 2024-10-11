<?php
    // Some stuff dint really fit into the classes, so i bootstrap them to ensure there included\loaded.
    use App\Core\{ App, Processing, SessionMan, User, Collection, Isbn };
    use App\Core\Database\{ QueryBuilder, Connection };

    // Create a registry binding for the database config file, creating the following syntax: "App::get('config')[configOption]".
    App::bind( "config", require "../config.php" );

    // Create a registry binding for the database connection, creating the following syntax: "App::get('database')->functionName($parameters)"
    App::bind( "database", new QueryBuilder( Connection::make( App::get( "config" )["database"] ) ) );

    // Create a user, collection and session links, for more readable code for example: "App::get('user')->functionName($parameters)".
    App::bind( "user", new User );
    App::bind( "collection", new Collection );
    App::bind( "session", new SessionMan );

    // Test code
    // Test binding for the isbn class
    App::bind( "isbn", new Isbn );

?>