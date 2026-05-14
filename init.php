<?php

include('conexion.php');

$sql = file_get_contents('database.sql');

$resultado = pg_query($conn, $sql);

if (!$resultado) {
    die("Error creando tablas");
}