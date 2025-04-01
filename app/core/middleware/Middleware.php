<?php

namespace App\Core\Middleware;

class Middleware {
    /* Middleware map, to link classes to specific keys. */
    public const MAP = [
        'guest' => Guest::class,
        'all' => All::class,
        'auth' => Auth::class,
        'user' => AuthUser::class,
        'admin' => AuthAdmin::class
    ];

    /*  resolve($key):
            This function attempts match router middleware requests, and lets the associated class handle the request.
                $key (String)   - The name of the middleware that needs to be resolved.
            
            Return Value: None.
     */
    public static function resolve($key) {
        if(!$key) {
            return;
        }

        $middleware = static::MAP[$key] ?? false;

        if(!$middleware) {
            throw new \Exception("not matching middleware found for key '{$key}'.");
        }

        (new $middleware)->handle();
    }
}