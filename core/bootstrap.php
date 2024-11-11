<?php

    /*  bootstrap.php:
            Here i mainly bootstrap App bindings, that allow mw to more easily use certain classes.
            It basically left over from the online course this App is based on, i see no reason to move/change this atm.
     */

    use App\Core\{ App, SessionMan, User, Collection, Isbn };
    use App\Core\Database\{ QueryBuilder, Connection };

    App::bind( "config", require "../config.php" );                                                             // Create a binding for the DB config file.
    App::bind( "database", new QueryBuilder( Connection::make( App::get( "config" )["database"] ) ) );          // Create a binding for the querry builder.
    App::bind( "user", new User );                                                                              // Create a binding for the User class.
    App::bind( "collection", new Collection );                                                                  // Create a binding for the Collection class
    App::bind( "session", new SessionMan );                                                                     // Create a binding for the Session Manager class.
    App::bind( "isbn", new Isbn );                                                                              // Create a binding for the ISBN class.
    
?>
