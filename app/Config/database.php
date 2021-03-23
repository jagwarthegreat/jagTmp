<?php

session_start();

$mysqli = new mysqli($database['DB_HOST'], $database['DB_USERNAME'], $database["DB_PASSWORD"]);

if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: " . $mysqli->connect_error;
	exit();
}

// CLASS AUTO LOADER
spl_autoload_register(function ($class) {
	include __DIR__ . '/autoload.php';
	if (array_key_exists($class, $classes)) {
		require_once $classes[$class];
	}
});
