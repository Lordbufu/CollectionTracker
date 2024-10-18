<?php
    // Some stuff dint really fit into the classes, so i bootstrap them to ensure there included\loaded.
    use App\Core\{ App, Processing, SessionMan, User, Collection, Isbn };
    use App\Core\Database\{ QueryBuilder, Connection };

    // Create a registry binding for the database config file, creating the following syntax: "App::get('config')[configOption]".
    App::bind( "config", require "../config.php" );

    // Create a registry binding for the database connection, creating the following syntax: "App::get('database')->functionName($parameters)"
    App::bind( "database", new QueryBuilder( Connection::make( App::get( "config" )["database"] ) ) );

    App::bind( "user", new User );                                                                              // Create binding for the User class.
    App::bind( "collection", new Collection );                                                                  // Create binding for the Collection class
    App::bind( "session", new SessionMan );                                                                     // Create binding for the Session Manager class.
    App::bind( "isbn", new Isbn );                                                                              // Create binding for the ISBN class.

?>