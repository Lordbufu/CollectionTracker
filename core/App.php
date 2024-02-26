<?php

namespace App\Core;

use Detection\MobileDetect;

class App {
    // Create a static registry, to store/load/link(bind) important features.
    protected static $registry = [];
    protected static $version;
    protected static $device;

    //  bind($key, $value):
    //      This function binds what i want to link, to the App so i can only use the App to acess other classes.
    //
    //      $key (string)       - The key name i want to use for said feature.
    //      $value (object)     - The feature i want to bind to said key.
    //
    public static function bind($key, $value) {
        static::$registry[$key] = $value;
    }

    // This function tries to access the $register, and return the linked features if there is a match.
    //  $key (string)   - The registry key i want to access.
    public static function get($key) {
        // Check if key is stored in registry, throw Exception if not
        if(! array_key_exists($key, static::$registry)) {
            throw new Exception("No {$key} is bound in the container.");
        }
        // return said feature
        return static::$registry[$key];
    }

    // Test functions
    public static function checkDevice() {
        // Init detect framework
        $detect = new MobileDetect();
        // Give it the user agent string
		$detect->setUserAgent($_SERVER['HTTP_USER_AGENT']);

        // Detect if mobile
        if($detect->isMobile()) { static::$device = "mobile"; }

        // Detect if tablet
        if($detect->isTablet()) { static::$device = "tablet"; }

        // Default to desktop in other cases
        if(!$detect->isMobile() && !$detect->isTablet()) { static::$device = "desktop"; }

        return;
    }

    public static function setVersion() {
        // Open, read and close the version.txt, so the content is stored in the version variable.
        $versionFile = fopen("../version.txt", "r");
        static::$version = fread($versionFile, filesize("../version.txt"));
        fclose($versionFile);

        return;
    }

    // A fairly simple view function, to return the correct view and data to the browser.
    // Now also include a device check, so i can properly add css files based on that.
    //  $name (string)       - The first part of the view file its name.
    //  $data (Multid Array) - The data i want to send to the page.
    public static function view($name, $data = []) {
        static::checkDevice();
        static::setVersion();

        // Add device tag to data array
        $data['device'] = static::$device;
        $data['version'] = static::$version;

        // Extract $data, so we can use the key's as variables on the page.
        extract($data);

        // Return the requested view.
        return require "../app/views/{$name}.view.php";
    }

    // A redirect function to redirect PhP to the right route.
    public static function redirect($path, $data = []) {
        static::checkDevice();
        static::setVersion();

        // Add device tag to data array
        $data['device'] = static::$device;
        $data['version'] = static::$version;

        // Extract $data, so we can use the key's as variables on the page.
        extract($data);

        // Always use the $_SERVER['SERVER_NAME'] variable, to make it function on other host names.
        header("location:https://{$_SERVER['SERVER_NAME']}/{$path}");
        
        return;
    }
}
?>