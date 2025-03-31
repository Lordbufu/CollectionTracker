<?php

namespace App\Core;

class Container {
    protected $bindings = [];

    /*  bind($name, $resolver):
            A function to bind a name, to a class object (resolver) that i would like to use.

            Return Value: None.
     */
    public function bind($name, $resolver) {
        $this->bindings[$name] = $resolver;
    }

    /*  resolve($key):
            Attempts to match a string value, with a bound class object.

            Return Value: Class Object.
     */
    public function resolve($key) {
        if( !array_key_exists($key, $this->bindings) ) {
            throw new \Exception("No matching binding for {$key}");
        }

        $resolver = $this->bindings[$key];

        return call_user_func($resolver);
    }
}