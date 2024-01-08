<?php
namespace App\Core;

class App {
    // Create a static registry, to store/load/link(bind) important features.
    protected static $registry = [];

    // (Set) This function binds what i want to link, to an easy to use $key value.
    //  $key (string)       - The key name i want to use for said feature.
    //  $value (object)     - The feature i want to bind to said key.
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

    // A fairly simple view function, to return the correct view and data to the browser.
    //  $name (string)       - The first part of the view file its name.
    //  $data (Multid Array) - The data i want to send to the page.
    public static function view($name, $data = []) {
        // Do note, that i can also still use $data['key'] instead of '$key' directly, extract allows the latter.
        extract($data);
        // And i terun the correct view with a require.
        return require "../app/views/{$name}.view.php";
    }

    // A redirect function to redirect PhP to the right route.
    public static function redirect($path, $data = []) {
        extract($data);
        // Always use the $_SERVER['SERVER_NAME'] variable, to make it function on other host names.
        header("location:http://{$_SERVER['SERVER_NAME']}/{$path}");
        return;
    }
}
?>