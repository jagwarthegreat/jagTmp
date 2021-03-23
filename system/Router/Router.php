<?php
require_once __DIR__ . '/../../app/Routes/web.php';

$base_name = "jagTmp";
$uri = $_SERVER['REQUEST_URI'];
$request = explode('?', $uri, 2);
$uriRoutes = str_replace("/" . $base_name . '/public', "", $request[0]);

if (array_key_exists($uriRoutes, $routes)) {
	$classScaffold = explode(":", $routes[$uriRoutes]);
	require_once  __DIR__ . '/../../app/Controllers/' . $classScaffold[0] . '.php';
	$controllerClass = new $classScaffold[0];
	$controllerClass->$classScaffold[1]();
} else {
	page_404();
}
