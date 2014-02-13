<?php

namespace Simple;

class Router {
	
	public static function start($namespace)
	{
		$routes = [];
		$controller = "";
		$method = "index";
		$args = [];
		
		// split our url by our index
		$url_parts = explode(self::get_index(), $_SERVER['REQUEST_URI']);
		$route_parts = explode("/", ltrim($url_parts[1]));
		
		// filter any null routes and reset our keys
		$routes = array_values(array_filter($route_parts));
		
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
		
		$controller_name = sprintf("%s\\Controllers\\%s", $namespace, $controller);
		
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
	
	public static function make_url($path = "")
	{
		$pieces = explode(self::get_index(), $_SERVER['REQUEST_URI']);
		$base = ($_SERVER['HTTPS'] ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $pieces[0] . self::get_index() . "/" . $path;
		return $base;
	}
	
	private static function get_index()
	{
		preg_match('/[a-z\_0-9]+\.php/i', $_SERVER['REQUEST_URI'], $matches);
		return $matches[0];
	}
}
