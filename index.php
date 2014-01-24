<?php

namespace Frame\Core;

// these should be configured per your environment
$core_path = __DIR__ . DIRECTORY_SEPARATOR . "core";
$app_path = __DIR__ . DIRECTORY_SEPARATOR . "app";
$app_namespace_prefix = "Frame\\App";

define('INDEX_PATH', __FILE__);
define('INDEX_FILE', basename(__FILE__));

if (!empty($core_path))
{
	define('CORE_PATH', $core_path);
}
else
{
	die("No core path defined.");
}

if (!empty($app_path))
{
	define('APP_PATH', $app_path);
}
else
{
	die("No application path defined.");
}

if (!empty($app_namespace_prefix))
{
	define('APP_NAMESPACE_PREFIX', $app_namespace_prefix);
}

require_once(CORE_PATH . DIRECTORY_SEPARATOR . "Autoloader.php");
$loader = new \Frame\Core\Autoloader;
$loader->register();

if (defined('APP_NAMESPACE_PREFIX'))
{
	// map our mvc namespaces
	$controllers = APP_NAMESPACE_PREFIX . "\\Controllers";
	$models = APP_NAMESPACE_PREFIX . "\\Models";
	$views = APP_NAMESPACE_PREFIX . "\\Views";
	
	$loader->addNamespace($controllers, APP_PATH . DIRECTORY_SEPARATOR . "controllers");
	$loader->addNamespace($models, APP_PATH . DIRECTORY_SEPARATOR . "models");
	$loader->addNamespace($views, APP_PATH . DIRECTORY_SEPARATOR . "views");
}

require_once($core_path . DIRECTORY_SEPARATOR . "Router.php");
//require_once(__DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php");

$app = new \Frame\Core\Router;
$app->start();