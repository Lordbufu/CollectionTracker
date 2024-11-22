<?php
    /*  bootstrap.php:
            Here i mainly bootstrap App bindings, that allow me to more easily use certain classes.
            Its bit of a left over from the course i followed, normally this isnt how you would manage these things.
            But for the scope of this project, this is more then fine imo.
     */

    use App\Core\{ App, Errors, SessionMan, User, Albums, Series, Collecties, Isbn };
    use App\Core\Database\{ QueryBuilder, Connection };

    /* Bind all database related thing, and extra classes */
    App::bind( "config", require "../config.php" );
    App::bind( "database", new QueryBuilder( Connection::make( App::get( "config" )["database"] ) ) );
    App::bind( "user", new User );
    App::bind( "session", new SessionMan );
    App::bind( "isbn", new Isbn );
    App::bind( "errors", new Errors );
    App::bind( "albums", new Albums );
    App::bind( "series", new Series );
    App::bind( "collecties", new Collecties );

?>