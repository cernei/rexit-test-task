<?php

namespace Kernel;

class Router
{
    private $routes = [];

    public function addRoute($url, $class, $method)
    {
        $this->routes[] = [
            'url' => $url,
            'class' => $class,
            'method' => $method,
        ];
    }

    public function route($url)
    {
        foreach ($this->routes as $route) {
            $pattern = '/' . str_replace('/', '\/', $route['url']) . '/';
            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Remove the full match
                return [
                    'class' => $route['class'],
                    'method' => $route['method'],
                    'params' => $matches,
                ];
            }
        }

        return null; // No matching route found
    }
}
