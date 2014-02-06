<?php

namespace Simple;

class Router {
	
	public function start()
	{
		$routes = [];
		$controller = "";
		$method = "index";
		$args = [];

		// this is nasty.  just let it be.
		$path = ltrim(explode(INDEX, parse_url($_SERVER['REQUEST_URI'])['path'])[1], "/");
		
		if (preg_match("/\//", $path))
		{
			$routes = array_filter(explode("/", $path));
		}
		else
		{
			$routes = [$path];
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
		
		$controller_name = sprintf("%s\\Controllers\\%s", APP_NAMESPACE, $controller);
		
		if (!class_exists($controller_name, TRUE))
		{
			header("HTTP/1.0 404 Not Found");
			die("404: Class {$controller_name} not found!");
		}

		// annnnnd, GO!
		$application = new $controller_name;
		
		if (!method_exists($application, $method))
		{
			header('HTTP/1.1 500 Internal Server Error');
			die("500: Method {$method} not found!");
		}
		
		call_user_func_array([$application, $method], $args);
	}
	
	public static function base($path = "")
	{
		$pieces = explode(INDEX, $_SERVER['REQUEST_URI']);
		$base = ($_SERVER['HTTPS'] ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $pieces[0] . INDEX . "/" . $path;
		return $base;
	}
}
