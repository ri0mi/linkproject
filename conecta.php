<?php


function conecta() {
    $host = "localhost";
    $port = "5432";
    $dbname = "link"; #Entre comillas pon el nombre de tu base de datos
    $user = "postgres";
    $password = "Flor"; #Pon la contraseña que pones cuando entras al shell de postgres

    $conexion = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

    return $conexion;
}


?>