<?php
/*  TODO List:
        - 
 */

namespace App\Core;

/* Set the use directive, for the classes that need it. */
use PDOException, Exception;
use Detection\MobileDetect;
use App\Core\Database\Database;
use App\Http\Forms\FormValidator;

/*  App Class: This class handels all default app behavior, including a init function, that will prepare the App for use. */
class App {
    /* Global stores used for the App */
    protected static $classMap;
    protected static $envString;
    protected static $container;

    /*  initApp():
            This function initialized all default app functions, like loading the database connection.
            It uses a Class Map, set via initClassMap(), to load all default service classes, into the App's service container.
            Using the enviroment.txt, to load the correct config set, allow me to swap the App state to a different enviroment on the fly.
            Can be evaluated to trigger other logic.

            Return Value: Boolean.
     */
    public static function initApp() {
        self::$classMap = self::initClassMap();                                                         // Load the ClassMap required to populate the service container.
        self::$envString = self::initEnvString(base_path('enviroment.txt'));                            // Load enviroment settings, to load the correct config data set.

        $cont = new Container();                                                                        // Open a new Container instance,

        $cont->bind('database', function () {                                                           // bind the 'database',
            $config = require base_path('config.php');                                                  // load the entire config.php file,
            $envConf = $config[self::$envString];                                                       // grab the data set associate with the current enviroment,

            return new Database($envConf);                                                              // return the new database connection.
        });

        if(!is_object($cont->resolve('database')) || !empty($cont->resolve('database')->errors)) {      // If the connection cant be resolved, or it had errors,
            throw new Exception('The Database connection failed to initialize!');                       // throw a exception to provide usuable feedback.
        }

        foreach(self::$classMap as $path => $obj) {                                                     // Loop over the loaded ClassMap,
            $cont->bind($path, $obj);                                                                   // and bind them to the service container.
        }

        self::setContainer($cont);                                                                      // Set the service container to the App class.

        if(!self::resolve('database')->checkDatabase()) {                                               // If the default database content is missing,
            self::resolve('database')->createDefDb();                                                   // create the default content.
        }

        if(!self::resolve('database')->checkDatabase()) {                                               // Check db again to see if something is still missing,
            throw new Exception(self::resolve('errors')->getError('database', 'default-content'));      // throw new exception if something is wrong.
        }

        return is_object(self::$container);                                                             // Return the container if its a object.
    }

    /*  initClassMap():
            This function only sets the class map array, to the global $classMap.
            Allowing me to auto-load all these classes, into the app service container.
     */
    protected static function initClassMap() {
        $clMap = [
            'router' => function() { return new Router; },
            'errors' => function() { return new Errors; },
            'user' => function() { return new User; },
            'session' => function() { return new SessionMan; },
            'isbn' => function() { return new Isbn; },
            'validator' => function() { return new Validator; },
            'form' => function() { return new FormValidator; },
            'file' => function() { return new FileHandler; },
            'auth' => function() { return new Authenticator; },
            'reeks' => function() { return new Reeks; },
            'items' => function() { return new Items; },
            'collectie' => function() { return new Collectie; }
        ];

        return $clMap;
    }

    /*  initEnvString($file):
            This function parses the enviroment.txt file, to check what the intended run state is.
            The string in this file, corrosponds with the tag in the config.php, so a sub-set of setting can be used 'loaded'.
     */
    protected static function initEnvString($file) {
        $fileStream = fopen($file, 'r');
        $fileContent = fread($fileStream, filesize($file));
        fclose($fileStream);

        return $fileContent;
    }

    /*  SetContainer($container):
            This function sets the passed-in $container, as the App service $container.
            In general this sets the $bindings from the Container class, meaning that class is bound to the service container.
     */
    public static function setContainer($container) {
        self::$container = $container;
    }

    /*  container():
            This function returns the app container as is, without resolving a specific key in it.
     */
    public static function container() {
        return self::$container;
    }

    /*  bind($key, $resolver):
            This function is a pass-true to the Container->bind($key, $resolver) function.
     */
    public static function bind($key, $resolver) {
        self::$container->bind($key, $resolver);
    }

    /*  resolve($key):
            A inherited function from the Container class, with the exact same profile.
     */
    public static function resolve($key) {
        return self::$container->resolve($key);
    }

    // W.I.P. Functions, that might still need seperate files/classes
    /*  checkDevice():
            This function uses the browser user agent, to detect what device is being used.
            The API MobileDetect, helps me seperate mobile/tablet and desktop clients, to ensure the right CSS is applied.
                $detect (Object)    - The class object for MobileDetect.
            
            Return value: None
     */
    public static function checkDevice() {
        $detect = new MobileDetect();
        /* Adding a useragent check, to remove log clutter/spam from some bots. */
        if(isset($_SERVER['HTTP_USER_AGENT'])) {
		    $detect->setUserAgent($_SERVER['HTTP_USER_AGENT'] );
        /* Simple die, so if people are getting this, they know why. */
        }

        if( $detect->isTablet() && $detect->isMobile() ) {
            return "tablet";
        } else if ( $detect->isMobile() ) {
            return "mobile";
        } else if(!$detect->isMobile() && !$detect->isTablet()) {
            return "desktop";
        } else {
            return "unknown";
        }
    }

    /* Attempt to set version on all views that are returned. */
    public static function setVersion() {
        $versionFile = fopen('../version.txt', 'r');
        $version = fread($versionFile, filesize('../version.txt'));
        fclose($versionFile);

        return $version;
    }

    // W.I.P.
    public static function view($path, $attributes = [], $tag = null) {
        if($tag && !isset($_SESSION['_flash']['tags']['redirect'])) {
            App::resolve('session')->flash('tags', [
                'redirect' => TRUE
            ]);
        }

        $attributes['device'] = static::checkDevice();
        $attributes['version'] = static::setVersion();

        extract($attributes);
        require base_path('app/http/views/' . $path);
    }

    // W.I.P.
    public static function redirect($path, $tag = null) {
        if($tag && !isset($_SESSION['_flash']['tags']['redirect'])) {
            App::resolve('session')->flash('tags', ['redirect' => TRUE]);
        }

        return header("location:https://{$_SERVER['SERVER_NAME']}/{$path}");
    }
}