<?php

$host = getenv('PGHOST');
$port = getenv('PGPORT');
$dbname = getenv('PGDATABASE');
$user = getenv('PGUSER');
$password = getenv('PGPASSWORD');

if (
    empty($host) ||
    empty($port) ||
    empty($dbname) ||
    empty($user) ||
    empty($password)
) {

    die('Las variables PostgreSQL no están configuradas.');

}

$conn = pg_connect(
    "host=$host port=$port dbname=$dbname user=$user password=$password"
);

if (!$conn) {

    die('No se pudo conectar a PostgreSQL.');

}

?>