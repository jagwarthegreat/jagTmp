<?php
require_once 'app/Routes/web.php';

$base_name = APP_NAME;
$uri = $_SERVER['REQUEST_URI'];
$request = explode('?', $uri, 2);
$uriRoutes = str_replace("/" . $base_name, "", $request[0]);

if (array_key_exists($uriRoutes, $routes)) {
	require_once 'app/Controllers/' . $routes[$uriRoutes] . '.php';
	$controllerClass = new $routes[$uriRoutes];
	$controllerClass->index();
}
