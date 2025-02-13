<?php

/*  MobileDetect notes:
        API/Library seems to still work, but has issues in certain use cases.
        I opted to keep this in the project, because it seems to do the job for me so far.
        Iff i get user error reports, it might change to something else.
 */

namespace App\Core;

use Detection\MobileDetect;

class App {
    /* Create a static registry, to store/load/link(bind) important features. */
    protected static $registry = [];
    protected static $version;
    protected static $device;

    /*  bind($key, $value):
            This function binds what i want to link, to the App so i can only use the App to acess other classes.
                $key (string)       - The key name i want to use for said feature.
                $value (object)     - The feature i want to bind to said key.

            Return value: None
     */
    public static function bind( $key, $value ) {
        return static::$registry[$key] = $value;
    }

    /*  get($key):
            This function tries to access the $register, and return the linked features if there is a match.
                $key (string)   - The registry key i want to access.
            
            Return value: None
     */
    public static function get( $key ) {

        if( !array_key_exists( $key, static::$registry ) ) {
            throw new Exception( "No {$key} is bound in the container." );
        }

        return static::$registry[$key];
    }

    /*  checkDevice():
            This function uses the browser user agent, to detect what device is being used.
            The API MobileDetect, helps me seperate mobile/tablet and desktop clients, to ensure the right CSS is applied.
                $detect (Object)    - The class object for MobileDetect.
            
            Return value: None
     */
    public static function checkDevice() {
        $detect = new MobileDetect();
        /* Adding a useragent check, to remove log clutter/spam from some bots. */
        if( isset( $_SERVER["HTTP_USER_AGENT"] ) ) {
		    $detect->setUserAgent( $_SERVER["HTTP_USER_AGENT"] );
        /* Simple die, so if people are getting this, they know why. */
        } else {
            die( App::get( "errors" )->getError( "UsrAgeErr" ) );
        }

        if( $detect->isTablet() && $detect->isMobile() ) {
            return static::$device = "tablet";
        } else if ( $detect->isMobile() ) {
            return static::$device = "mobile";
        } else if(!$detect->isMobile() && !$detect->isTablet()) {
            return static::$device = "desktop";
        } else {
            die( App::get( "errors" )->getError( "deviceErr" ) );
        }
    }

    /*  setVersion():
            This function simply opens a the 'version.txt' file, and stores the version number for later use.
                $versionFile (file) - Temp store for the file i want to read.
            
            Return value: None
     */
    public static function setVersion() {
        $versionFile = fopen( "../version.txt", "r" );
        static::$version = fread($versionFile, filesize( "../version.txt" ) );
        return fclose( $versionFile );
    }

    /*  view($name, $data=[])
            A fairly simple view function, to return the correct view and data to the browser.
            Now also include a device check, so i can properly add css files based on that.
                $name (string)       - The first part of the view file its name.
                $data (Multid Array) - The data i want to send to the page, defaulting to a empty array.

            Return value: None
     */
    public static function view( $name, $data=[] ) {
        static::checkDevice();
        static::setVersion();
        $data["device"] = static::$device;
        $data["version"] = static::$version;
        extract( $data );
        return require( "../app/views/{$name}.view.php" );
    }

    /*  redirect($path, $data=[]):
            A simple redirect based on the server_naam variable, so the uri is in FQDN style at all time.
            This function now also includes the data for the MobileDetect API and setVersion, while also allowing the normal way of passing data to the page.
                $path (string)          - The path for the redirect, not designed for recursive redirects, when you are already on a sub-path.
                $data (Multid Array)    - The data array that needs to be passed on to the page, incl the MobileDetect and version data.

            Return value: None
     */
    public static function redirect( $path, $data=[] ) {
        static::checkDevice();
        static::setVersion();
        $data["device"] = static::$device;
        $data["version"] = static::$version;
        extract( $data );
        return header( "location:https://{$_SERVER['SERVER_NAME']}/{$path}" );
    }
}
?>