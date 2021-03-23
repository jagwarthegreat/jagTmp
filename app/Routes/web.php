<?php

/**
 * Will load the controller of the given URI
 * @param "route" => "class:method"
 * 
 */
$routes = [


	'/login' => 'LoginController:index',
	'/' => 'HomeController:index',
	'/home' => 'HomeController:index'


];
