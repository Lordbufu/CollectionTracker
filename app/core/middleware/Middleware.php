<?php

namespace App\Core\Middleware;

class Middleware {
    public const MAP = [                                                                    // Create a middleware map,
        'auth' => Auth::class,                                                              // All basic authenticated users (gast/gebruiker/beheerder),
        'guest' => Guest::class,                                                            // The default Guest,
        'user' => AuthUser::class,                                                          // The regular User,
        'admin' => AuthAdmin::class                                                         // The Administrator,
    ];

    /*  resolve($key):
            This function attempts match router middleware requests, and lets the associated class handle the request.
                $key (String)   - The name of the middleware that needs to be resolved.
            
            Return Value: None.
     */
    public static function resolve($key) {
        if(!$key) { return; }                                                               // If no key is set return to caller.

        $middleware = static::MAP[$key] ?? false;                                           // Attempt to set the middleware to the right class,

        if(!$middleware) {                                                                  // if false was set,
            throw new \Exception("not matching middleware found for key '{$key}'.");        // we throw an exception.
        }

        (new $middleware)->handle();                                                        // If middleware was set, delegate to its handle function.
    }
}