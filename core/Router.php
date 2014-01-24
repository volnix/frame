<?php

namespace Frame\Core;

class Router {
	
	public function start()
	{
		$routes = [];
		$controller = "";
		$method = "index";
		$args = [];
		
		$uri = $_SERVER['REQUEST_URI'];
		$route_regex = sprintf("/%s([a-z0-9\/]*)/", preg_quote(INDEX_FILE));
		
		if (preg_match($route_regex, $uri, $matches))
		{
			$routes = array_filter(explode("/", $matches[1]));
		}
		
		// reset the keys to 0 after the array_filter
		$routes = array_values($routes);
		
		if (count($routes) == 1)
		{
			$controller = $routes[0];
		}
		elseif (count($routes) == 2)
		{
			$controller = $routes[0];
			$method = $routes[1];
		}
		elseif (count($routes) > 2)
		{
			$controller = $routes[0];
			$method = $routes[1];
			$args = array_slice($routes, 2);
		}
		elseif (defined('DEFAULT_CONTROLLER'))
		{
			$controller = DEFAULT_CONTROLLER;
		}
		else
		{
			die("You must pass in a route or set a default controller.");
		}
		
		// if your app namespace is set, then tack on our namespace to our controller name
		if (defined('APP_NAMESPACE_PREFIX'))
		{
			$controller = sprintf("%s\\controllers\\%s", APP_NAMESPACE_PREFIX, $controller);
		}
		
		if (!class_exists($controller, TRUE))
		{
			header("HTTP/1.0 404 Not Found");
			die("404: Class {$controller} not found!");
		}

		// annnnnd, GO!
		$application = new $controller;
		
		if (!method_exists($application, $method))
		{
			header('HTTP/1.1 500 Internal Server Error');
			die("500: Method {$method} not found!");
		}
		
		call_user_func_array([$application, $method], $args);
	}
}