<?php

namespace App\Core;

use App\Core\Middleware\Middleware;
use App\Core\App;

class Router {
    protected $routes = [];

    protected function add($method, $uri, $controller) {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method,
            'middleware' => null
        ];

        return $this;
    }

    /* Method function, simply returning the add function. */
    public function get($uri, $controller) {
        return $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller) {
        return $this->add('POST', $uri, $controller);
    }

    public function delete($uri, $controller) {
        return $this->add('DELETE', $uri, $controller);
    }

    public function patch($uri, $controller) {
        return $this->add('PATCH', $uri, $controller);
    }

    public function put($uri, $controller) {
        return $this->add('PUT', $uri, $controller);
    }

    public function only($key) {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;

        return $this;
    }

    public function route($uri, $method) {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
                Middleware::resolve($route['middleware']);

                return require base_path('app/http/controllers/' . $route['controller']);
            }
        }

        $this->abort();
    }

    protected function abort($code = 404) {
        http_response_code($code);

        require base_path("app/http/views/errors/{$code}.php");

        die();
    }

    // Currently not used ??
    public function previousUrl() {
        return $_SERVER['HTTP_REFERER'];
    }
}