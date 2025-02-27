<?php

namespace App\Core;

use Exception;      // This could be ommited, but i like having it here for clarity reasons.

/*  Container Class:
        This class is used to create a App Service Container, allowing a easy interface with the App.
        Making the code more readable, and easier to use (in theory), similar to a API approach.
 */
class Container {
    protected $bindings = [];

    /*  bind($name, $resolver): A function to bind a name, to a class object (resolver) that i would like to use. */
    public function bind($name, $resolver) {
        $this->bindings[$name] = $resolver;
    }

    /*  resolve($key): Attempts to match a string value, with a bound class object, return the object as closure (useable class object) */
    public function resolve($key) {
        if( !array_key_exists($key, $this->bindings) ) {
            throw new Exception("No matching binding for {$key}");
        }

        $resolver = $this->bindings[$key];

        return call_user_func($resolver);
    }
}