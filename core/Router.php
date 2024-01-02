<?php

namespace App\Core;

class Router {
    protected $routes = [
        'GET' => [],
        'POST' => []
    ];
    
    public static function load($file){
        $router = new static;
        require $file;
        return $router;
    }
    
	public function get($uri, $controller) {
		$this->routes['GET'][$uri] = $controller;
	}
	
	public function post($uri, $controller) {
		$this->routes['POST'][$uri] = $controller;
	}
    
    public function direct($uri, $requestType) {
        foreach ($this->routes[$requestType] as $route => $controller) {
            if( strpos($route, '{') && strpos($route, '}') ) {
                $pattern = '/(.*)\/(\{(.*)\})/i';
                $replacement = '$1\/(\d+)';
				$regex = preg_replace($pattern, $replacement, $route);
				$result = preg_match('/' . $regex . '/', rawurldecode($uri), $matches);
                $parts = explode('@', $this->routes[$requestType][$route]);
				
                return $this->callAction(
                    $parts[0], $parts[1], $matches[1]
                );
            } else {
				if (array_key_exists($uri, $this->routes[$requestType])) {
					return $this->callAction(
						...explode('@', $this->routes[$requestType][$uri])
					);
				}
			}
		}
        throw new \Exception('No route defined for this URI.');
    }
    
    protected function callAction($controller, $action, $id = null) {
        $controller = "App\\Controllers\\{$controller}";
        $controller = new $controller;
        if (! method_exists($controller, $action)) {
            throw new \Exception("{$controller} does not respond to the {$action} action.");
        }
    
        return $controller->$action($id);
    }
}

?>