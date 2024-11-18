<?php
    /*  bootstrap.php:
            Here i mainly bootstrap App bindings, that allow mw to more easily use certain classes.
            It basically left over from the online course this App is based on, i see no reason to move/change this atm.
     */
    use App\Core\{ App, Errors, SessionMan, User, Albums, Series, Collecties, Isbn, Collection };
    use App\Core\Database\{ QueryBuilder, Connection };

    /* Bind all database related thing, and extra classes */
    App::bind( "config", require "../config.php" );
    App::bind( "database", new QueryBuilder( Connection::make( App::get( "config" )["database"] ) ) );
    App::bind( "user", new User );
    App::bind( "collection", new Collection );
    App::bind( "session", new SessionMan );
    App::bind( "isbn", new Isbn );
    App::bind( "errors", new Errors );
    App::bind( "albums", new Albums );
    App::bind( "series", new Series );
    App::bind( "collecties", new Collecties );
?>
