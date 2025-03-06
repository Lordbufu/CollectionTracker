<?php

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
        /* Load/Init the class map and enviroment string. */
        self::$classMap = self::initClassMap();
        self::$envString = self::initEnvString(base_path('enviroment.txt'));

        /* Create a container, and bind the database class and connection to it. */
        $cont = new Container();

        $cont->bind('database', function () {
            $config = require base_path('config.php');
            $envConf = $config[self::$envString];

            return new Database($envConf);
        });

        /* Throw costum Exception if the binding the database class/connection failed. */
        if(!is_object($cont->resolve('database')) || !empty($cont->resolve('database')->errors)) {
            throw new Exception('The Database connection failed to initialize!');
        }

        /* Loop over the class map, and bind them all to the container, then set the finished container to the App. */
        foreach(self::$classMap as $path => $obj) {
            $cont->bind($path, $obj);
        }

        self::setContainer($cont);

        /* Check database tables and default admin, and attempt to create if missing, recheck again to check for errors and throw error if failed. */
        if(!self::resolve('database')->checkDatabase()) {
            self::resolve('database')->createDefDb();
        }

        if(!self::resolve('database')->checkDatabase()) {
            throw new Exception(self::resolve('errors')->getError('database', 'default-content'));
        }

        /* Return a boolean, based on if the container is a object or not. */
        return is_object(self::$container);
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

            Return Value: String.
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

            Return Value: Object.
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

            Return Value: Object\Class.
     */
    public static function resolve($key) {
        return self::$container->resolve($key);
    }

    /*  checkDevice():
            This function uses the browser user agent, to detect what device is being used.
            The API MobileDetect, helps me seperate mobile/tablet and desktop clients, to ensure the right CSS is applied.
                $detect (Object)    - The class object for MobileDetect.
            
            Return value: String.
     */
    public static function checkDevice() {
        $detect = new MobileDetect();

        if(isset($_SERVER['HTTP_USER_AGENT'])) {
		    $detect->setUserAgent($_SERVER['HTTP_USER_AGENT'] );
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

    /*  setVersion():
            Attempt to set version of the App, based on the string value in the version.txt.

            Return Value: String.
     */
    public static function setVersion() {
        $versionFile = fopen('../version.txt', 'r');
        $version = fread($versionFile, filesize('../version.txt'));
        fclose($versionFile);

        return $version;
    }

    /*  view($path, $attributes=[]):
            This function requires the correct view, so the page can be rendered, and an empty array to pass data to the page.
            It also set the device type and version, in a empty attributes array, so these are always included on page as $device and $version.
                $path (String)      - The 'path' associated with the requested view.
                $attributes (Arr)   - The data array, that is used to pass on data to the view, always including the device type and version string.
            
            Return Value: Require view file.
     */
    public static function view($path, $attributes = []) {
        $attributes['device'] = static::checkDevice();
        $attributes['version'] = static::setVersion();

        extract($attributes);

        require base_path('app/http/views/' . $path);
    }

    /*  redirect($path, $tag):
            This function simply redirect the user, to the desired path based on other logic, so the router can process said request.
            It includes a session _flash loop, to preserve flashed data, as it sometimes get lost between redirects on more complex logic.
                $path (String)  - The path the user needs to be redirect to.
                $tag (Boolean)  - (Optional) A flag to indicate if the flash data should be preserved.
            
            Return Valeue: New location via the header.
     */
    public static function redirect($path, $tag = null) {
        if($tag && !isset($_SESSION['_flash']['tags']['redirect'])) {
            App::resolve('session')->flash('tags', ['redirect' => TRUE]);
        }

        return header("location:https://{$_SERVER['SERVER_NAME']}/{$path}");
    }
}