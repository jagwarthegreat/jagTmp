<?php

error_reporting(0);
include __DIR__ . '/../../env.php';

$configApp = [
	'locale' => 'en',
	'timezone' => "Asia/Manila"
];

ini_set('date.timezone', $configApp["timezone"]);

foreach ($defines as $dName => $dValue) {
	define($dName, $dValue);
}

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/services.php';
