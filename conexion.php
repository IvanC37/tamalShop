<?php

$host = "localhost";
$port = "5432";
$dbname = "tamalshop";
$user = "postgres";
$password = "1192";

$conn = pg_connect("
    host=$host
    port=$port
    dbname=$dbname
    user=$user
    password=$password
");

if (!$conn) {
    die("Error de conexión con PostgreSQL");
}

?>