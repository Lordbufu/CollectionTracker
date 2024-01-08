<?php
/* TODO List: - Few comments are still not clear enough for the code there intended for. */

namespace App\Core;

// All things related to routing requests.
class Router {
    // Routes multidimensional array, to load the routes file into.
    protected $routes = [
        'GET' => [],
        'POST' => []
    ];
    
    // Load function to 'construct' our routes.
    public static function load($file){
        $router = new static;
        require $file;
        return $router;
    }
    
    // Get function.
	public function get($uri, $controller) { $this->routes['GET'][$uri] = $controller; }
	
    // Post function.
	public function post($uri, $controller) { $this->routes['POST'][$uri] = $controller; }
    
    // Direct function.
    // TODO: Might require a bit more clearly defined comments.
    public function direct($uri, $requestType) {
        // Loop over all routes from the specific request type.
        foreach ($this->routes[$requestType] as $route => $controller) {
            // The rest is still a bit of magic to me, though i should have the original comments somewhere.
            if( strpos($route, '{') && strpos($route, '}') ) {
                $pattern = '/(.*)\/(\{(.*)\})/i';
                $replacement = '$1\/(\d+)';
				$regex = preg_replace($pattern, $replacement, $route);
				$result = preg_match('/' . $regex . '/', rawurldecode($uri), $matches);
                $parts = explode('@', $this->routes[$requestType][$route]);
				
                return $this->callAction( $parts[0], $parts[1], $matches[1] );
            } else {
				if (array_key_exists($uri, $this->routes[$requestType])) {
					return $this->callAction( ...explode('@', $this->routes[$requestType][$uri]) );
				}
			}
		}
        // Throw and general error if catching a Exception
        throw new \Exception('No route defined for this URI.');
    }
  
    // callAction function, to ensure the right controller action is triggered.
    // TODO: Might require a bit more clearly defined comments.
    protected function callAction($controller, $action, $id = null) {
        // The rest is still a bit of magic to me, though i should have the original comments somewhere.
        $controller = "App\\Controllers\\{$controller}";
        $controller = new $controller;
        // If the function we want to trigger is not there,
        if (! method_exists($controller, $action)) {
            // we throw a general error.
            throw new \Exception("{$controller} does not respond to the {$action} action.");
        }

        // If all is well, we terun the right controller and action (function).
        return $controller->$action($id);
    }
}

?>