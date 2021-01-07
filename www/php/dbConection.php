<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'phpmyadmin');
define('DB_PASSWORD', 'tfm123');
define('DB_DATABASE', 'tfm');

$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if (!$db) {
	$response = "Connection failed";
	die("Connection failed: " . mysqli_connect_error());
}

$encryption_key = "12345tfmblue6789";

?>
