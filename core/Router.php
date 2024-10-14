<?php
/* TODO List: Few comments are still not clear enough for the code there intended for. */

namespace App\Core;

class Router {
    /* Routes multidimensional array, to load the routes file into. */
    protected $routes = [ "GET" => [], "POST" => [] ];
    
    /* Load function to 'construct' our routes. */
    public static function load($file){
        $router = new static;
        require $file;
        return $router;
    }
    
    /* Get function. */
	public function get($uri, $controller) {
        $this->routes["GET"][$uri] = $controller;
    }
	
    /* Post function. */
	public function post($uri, $controller) {
        $this->routes["POST"][$uri] = $controller;
    }
    
    /* Direct function. */
    public function direct($uri, $requestType) {
        foreach ($this->routes[$requestType] as $route => $controller) {

            if( strpos( $route, "{" ) && strpos( $route, "}" ) ) {
                $pattern = "/(.*)\/(\{(.*)\})/i";
                $replacement = "$1\/(\d+)";
				$regex = preg_replace( $pattern, $replacement, $route );
				$result = preg_match( "/" . $regex . "/", rawurldecode( $uri ), $matches );
                $parts = explode( "@", $this->routes[$requestType][$route] );
                return $this->callAction( $parts[0], $parts[1], $matches[1] );

            } else {

				if ( array_key_exists( $uri, $this->routes[$requestType] ) ) {
					return $this->callAction( ...explode( "@", $this->routes[$requestType][$uri] ) );
				}
			}
		}
        throw new \Exception( "No route defined for this URI." );
    }
  
    /* callAction function, to ensure the right controller action is triggered. */
    protected function callAction($controller, $action, $id=null) {
        $controller = "App\\Controllers\\{$controller}";
        $controller = new $controller;

        if (! method_exists( $controller, $action ) ) {
            throw new \Exception( "{$controller} does not respond to the {$action} action." );
        }

        return $controller->$action( $id );
    }
}

?>