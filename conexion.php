<?php

$host = getenv("PGHOST");
$port = getenv("PGPORT");
$dbname = getenv("PGDATABASE");
$user = getenv("PGUSER");
$password = getenv("PGPASSWORD");

echo "<pre>";

echo "HOST: ";
var_dump($host);

echo "PORT: ";
var_dump($port);

echo "DATABASE: ";
var_dump($dbname);

echo "USER: ";
var_dump($user);

echo "PASSWORD: ";
var_dump($password);

echo "</pre>";

die();

?>