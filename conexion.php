<?php

$host = getenv("PGHOST");
$port = getenv("PGPORT");
$dbname = getenv("PGDATABASE");
$user = getenv("PGUSER");
$password = getenv("PGPASSWORD");

$conn_string = "
    host=$host
    port=$port
    dbname=$dbname
    user=$user
    password=$password
";

$conn = pg_connect($conn_string);

if (!$conn) {

    die("Error de conexión con PostgreSQL");

}

?>